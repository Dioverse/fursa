import { ref, watch, onMounted } from 'vue'
import { useLanguageStore } from '@/stores/language'

/**
 * useLanguageContent
 * Fetches localized content bundle for a given name (e.g., 'header', 'home', 'about').
 * Refetches automatically when the selected language changes.
 */
export function useLanguageContent(name, { immediate = true } = {}) {
  const languageStore = useLanguageStore()
  const content = ref({})
  const loading = ref(false)
  const error = ref(null)

  const load = async () => {
    loading.value = true
    error.value = null
    try {
      const data = await languageStore.getContent(name)
      // Backend may return nested by name or flat map
      content.value = data?.[name] || data || {}
    } catch (e) {
      error.value = e?.response?.data?.message || e?.message || 'Failed to load language content'
    } finally {
      loading.value = false
    }
  }

  if (immediate) {
    onMounted(load)
  }

  watch(
    () => languageStore.current,
    () => {
      load()
    },
  )

  return { content, loading, error, reload: load }
}

export default useLanguageContent
