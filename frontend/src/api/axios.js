import axios from 'axios'
import { Notify } from 'quasar'

const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1/'
})

api.interceptors.request.use(config => {
  const user = JSON.parse(localStorage.getItem('user'))
  if (user && user.token) {
    config.headers.Authorization = `Bearer ${user.token}`
  }
  return config
}, error => {
  return Promise.reject(error)
})

api.interceptors.response.use(response => {
  return response
}, error => {
  if (error.response && error.response.status === 401) {
    localStorage.removeItem('user')
    window.location.href = '/login'
  }
  
  // Do not show notification for 404 (often means empty search results in this API)
  if (error.response && error.response.status !== 404) {
    const message = error.response?.data?.message || 'An error occurred'
    Notify.create({
      type: 'negative',
      message: message
    })
  }
  
  return Promise.reject(error)
})

export default api
