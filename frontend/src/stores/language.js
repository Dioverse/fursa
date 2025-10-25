import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import i18n from '@/plugins/i18n'

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
      // Switch vue-i18n locale without reloading the page
      i18n.global.locale.value = lang
    }
  }

  // Optional: expose current i18n messages for debugging or advanced use
  function getMessages() {
    return i18n.global.messages.value[current.value] || {}
  }

  return {
    current,
    currentLanguage,
    loading,
    error,
    allowedLanguages,
    set,
    getMessages,
  }
})
