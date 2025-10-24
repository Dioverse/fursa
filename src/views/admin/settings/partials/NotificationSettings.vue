<template>
  <div class="bg-white shadow rounded-lg p-6 space-y-6">
    <div>
      <h2 class="text-lg font-semibold text-gray-900">Notification Settings</h2>
      <p class="text-sm text-gray-500">Control email delivery and templates.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <!-- Method -->
      <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Email Method</label>
        <select v-model="form.email_method" class="input">
          <option value="php">PHP Mail</option>
          <option value="smtp">SMTP</option>
          <option value="sendgrid">SendGrid</option>
          <option value="mailjet">Mailjet</option>
        </select>
      </div>

      <!-- Test recipient -->
      <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Test recipient email</label>
        <input v-model="form.test_email" type="email" class="input" placeholder="you@example.com" />
      </div>

      <!-- SMTP fields -->
      <template v-if="form.email_method === 'smtp'">
        <div class="space-y-3">
          <label class="block text-sm font-medium text-gray-700">SMTP host</label>
          <input v-model="form.host" type="text" class="input" placeholder="smtp.mailtrap.io" />
        </div>
        <div class="space-y-3">
          <label class="block text-sm font-medium text-gray-700">SMTP port</label>
          <input v-model="form.port" type="text" class="input" placeholder="587" />
        </div>
        <div class="space-y-3">
          <label class="block text-sm font-medium text-gray-700">Encryption</label>
          <select v-model="form.enc" class="input">
            <option value="tls">TLS</option>
            <option value="ssl">SSL</option>
          </select>
        </div>
        <div class="space-y-3">
          <label class="block text-sm font-medium text-gray-700">Username</label>
          <input v-model="form.username" type="text" class="input" />
        </div>
        <div class="space-y-3 md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input v-model="form.password" type="password" class="input" />
        </div>
      </template>

      <!-- SendGrid -->
      <template v-else-if="form.email_method === 'sendgrid'">
        <div class="space-y-3 md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">SendGrid API Key</label>
          <input v-model="form.appkey" type="text" class="input" />
        </div>
      </template>

      <!-- Mailjet -->
      <template v-else-if="form.email_method === 'mailjet'">
        <div class="space-y-3">
          <label class="block text-sm font-medium text-gray-700">Public Key</label>
          <input v-model="form.public_key" type="text" class="input" />
        </div>
        <div class="space-y-3">
          <label class="block text-sm font-medium text-gray-700">Secret Key</label>
          <input v-model="form.secret_key" type="text" class="input" />
        </div>
      </template>
    </div>

    <div class="flex items-center justify-end gap-3">
      <button type="button" class="btn-outline" @click="testEmail" :disabled="testing">{{ testing ? 'Sending…' : 'Send test' }}</button>
      <button type="button" class="btn-primary" :disabled="saving" @click="save">{{ saving ? 'Saving…' : 'Save changes' }}</button>
    </div>
  </div>

  <!-- Templates management -->
  <NotificationTemplates />
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import { useNotificationsAdminStore } from '@/stores/notificationsAdmin'
import NotificationTemplates from './NotificationTemplates.vue'

const store = useNotificationsAdminStore()
const form = reactive({
  email_method: 'php',
  host: '', port: '', enc: 'tls', username: '', password: '',
  appkey: '', public_key: '', secret_key: '',
  test_email: '',
})
const saving = store.emailSaving
const testing = store.emailTesting
const notify = useNotificationStore()

onMounted(async () => {
  try {
    const cfg = await store.fetchEmailConfig()
    form.email_method = cfg.name || cfg.email_method || 'php'
    form.host = cfg.host || ''
    form.port = cfg.port || ''
    form.enc = cfg.enc || 'tls'
    form.username = cfg.username || ''
    form.password = ''
    form.appkey = cfg.appkey || ''
    form.public_key = cfg.public_key || ''
    form.secret_key = ''
  } catch (e) {
    console.debug('Notification preload skipped', e?.message)
  }
})

const save = async () => {
  try {
    const payload = { email_method: form.email_method }
    if (form.email_method === 'smtp') {
      Object.assign(payload, { host: form.host, port: form.port, enc: form.enc, username: form.username, password: form.password })
    } else if (form.email_method === 'sendgrid') {
      Object.assign(payload, { appkey: form.appkey })
    } else if (form.email_method === 'mailjet') {
      Object.assign(payload, { public_key: form.public_key, secret_key: form.secret_key })
    }
    await store.updateEmailConfig(payload)
    notify.success('Email settings saved')
  } catch {
    notify.error('Failed to save email settings')
  }
}

const testEmail = async () => {
  const to = form.test_email?.trim()
  if (!to) {
    notify.error('Enter a test recipient email')
    return
  }
  try {
    await store.testEmail(to)
    notify.success('Test email sent')
  } catch {
    notify.error('Failed to send test email')
  }
}
</script>

<style scoped>
.input { width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.5rem 0.75rem; }
.btn-primary { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.375rem; color: #fff; background: #2563eb; }
.btn-outline { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.375rem; border: 1px solid #d1d5db; color: #374151; background: #fff; }
</style>

