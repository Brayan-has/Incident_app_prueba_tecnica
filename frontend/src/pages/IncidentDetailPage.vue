<template>
  <q-page class="q-pa-md bg-grey-2">
    <div class="row q-col-gutter-md justify-center">
      <div class="col-12 col-md-8">
        <q-btn flat icon="arrow_back" label="Back to Dashboard" color="primary" @click="$router.push('/')" class="q-mb-md" />
        
        <q-card v-if="incident" class="incident-card">
          <q-card-section class="bg-primary text-white row items-center">
            <div class="text-h6">Incident Details #{{ incident.id }}</div>
            <q-space />
            <div class="q-gutter-x-sm">
              <q-chip :color="getPriorityColor(incident.priority)" outline text-white>
                {{ incident.priority }}
              </q-chip>
              <q-chip :color="getStatusColor(incident.status)" text-white>
                {{ incident.status }}
              </q-chip>
            </div>
          </q-card-section>

          <q-card-section class="q-pa-lg">
            <div class="row q-col-gutter-md">
              <div class="col-12">
                <div class="text-subtitle2 text-grey-7 uppercase">Title</div>
                <div class="text-h5 text-weight-bold">{{ incident.title || 'Untitled Incident' }}</div>
              </div>

              <div class="col-12">
                <div class="text-subtitle2 text-grey-7 uppercase">Description</div>
                <div class="text-body1 q-mt-sm">{{ incident.description || 'No description provided.' }}</div>
              </div>

              <div class="col-12 col-md-4">
                <div class="text-subtitle2 text-grey-7 uppercase">Created At</div>
                <div class="text-body1">{{ formatDate(incident.created_at) }}</div>
              </div>

              <div class="col-12 col-md-4">
                <div class="text-subtitle2 text-grey-7 uppercase">Expiration Date</div>
                <div class="text-body1 text-negative text-weight-bold">{{ formatDate(incident.expiration_date) }}</div>
              </div>

              <div class="col-12 col-md-4">
                <div class="text-subtitle2 text-grey-7 uppercase">Priority</div>
                <div class="text-body1">{{ incident.priority || 'N/A' }}</div>
              </div>

              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-grey-7 uppercase">Created By</div>
                <div class="text-h6 text-primary">{{ incident.created_by?.name || 'N/A' }}</div>
                <div class="text-caption text-grey">{{ incident.created_by?.email }}</div>
              </div>

              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-grey-7 uppercase">Assigned To</div>
                <div class="text-h6 text-secondary">{{ incident.assigned_to?.name || 'N/A' }}</div>
                <div class="text-caption text-grey">{{ incident.assigned_to?.email }}</div>
              </div>
            </div>
          </q-card-section>

          <q-separator />

          <q-card-actions align="right" class="q-pa-md">
            <q-btn flat color="primary" icon="edit" label="Edit" @click="onEdit" />
            <q-btn flat color="negative" icon="delete" label="Delete" @click="onDelete" />
          </q-card-actions>
        </q-card>

        <q-card v-else-if="loading" class="q-pa-xl text-center">
          <q-spinner color="primary" size="3em" />
          <div class="q-mt-md">Loading incident details...</div>
        </q-card>

        <q-card v-else class="q-pa-xl text-center">
          <q-icon name="error" color="negative" size="4em" />
          <div class="text-h6 q-mt-md">Incident not found</div>
        </q-card>
      </div>
    </div>

    <!-- Edit Dialog -->
    <q-dialog v-model="editDialog" persistent>
      <q-card style="min-width: 500px; border-radius: 12px;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Edit Incident</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <q-form @submit="updateIncident" class="q-gutter-md">
            <q-input filled v-model="form.title" label="Title" :rules="[val => !!val || 'Required']" />
            <q-input filled v-model="form.description" label="Description" type="textarea" :rules="[val => !!val || 'Required']" />
            
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-6">
                <q-select 
                  filled 
                  v-model="form.status" 
                  :options="['pending', 'in_progress', 'resolved', 'closed', 'expired']" 
                  label="Status" 
                />
              </div>
              <div class="col-12 col-md-6">
                <q-select 
                  filled 
                  v-model="form.priority" 
                  :options="['low', 'medium', 'high', 'critical']" 
                  label="Priority" 
                />
              </div>
            </div>

            <q-input filled v-model="form.expiration_date" label="Expiration Date" type="date" stack-label />

            <div class="flex justify-end q-mt-lg">
              <q-btn label="Cancel" flat v-close-popup />
              <q-btn label="Update" type="submit" color="primary" unelevated :loading="saving" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { IncidentService } from '../api/services'
import { useQuasar } from 'quasar'

const props = defineProps({
  id: { type: String, required: true }
})

const router = useRouter()
const $q = useQuasar()
const incident = ref(null)
const loading = ref(true)
const editDialog = ref(false)
const saving = ref(false)

const form = reactive({
  title: '',
  description: '',
  status: '',
  priority: '',
  expiration_date: ''
})

const fetchDetail = async () => {
  loading.value = true
  try {
    const response = await IncidentService.getById(props.id)
    // Handle Laravel controller response structure: { incident: {...}, message: "..." }
    const data = response.data.incident || response.data.data || response.data
    incident.value = data
    
    // Sync form
    form.title = data.title
    form.description = data.description
    form.status = data.status
    form.priority = data.priority
    form.expiration_date = data.expiration_date ? data.expiration_date.split('T')[0] : ''
  } catch (error) {
    console.error('Error fetching detail:', error)
  } finally {
    loading.value = false
  }
}

const onEdit = () => {
  editDialog.value = true
}

const updateIncident = async () => {
  saving.value = true
  try {
    await IncidentService.update(props.id, form)
    $q.notify({ color: 'positive', message: 'Updated successfully' })
    editDialog.value = false
    fetchDetail()
  } catch (error) {
    console.error('Update error:', error)
  } finally {
    saving.value = false
  }
}

const onDelete = () => {
  $q.dialog({
    title: 'Confirm Delete',
    message: 'Are you sure?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await IncidentService.delete(props.id)
      $q.notify({ color: 'positive', message: 'Deleted successfully' })
      router.push('/')
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

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

onMounted(fetchDetail)
</script>

<style scoped>
.incident-card {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.uppercase {
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.75rem;
}
</style>
