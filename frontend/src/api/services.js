import api from './axios'

export const AuthService = {
  login: (credentials) => api.post('auth/login', credentials),
  logout: () => api.post('logout')
}

export const UserService = {
  getAll: (page = 1, search = '', trashed = '') => api.get(`users?page=${page}&search=${search}&trashed=${trashed}`),
  getById: (id) => api.get(`users/${id}`),
  create: (data) => api.post('users', data),
  update: (id, data) => api.put(`users/${id}`, data),
  delete: (id) => api.delete(`users/${id}`),
  restore: (id) => api.post(`users/${id}/restore`),
  forceDelete: (id) => api.delete(`users/${id}/force-delete`)
}

export const RoleService = {
  getAll: () => api.get('roles'),
  getMyRole: () => api.get('roles/me')
}

export const PermissionService = {
  getAll: () => api.get('permissions')
}

export const IncidentService = {
  getDashboard: () => api.get('incidents/dashboard'),
  getAll: (page = 1, search = '', trashed = '') => api.get(`incidents?page=${page}&search=${search}&trashed=${trashed}`),
  getByStatus: (status) => api.get(`incidents/status/${status}`),
  getExpired: (page = 1) => api.get(`incidents/expired?page=${page}`),
  getById: (id) => api.get(`incidents/${id}`),
  create: (data) => api.post('incidents', data),
  update: (id, data) => api.put(`incidents/${id}`, data),
  delete: (id) => api.delete(`incidents/${id}`),
  restore: (id) => api.post(`incidents/${id}/restore`),
  forceDelete: (id) => api.delete(`incidents/${id}/force-delete`)
}
