import api from './api'

const languageService = {
  async getContent(name, lang) {
    return api.get(`/lang/fetch/${lang}/${name}`)
  }
}

export default languageService
