<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          Incident Manager
        </q-toolbar-title>

        <div class="q-gutter-sm row items-center no-wrap">
          <div class="text-subtitle2 q-mr-sm text-weight-bold">
            {{ auth.user?.user?.name || auth.user?.name }}
          </div>
          <q-chip
            v-for="role in userRoles"
            :key="role.id"
            color="white"
            text-color="primary"
            dense
            class="text-weight-bold"
          >
            {{ role.name }}
          </q-chip>
          <q-btn flat round dense icon="logout" @click="confirmLogout">
            <q-tooltip>Logout</q-tooltip>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-grey-1"
    >
      <q-list>
        <q-item-label header>
          Navigation
        </q-item-label>

        <q-item clickable v-ripple to="/">
          <q-item-section avatar>
            <q-icon name="dashboard" />
          </q-item-section>
          <q-item-section>
            Dashboard
          </q-item-section>
        </q-item>

        <q-item clickable v-ripple to="/incidents">
          <q-item-section avatar>
            <q-icon name="list" />
          </q-item-section>
          <q-item-section>
            Incidents
          </q-item-section>
        </q-item>

        <q-item clickable v-ripple to="/users" v-if="auth.can('view-user')">
          <q-item-section avatar>
            <q-icon name="people" />
          </q-item-section>
          <q-item-section>
            Users
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useQuasar } from 'quasar'
import { RoleService } from '../api/services'

const leftDrawerOpen = ref(false)
const userRoles = ref([])
const router = useRouter()
const auth = useAuthStore()
const $q = useQuasar()

const fetchUserRoles = async () => {
  try {
    const response = await RoleService.getMyRole()
    const roles = response.data.data || []
    userRoles.value = roles
    // Sync roles with auth store to enable permission checks
    auth.setUser({ roles: roles })
  } catch (error) {
    console.error('Error fetching my role:', error)
  }
}

onMounted(fetchUserRoles)

function toggleLeftDrawer () {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function confirmLogout () {
  $q.dialog({
    title: 'Logout',
    message: 'Are you sure you want to logout?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await auth.logout()
      router.push('/login')
    } catch (e) {
      window.location.href = '/login'
    }
  })
}
</script>
