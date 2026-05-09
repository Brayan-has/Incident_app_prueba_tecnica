import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/login',
    component: () => import('../pages/LoginPage.vue'),
    meta: { guest: true }
  },
  {
    path: '/',
    component: () => import('../layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import('../pages/DashboardPage.vue')
      },
      {
        path: 'incidents',
        name: 'incidents',
        component: () => import('../pages/IncidentsPage.vue')
      },
      {
        path: 'incidents/:id',
        name: 'incident-detail',
        component: () => import('../pages/IncidentDetailPage.vue'),
        props: true
      },
      {
        path: 'users',
        name: 'users',
        component: () => import('../pages/UsersPage.vue')
      }
    ],
    meta: { requiresAuth: true }
  },
  // Catch all
  {
    path: '/:catchAll(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && auth.isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router
