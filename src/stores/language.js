import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import languageService from '@/services/language.service'

const allowedLanguages = [
  { code: 'en', name: 'English' , icon:'en.png' },
  { code: 'fr', name: 'French' , icon:'fr.png' },
  { code: 'de', name: 'German' , icon:'de.png' },
  { code: 'ar', name: 'Arabic' , icon:'ar.png' },
  { code: 'es', name: 'Spanish' , icon:'es.png' },
  { code: 'zh', name: 'Chinese' , icon:'zh.png' },
  { code: 'ru', name: 'Russian' , icon:'ru.png' },
]

function initLanguage() {
  let lang = localStorage.getItem('language')
  const validCodes = allowedLanguages.map((l) => l.code)
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

  const currentLanguage = computed(() => {
    return {
      name: allowedLanguages.find((l) => l.code === current.value)?.name || 'English',
      icon: allowedLanguages.find((l) => l.code === current.value)?.icon
    }
  })

  function set(lang) {
    const validCodes = allowedLanguages.map((l) => l.code)
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
    currentLanguage,
    loading,
    error,
    allowedLanguages,
    set,
    getContent,
  }
})
