import { defineStore } from 'pinia'
import { ref, reactive } from 'vue'
import api from '@/services/api'

export const useNotificationsAdminStore = defineStore('notificationsAdmin', () => {
  // Email config
  const emailConfig = reactive({
    email_method: 'php',
    host: '', port: '', enc: 'tls', username: '', password: '',
    appkey: '', public_key: '', secret_key: ''
  })
  const emailLoading = ref(false)
  const emailSaving = ref(false)
  const emailTesting = ref(false)

  const fetchEmailConfig = async () => {
    emailLoading.value = true
    try {
      const { data } = await api({ method: 'get', url: '/admin/email/setting' })
      const cfg = data?.data || data || {}
      emailConfig.email_method = cfg.name || cfg.email_method || 'php'
      emailConfig.host = cfg.host || ''
      emailConfig.port = cfg.port || ''
      emailConfig.enc = cfg.enc || 'tls'
      emailConfig.username = cfg.username || ''
      emailConfig.password = ''
      emailConfig.appkey = cfg.appkey || ''
      emailConfig.public_key = cfg.public_key || ''
      emailConfig.secret_key = ''
      return cfg
    } finally {
      emailLoading.value = false
    }
  }

  const updateEmailConfig = async (payload) => {
    emailSaving.value = true
    try {
      await api({ method: 'post', url: '/admin/email/setting-update', data: payload })
    } finally {
      emailSaving.value = false
    }
  }

  const testEmail = async (email) => {
    emailTesting.value = true
    try {
      await api({ method: 'post', url: '/admin/settings/notifications/email/test', data: { email } })
    } finally {
      emailTesting.value = false
    }
  }

  // Templates
  const templates = ref([])
  const templatesLoading = ref(false)
  const templatesSaving = ref(false)

  const fetchTemplates = async () => {
    templatesLoading.value = true
    try {
      const { data } = await api({ method: 'get', url: '/admin/email/templates' })
      templates.value = data?.data || data || []
      return templates.value
    } finally {
      templatesLoading.value = false
    }
  }

  const updateTemplate = async (id, payload) => {
    templatesSaving.value = true
    try {
      await api({ method: 'post', url: `/admin/email/template-update/${id}` , data: payload })
      const idx = templates.value.findIndex(t => t.id === id)
      if (idx !== -1) templates.value[idx] = { ...templates.value[idx], ...payload }
    } finally {
      templatesSaving.value = false
    }
  }

  return {
    // email
    emailConfig, emailLoading, emailSaving, emailTesting,
    fetchEmailConfig, updateEmailConfig, testEmail,
    // templates
    templates, templatesLoading, templatesSaving,
    fetchTemplates, updateTemplate,
  }
})
