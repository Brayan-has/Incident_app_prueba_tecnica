import { defineStore } from 'pinia'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    isAuthenticated: !!localStorage.getItem('user')
  }),
  getters: {
    can: (state) => (permission) => {
      const userObj = state.user?.user || state.user
      if (!userObj) return false
      
      const permissions = userObj.permissions || []
      const roles = userObj.roles || []
      
      // DEBUG: Para ver qué llega del servidor
      // console.log('[AUTH DEBUG] User object:', userObj)
      // console.log('[AUTH DEBUG] User roles:', roles)
      
      // Check if user is admin (by name or by role object)
      const isAdmin = roles.some(r => (typeof r === 'string' ? r === 'admin' : r.name === 'admin'))
      if (isAdmin) return true
      
      return permissions.some(p => (typeof p === 'string' ? p === permission : p.name === permission))
    },
    isAdmin: (state) => {
      const userObj = state.user?.user || state.user
      if (!userObj) return false
      const roles = userObj.roles || []
      return roles.some(r => (typeof r === 'string' ? r === 'admin' : r.name === 'admin'))
    }
  },
  actions: {
    async login(credentials) {
      try {
        const response = await api.post('auth/login', credentials)
        const userData = response.data
        this.user = userData
        this.isAuthenticated = true
        localStorage.setItem('user', JSON.stringify(userData))
        return response
      } catch (error) {
        throw error
      }
    },
    async logout() {
      try {
        await api.post('auth/logout')
      } catch (error) {
        console.error('Logout error', error)
      } finally {
        this.user = null
        this.isAuthenticated = false
        localStorage.removeItem('user')
      }
    },
    setUser(data) {
      if (this.user && this.user.user) {
        this.user.user = { ...this.user.user, ...data }
      } else if (this.user) {
        this.user = { ...this.user, ...data }
      }
      localStorage.setItem('user', JSON.stringify(this.user))
    }
  }
})
