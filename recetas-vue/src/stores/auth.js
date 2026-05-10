import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/lib/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') ?? 'null'))
  const token = ref(localStorage.getItem('token') ?? null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function register(data) {
    const response = await api.post('/auth/register', data)
    setSession(response.data)
  }

  async function login(data) {
    const response = await api.post('/auth/login', data)
    setSession(response.data)
  }

  async function logout() {
    await api.post('/auth/logout')
    clearSession()
  }

  async function fetchUser() {
    const response = await api.get('/auth/me')
    user.value = response.data
    localStorage.setItem('user', JSON.stringify(response.data))
  }

  function setSession(data) {
    token.value = data.token
    user.value = data.user
    localStorage.setItem('token', data.token)
    localStorage.setItem('user', JSON.stringify(data.user))
  }

  function clearSession() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  return { user, token, isAuthenticated, isAdmin, register, login, logout, fetchUser }
})
