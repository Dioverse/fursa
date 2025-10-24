import { defineStore } from 'pinia'
import { ref, reactive } from 'vue'
import api from '@/services/api'

export const useShippingAdminStore = defineStore('shippingAdmin', () => {
  const items = ref([])
  const page = ref(1)
  const perPage = ref(20)
  const total = ref(0)
  const lastPage = ref(1)
  const stats = ref(null)
  const filtersOpts = ref({})
  const loading = ref(false)
  const saving = ref(false)

  const filters = reactive({
    country: '', state: '', province: '', provider: '', status: '',
    cost_from: '', cost_to: '', date_from: '', date_to: '',
    sort_by: '', sort_order: '', per_page: 100,
  })

  const fetchList = async (params = {}) => {
    loading.value = true
    try {
      const query = { ...filters, ...params }
      const { data } = await api({ method: 'get', url: '/admin-shippings', params: query })
      // Laravel paginator structure
      const paged = data?.data || {}
      items.value = paged.data || []
      page.value = paged.current_page || 1
      perPage.value = paged.per_page || query.per_page || 20
      total.value = paged.total || (items.value?.length || 0)
      lastPage.value = paged.last_page || 1
      stats.value = data?.stats || null
      filtersOpts.value = data?.filters || {}
      return items.value
    } finally {
      loading.value = false
    }
  }

  // Fetch only filters/stats without replacing current items/pagination
  const fetchFilters = async (params = {}) => {
    try {
      const query = { ...filters, per_page: 1, ...params }
      const { data } = await api({ method: 'get', url: '/admin-shippings', params: query })
      filtersOpts.value = data?.filters || {}
      stats.value = data?.stats || stats.value
      return filtersOpts.value
  } catch {
      // swallow to avoid blocking form UX
      return filtersOpts.value
    }
  }

  const createRules = async (rules) => {
    saving.value = true
    try {
      await api({ method: 'post', url: '/admin-shippings', data: { rules } })
    } finally {
      saving.value = false
    }
  }

  const updateRule = async (id, payload) => {
    saving.value = true
    try {
      await api({ method: 'patch', url: `/admin-shippings/${id}`, data: payload })
    } finally {
      saving.value = false
    }
  }

  const bulkAction = async (action, ids) => {
    saving.value = true
    try {
      await api({ method: 'post', url: '/admin-shippings-bulk-action', data: { action, ids } })
    } finally {
      saving.value = false
    }
  }

  return {
    items, page, perPage, total, lastPage, stats, filtersOpts, loading, saving, filters,
  fetchList, fetchFilters, createRules, updateRule, bulkAction,
  }
})
