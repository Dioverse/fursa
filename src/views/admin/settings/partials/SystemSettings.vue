<template>
  <div class="bg-white shadow rounded-lg p-6 space-y-6">
    <div>
      <h2 class="text-lg font-semibold text-gray-900">System Settings</h2>
      <p class="text-sm text-gray-500">General platform configuration.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="space-y-3 md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Site name</label>
        <input v-model="form.site_name" type="text" class="input" placeholder="Fursa Energy LTD" />
      </div>

      <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Site logo</label>
        <div class="flex items-center gap-3">
          <input ref="logoInput" type="file" accept="image/*" class="hidden" @change="onLogoFileChange" />
          <button type="button" class="btn-outline" :disabled="uploadingLogo" @click="triggerLogoPicker">
            {{ uploadingLogo ? `Uploading ${uploadProgress}%` : 'Upload logo' }}
          </button>
          <span class="text-xs text-gray-500">PNG, JPG, SVG up to ~2MB</span>
        </div>
        <div class="text-xs text-gray-500">Or paste a public URL</div>
        <input v-model="form.site_logo" type="text" class="input" placeholder="https://.../logo.png" />
      </div>
      <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Tax (%)</label>
        <input v-model="form.tax" type="number" step="0.01" class="input" placeholder="7.5" />
      </div>

      <div class="space-y-3 md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Logo preview</label>
        <div class="flex items-center gap-3">
          <div class="h-12 w-12 rounded bg-gray-100 border flex items-center justify-center overflow-hidden">
            <img v-if="form.site_logo" :src="form.site_logo" alt="Logo" class="object-contain h-full w-full" />
            <span v-else class="text-xs text-gray-500">No logo</span>
          </div>
          <div class="text-xs text-gray-500">Paste a public URL to your logo image</div>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3">
      <button class="btn-outline" @click="reset">Reset</button>
      <button class="btn-primary" :disabled="saving" @click="save">{{ saving ? 'Saving…' : 'Save changes' }}</button>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import { useSiteStore } from '@/stores/site'
import { apiHelpers } from '@/services/api'

const form = reactive({ site_name: '', site_logo: '', tax: '' })
const saving = ref(false)
const notify = useNotificationStore()
const siteStore = useSiteStore()
const logoInput = ref(null)
const uploadingLogo = ref(false)
const uploadProgress = ref(0)

onMounted(async () => {
  try {
    const info = await siteStore.fetchSiteInfo()
    form.site_name = info.site_name || ''
    form.site_logo = info.site_logo || ''
    form.tax = info.tax ?? ''
  } catch (e) {
    console.debug('System preload skipped', e?.message)
  }
})

const reset = () => {
  form.site_name = siteStore.name || ''
  form.site_logo = siteStore.logo || ''
  form.tax = siteStore.taxNumber ?? ''
}

const save = async () => {
  try {
    saving.value = true
    const tasks = []

    // Compare and update only changed fields
    if ((form.site_name || '') !== (siteStore.name || '')) {
      tasks.push(siteStore.updateSiteName(form.site_name))
    }
    // For logo, if changed, post URL too (file upload path already handled upload)
    if ((form.site_logo || '') !== (siteStore.logo || '')) {
      tasks.push(siteStore.updateSiteLogoUrl(form.site_logo))
    }
    const currentTax = siteStore.taxNumber ?? ''
    const nextTax = form.tax === '' || form.tax === null ? '' : form.tax
    if (`${nextTax}` !== `${currentTax ?? ''}`) {
      tasks.push(siteStore.updateSiteTax(form.tax))
    }

    await Promise.all(tasks)
    // Refresh to ensure store is fully in sync with backend formats
    await siteStore.fetchSiteInfo()
    notify.success('System settings saved')
  } catch {
    notify.error('Failed to save system settings')
  } finally {
    saving.value = false
  }
}

const triggerLogoPicker = () => {
  logoInput.value?.click()
}

const onLogoFileChange = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  uploadingLogo.value = true
  uploadProgress.value = 0
  try {
  const res = await apiHelpers.uploadFile('/admin/site/logo', file, (p) => (uploadProgress.value = p), 'put')
    const body = res?.data?.data || res?.data || {}
    const url = body.url || body.path || body.location || body.file || body.site_logo
    if (url) {
      form.site_logo = url
      notify.success('Logo uploaded')
      // Keep store in sync so sidebar/header reflect immediately
  try { await siteStore.fetchSiteInfo() } catch { /* no-op sync */ }
    } else {
      notify.error('Upload succeeded but no URL returned')
    }
  } catch (err) {
    console.error('Logo upload failed', err)
    notify.error('Failed to upload logo')
  } finally {
    uploadingLogo.value = false
    uploadProgress.value = 0
    if (logoInput.value) logoInput.value.value = ''
  }
}
</script>

<style scoped>
.input { width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.5rem 0.75rem; }
.btn-primary { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.375rem; color: #fff; background: #b8974f; }
.btn-outline { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.375rem; border: 1px solid #d1d5db; color: #374151; background: #fff; }
</style>
