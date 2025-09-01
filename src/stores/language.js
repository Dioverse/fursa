import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import languageService from '@/services/language.service'

const allowedLanguages = [
  { code: 'en', name: 'English' },
  { code: 'fr', name: 'French' },
  { code: 'de', name: 'German' },
  { code: 'ar', name: 'Arabic' },
  { code: 'es', name: 'Spanish' },
  { code: 'zh', name: 'Chinese' },
  { code: 'ru', name: 'Russian' }
]

function initLanguage() {
  let lang = localStorage.getItem('language')
  const validCodes = allowedLanguages.map(l => l.code)
  if (!lang || !validCodes.includes(lang)) {
    lang = 'en'
    localStorage.setItem('language', lang)
  }
  return lang
}

export const useLanguageStore = defineStore('language', () => {
  const current = ref(initLanguage())
  const loading = ref(false)
  const error = ref(null)

  const currentName = computed(() => {
    return allowedLanguages.find(l => l.code === current.value)?.name || 'English'
  })

  function set(lang) {
    const validCodes = allowedLanguages.map(l => l.code)
    if (validCodes.includes(lang)) {
      current.value = lang
      localStorage.setItem('language', lang)
      window.location.reload()
    }
  }

  async function getContent(name) {
    loading.value = true
    error.value = null
    try {
      const response = await languageService.getContent(name, current.value)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch language content'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    current,
    currentName,
    loading,
    error,
    allowedLanguages,
    set,
    getContent
  }
})
