import { createI18n } from 'vue-i18n'
import en from '@/locales/en.json'
import fr from '@/locales/fr.json'
import de from '@/locales/de.json'
import es from '@/locales/es.json'
import zh from '@/locales/zh.json'
import ru from '@/locales/ru.json'
import ar from '@/locales/ar.json'

const messages = {
  en,
  fr,
  de,
  es,
  zh,
  ru,
  ar,
}

export const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('language') || 'en',
  fallbackLocale: 'en',
  messages,
  globalInjection: true,
})

export default i18n
