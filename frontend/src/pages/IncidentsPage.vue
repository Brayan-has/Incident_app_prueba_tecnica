<template>
  <q-page class="q-pa-md bg-grey-2">
    <div class="row q-col-gutter-md">
      <div class="col-12 flex justify-between items-center">
        <h1 class="text-h4 text-weight-bold q-my-md text-grey-9">Incidents List</h1>
        <q-btn v-if="auth.can('create-incident')" color="primary" icon="add" label="New Incident" @click="openDialog()" unelevated />
      </div>

      <!-- Incident List Table -->
      <div class="col-12">
        <q-card flat bordered class="incident-table-card">
          <q-table
            :rows="incidents"
            :columns="columns"
            row-key="id"
            :loading="loading"
            flat
            v-model:pagination="pagination"
            @request="onRequest"
            :rows-per-page-options="[]"
          >
            <template v-slot:top-right>
              <div class="row q-gutter-sm items-center">
                <q-select
                  v-model="trashedFilter"
                  :options="trashedOptions"
                  label="Status"
                  outlined
                  dense
                  emit-value
                  map-options
                  style="min-width: 150px"
                  @update:model-value="fetchIncidents(1)"
                />
                <q-input 
                  outlined 
                  dense 
                  debounce="300" 
                  v-model="filter" 
                  placeholder="Search incidents..."
                  class="search-input"
                  clearable
                >
                  <template v-slot:append>
                    <q-icon name="search" />
                  </template>
                </q-input>
              </div>
            </template>

            <template v-slot:body-cell-status="props">
              <q-td :props="props">
                <q-chip v-if="props.row.deleted_at" color="grey-7" text-white dense>
                  ARCHIVED
                </q-chip>
                <q-chip v-else :color="getStatusColor(props.value)" text-white dense>
                  {{ props.value }}
                </q-chip>
              </q-td>
            </template>

            <template v-slot:body-cell-priority="props">
              <q-td :props="props">
                <q-chip :color="getPriorityColor(props.value)" outline dense>
                  {{ props.value }}
                </q-chip>
              </q-td>
            </template>

            <template v-slot:body-cell-actions="props">
              <q-td :props="props" class="q-gutter-x-sm">
                <q-btn flat round color="primary" icon="visibility" @click="$router.push(`/incidents/${props.row.id}`)">
                  <q-tooltip>View Details</q-tooltip>
                </q-btn>
                <q-btn v-if="auth.can('edit-incident')" flat round color="orange" icon="edit" @click="openDialog(props.row)">
                  <q-tooltip>Edit</q-tooltip>
                </q-btn>
                <q-btn v-if="auth.can('delete-incident')" flat round color="negative" icon="delete" @click="confirmDelete(props.row.id)">
                  <q-tooltip>Delete</q-tooltip>
                </q-btn>
              </q-td>
            </template>
          </q-table>
        </q-card>
      </div>
    </div>

    <!-- Create/Edit Dialog -->
    <q-dialog v-model="dialog.show" persistent>
      <q-card style="min-width: 500px; border-radius: 12px;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ dialog.editMode ? 'Edit Incident' : 'Create New Incident' }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <q-form @submit="saveIncident" class="q-gutter-md">
            <q-input
              filled
              v-model="form.title"
              label="Title"
              lazy-rules
              :rules="[ val => val && val.length > 0 || 'Required']"
            />
            
            <q-input
              filled
              v-model="form.description"
              label="Description"
              type="textarea"
              lazy-rules
              :rules="[ val => val && val.length > 0 || 'Required']"
            />

            <div class="row q-col-gutter-sm" v-if="dialog.editMode">
              <div class="col-12 col-md-6">
                <q-select
                  filled
                  v-model="form.status"
                  :options="statusOptions"
                  label="Status"
                  emit-value
                  map-options
                />
              </div>
              <div class="col-12 col-md-6">
                <q-select
                  filled
                  v-model="form.priority"
                  :options="priorityOptions"
                  label="Priority"
                  emit-value
                  map-options
                />
              </div>
            </div>

            <q-input filled v-model="form.expiration_date" label="Expiration Date" type="date" stack-label />

            <div class="flex justify-end q-mt-lg">
              <q-btn label="Cancel" flat v-close-popup color="grey" />
              <q-btn :label="dialog.editMode ? 'Update' : 'Create'" type="submit" color="primary" unelevated :loading="saving" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted, reactive, watch } from 'vue'
import { useRoute } from 'vue-router'
import { IncidentService } from '../api/services'
import { useQuasar } from 'quasar'

import { useAuthStore } from '../stores/auth'

const route = useRoute()
const $q = useQuasar()
const auth = useAuthStore()
const incidents = ref([])
const loading = ref(true)
const saving = ref(false)
const filter = ref(route.query.search || '')
const trashedFilter = ref('') // '' (all active), 'only' (archived), 'with' (all)
const trashedOptions = [
  { label: 'Active', value: '' },
  { label: 'Archived', value: 'only' },
  { label: 'All', value: 'with' }
]

const pagination = ref({
  sortBy: 'id',
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0
})

watch(filter, () => {
  fetchIncidents(1)
})

const columns = [
  { name: 'id', align: 'left', label: 'ID', field: 'id', sortable: true },
  { name: 'title', align: 'left', label: 'Title', field: 'title', sortable: true },
  { name: 'created_by', align: 'left', label: 'Created By', field: row => row.created_by?.name || 'N/A', sortable: true },
  { name: 'assigned_to', align: 'left', label: 'Assigned To', field: row => row.assigned_to?.name || 'N/A', sortable: true },
  { name: 'status', align: 'center', label: 'Status', field: 'status', sortable: true },
  { name: 'priority', align: 'center', label: 'Priority', field: 'priority', sortable: true },
  { name: 'expiration_date', align: 'left', label: 'Expires', field: 'expiration_date', sortable: true },
  { name: 'actions', align: 'right', label: 'Actions', field: 'actions' }
]

const statusOptions = [
  { label: 'Pending', value: 'pending' },
  { label: 'In Progress', value: 'in_progress' },
  { label: 'Resolved', value: 'resolved' },
  { label: 'Closed', value: 'closed' },
  { label: 'Expired', value: 'expired' }
]

const priorityOptions = [
  { label: 'Low', value: 'low' },
  { label: 'Medium', value: 'medium' },
  { label: 'High', value: 'high' },
  { label: 'Critical', value: 'critical' }
]

const dialog = reactive({
  show: false,
  editMode: false,
  currentId: null
})

const form = reactive({
  title: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  expiration_date: ''
})

const fetchIncidents = async (page = 1) => {
  loading.value = true
  try {
    let response
    const currentFilter = filter.value.toLowerCase()
    
    if (currentFilter === 'expired') {
      response = await IncidentService.getExpired(page)
    } else {
      response = await IncidentService.getAll(page, filter.value, trashedFilter.value)
    }
    
    const data = response.data
    incidents.value = data.data || []
    const meta = data.meta || data
    pagination.value.page = meta.current_page || 1
    pagination.value.rowsNumber = meta.total || incidents.value.length
    pagination.value.rowsPerPage = meta.per_page || 10
  } catch (error) {
    console.error('Error fetching incidents:', error)
    incidents.value = []
  } finally {
    loading.value = false
  }
}

const restoreIncident = async (id) => {
  try {
    await IncidentService.restore(id)
    $q.notify({ color: 'positive', message: 'Incident restored' })
    fetchIncidents(pagination.value.page)
  } catch (e) {}
}

const confirmForceDelete = (id) => {
  $q.dialog({
    title: 'PERMANENT DELETE',
    message: 'This action cannot be undone. Are you sure?',
    cancel: true,
    color: 'negative',
    persistent: true
  }).onOk(async () => {
    try {
      await IncidentService.forceDelete(id)
      $q.notify({ color: 'positive', message: 'Incident deleted permanently' })
      fetchIncidents(pagination.value.page)
    } catch (e) {}
  })
}

const onRequest = (props) => {
  const { page } = props.pagination
  fetchIncidents(page)
}

const openDialog = (incident = null) => {
  if (incident) {
    dialog.editMode = true
    dialog.currentId = incident.id
    form.title = incident.title
    form.description = incident.description
    form.status = incident.status
    form.priority = incident.priority
    form.expiration_date = incident.expiration_date ? incident.expiration_date.split('T')[0] : ''
  } else {
    dialog.editMode = false
    dialog.currentId = null
    form.title = ''
    form.description = ''
    form.status = 'pending'
    form.priority = 'medium'
    form.expiration_date = ''
  }
  dialog.show = true
}

const saveIncident = async () => {
  saving.value = true
  try {
    if (dialog.editMode) {
      await IncidentService.update(dialog.currentId, form)
      $q.notify({ color: 'positive', message: 'Incident updated successfully' })
    } else {
      await IncidentService.create(form)
      $q.notify({ color: 'positive', message: 'Incident created successfully' })
    }
    dialog.show = false
    fetchIncidents(pagination.value.page)
  } catch (error) {
    console.error('Save error:', error)
  } finally {
    saving.value = false
  }
}

const confirmDelete = (id) => {
  $q.dialog({
    title: 'Confirm Delete',
    message: 'Are you sure?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await IncidentService.delete(id)
      $q.notify({ color: 'positive', message: 'Incident deleted' })
      fetchIncidents(pagination.value.page)
    } catch (error) {
      console.error('Delete error:', error)
    }
  })
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'orange',
    in_progress: 'blue',
    resolved: 'green',
    closed: 'grey',
    expired: 'red'
  }
  return colors[status] || 'primary'
}

const getPriorityColor = (priority) => {
  const colors = {
    low: 'grey',
    medium: 'blue',
    high: 'orange',
    critical: 'red'
  }
  return colors[priority] || 'primary'
}

onMounted(() => {
  fetchIncidents(1)
})
</script>

<style scoped>
.incident-table-card {
  border-radius: 16px;
  overflow: hidden;
}
.search-input {
  min-width: 300px;
}
</style>
