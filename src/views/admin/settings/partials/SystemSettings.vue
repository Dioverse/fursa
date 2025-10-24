<template>
  <div class="bg-white shadow rounded-lg p-6 space-y-6">
    <div>
      <h2 class="text-lg font-semibold text-gray-900">System Settings</h2>
      <p class="text-sm text-gray-500">General platform configuration.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Site name</label>
        <input v-model="form.siteName" type="text" class="input" placeholder="Fursa Energy" />
      </div>
      <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Support email</label>
        <input v-model="form.supportEmail" type="email" class="input" placeholder="support@fursaenergy.com" />
      </div>
      <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Default currency</label>
        <input v-model="form.currency" type="text" class="input" placeholder="NGN" />
      </div>
      <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Timezone</label>
        <input v-model="form.timezone" type="text" class="input" placeholder="Africa/Lagos" />
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
import { apiHelpers as api } from '@/services/api'
import { useNotificationStore } from '@/stores/notification'

const form = reactive({ siteName: '', supportEmail: '', currency: 'NGN', timezone: 'Africa/Lagos' })
const saving = ref(false)
const notify = useNotificationStore()

onMounted(async () => {
  try {
    const { data } = await api.silent({ method: 'get', url: '/admin/settings/system' })
    Object.assign(form, data || {})
  } catch (e) {
    console.debug('System preload skipped', e?.message)
  }
})

const reset = () => {
  form.siteName = ''
  form.supportEmail = ''
  form.currency = 'NGN'
  form.timezone = 'Africa/Lagos'
}

const save = async () => {
  try {
    saving.value = true
    await api.silent({ method: 'put', url: '/admin/settings/system', data: form })
    notify.success('System settings saved')
  } catch {
    notify.error('Failed to save system settings')
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.input { width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.5rem 0.75rem; }
.btn-primary { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.375rem; color: #fff; background: #2563eb; }
.btn-outline { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.375rem; border: 1px solid #d1d5db; color: #374151; background: #fff; }
</style>
