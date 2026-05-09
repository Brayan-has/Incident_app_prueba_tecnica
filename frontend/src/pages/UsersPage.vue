<template>
  <q-page class="q-pa-md bg-grey-2">
    <div class="row q-col-gutter-md">
      <div class="col-12 flex justify-between items-center">
        <h1 class="text-h4 text-weight-bold q-my-md text-grey-9">User Management</h1>
        <q-btn v-if="auth.can('create-user')" color="primary" icon="add" label="New User" @click="openDialog()" unelevated />
      </div>

      <!-- Users Table -->
      <div class="col-12">
        <q-card flat bordered class="user-table-card">
          <q-table
            :rows="users"
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
                  @update:model-value="fetchUsers(1)"
                />
                <q-input 
                  outlined 
                  dense 
                  debounce="300" 
                  v-model="filter" 
                  placeholder="Search users..."
                  class="search-input"
                  clearable
                >
                  <template v-slot:append>
                    <q-icon name="search" />
                  </template>
                </q-input>
              </div>
            </template>

            <template v-slot:body-cell-name="props">
              <q-td :props="props">
                {{ props.value }}
                <q-chip v-if="props.row.deleted_at" color="grey-7" text-white dense size="sm" class="q-ml-sm">
                  ARCHIVED
                </q-chip>
              </q-td>
            </template>

            <template v-slot:body-cell-actions="props">
              <q-td :props="props" class="q-gutter-x-sm">
                <q-btn v-if="auth.can('edit-user') && !props.row.deleted_at" flat round color="orange" icon="edit" @click="openDialog(props.row)">
                  <q-tooltip>Edit User</q-tooltip>
                </q-btn>
                <q-btn v-if="auth.can('edit-user') && props.row.deleted_at" flat round color="green" icon="restore" @click="restoreUser(props.row.id)">
                  <q-tooltip>Restore User</q-tooltip>
                </q-btn>
                <q-btn v-if="auth.can('delete-user') && !props.row.deleted_at" flat round color="negative" icon="delete" @click="confirmDelete(props.row.id)">
                  <q-tooltip>Archive User</q-tooltip>
                </q-btn>
                <q-btn v-if="auth.can('delete-user') && props.row.deleted_at" flat round color="black" icon="delete_forever" @click="confirmForceDelete(props.row.id)">
                  <q-tooltip>Permanently Delete</q-tooltip>
                </q-btn>
              </q-td>
            </template>
          </q-table>
        </q-card>
      </div>
    </div>

    <!-- Create/Edit User Dialog -->
    <q-dialog v-model="dialog.show" persistent>
      <q-card style="min-width: 450px; border-radius: 12px;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ dialog.editMode ? 'Edit User' : 'Create New User' }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <q-form @submit="saveUser" class="q-gutter-md">
            <q-input
              filled
              v-model="form.name"
              label="Full Name"
              lazy-rules
              :rules="[ val => val && val.length > 0 || 'Required']"
            />
            
            <q-input
              filled
              v-model="form.email"
              label="Email Address"
              type="email"
              lazy-rules
              :rules="[ val => val && /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(val) || 'Invalid email']"
            />

            <q-input
              filled
              v-model="form.password"
              label="Password"
              type="password"
              :placeholder="dialog.editMode ? 'Leave blank to keep current' : ''"
              :rules="dialog.editMode ? [] : [ val => val && val.length >= 6 || 'Min 6 characters']"
            />

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
import { UserService } from '../api/services'
import { useAuthStore } from '../stores/auth'
import { useQuasar } from 'quasar'

const $q = useQuasar()
const auth = useAuthStore()
const users = ref([])
const loading = ref(true)
const saving = ref(false)
const filter = ref('')
const trashedFilter = ref('')
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

const columns = [
  { name: 'id', align: 'left', label: 'ID', field: 'id', sortable: true },
  { name: 'name', align: 'left', label: 'Name', field: 'name', sortable: true },
  { name: 'email', align: 'left', label: 'Email', field: 'email', sortable: true },
  { name: 'actions', align: 'right', label: 'Actions', field: 'actions' }
]

watch(filter, () => {
  fetchUsers(1)
})

const fetchUsers = async (page = 1) => {
  loading.value = true
  try {
    const response = await UserService.getAll(page, filter.value, trashedFilter.value)
    const data = response.data
    users.value = data.data || []
    const meta = data.meta || data
    pagination.value.page = meta.current_page || 1
    pagination.value.rowsNumber = meta.total || users.value.length
    pagination.value.rowsPerPage = meta.per_page || 10
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}

const restoreUser = async (id) => {
  try {
    await UserService.restore(id)
    $q.notify({ color: 'positive', message: 'User restored' })
    fetchUsers(pagination.value.page)
  } catch (e) {}
}

const confirmForceDelete = (id) => {
  $q.dialog({
    title: 'PERMANENT DELETE',
    message: 'This user will be permanently removed. Are you sure?',
    cancel: true,
    color: 'negative',
    persistent: true
  }).onOk(async () => {
    try {
      await UserService.forceDelete(id)
      $q.notify({ color: 'positive', message: 'User deleted permanently' })
      fetchUsers(pagination.value.page)
    } catch (e) {}
  })
}

const onRequest = (props) => {
  const { page } = props.pagination
  fetchUsers(page)
}

const dialog = reactive({
  show: false,
  editMode: false,
  currentId: null
})

const form = reactive({
  name: '',
  email: '',
  password: ''
})

const openDialog = (user = null) => {
  if (user) {
    dialog.editMode = true
    dialog.currentId = user.id
    form.name = user.name
    form.email = user.email
    form.password = '' // Don't show password
  } else {
    dialog.editMode = false
    dialog.currentId = null
    form.name = ''
    form.email = ''
    form.password = ''
  }
  dialog.show = true
}

const saveUser = async () => {
  saving.value = true
  try {
    const payload = { ...form }
    if (dialog.editMode && !payload.password) {
      delete payload.password // Don't send empty password on update
    }

    if (dialog.editMode) {
      await UserService.update(dialog.currentId, payload)
      $q.notify({ color: 'positive', message: 'User updated successfully' })
    } else {
      await UserService.create(payload)
      $q.notify({ color: 'positive', message: 'User created successfully' })
    }
    dialog.show = false
    fetchUsers(pagination.value.page)
  } catch (error) {
    console.error('Save user error:', error)
  } finally {
    saving.value = false
  }
}

const confirmDelete = (id) => {
  $q.dialog({
    title: 'Confirm Delete',
    message: 'Are you sure you want to delete this user?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await UserService.delete(id)
      $q.notify({ color: 'positive', message: 'User deleted' })
      fetchUsers(pagination.value.page)
    } catch (error) {
      console.error('Delete user error:', error)
    }
  })
}

onMounted(() => {
  fetchUsers(1)
})
</script>

<style scoped>
.user-table-card {
  border-radius: 16px;
  overflow: hidden;
}
.search-input {
  min-width: 300px;
}
</style>
