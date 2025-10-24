<template>
  <div class="bg-white shadow rounded-lg p-6 mt-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-base font-semibold text-gray-900">Notification Templates</h3>
        <p class="text-sm text-gray-500">Manage subjects, bodies, and channel toggles.</p>
      </div>
      <div class="relative">
        <input v-model="q" type="text" class="input" placeholder="Search by name or act..." />
      </div>
    </div>

    <div v-if="loading" class="text-sm text-gray-500">Loading templates…</div>
    <div v-else>
      <div v-if="filtered.length === 0" class="text-sm text-gray-500">No templates found.</div>
      <div v-else class="overflow-x-auto -mx-2">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="th">Name</th>
              <th class="th">Code</th>
              <th class="th">Email</th>
              <th class="th">SMS</th>
              <th class="th">Push</th>
              <th class="th text-right">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="t in filtered" :key="t.id">
              <td class="td">{{ t.name }}</td>
              <td class="td font-mono text-xs">{{ t.act }}</td>
              <td class="td"><span :class="badge(t.email_status)">{{ t.email_status ? 'On' : 'Off' }}</span></td>
              <td class="td"><span :class="badge(t.sms_status)">{{ t.sms_status ? 'On' : 'Off' }}</span></td>
              <td class="td"><span :class="badge(t.push_status)">{{ t.push_status ? 'On' : 'Off' }}</span></td>
              <td class="td text-right">
                <button class="btn-outline" @click="openEditor(t)">Edit</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Editor Modal -->
    <div v-if="editing" class="fixed inset-0 z-40 flex items-end sm:items-center justify-center bg-black/30">
      <div class="bg-white w-full sm:max-w-3xl max-h-[90vh] overflow-auto rounded-t-lg sm:rounded-lg shadow-lg">
        <div class="flex items-center justify-between border-b px-4 py-3">
          <div>
            <h4 class="font-semibold">Edit Template — {{ current?.name }}</h4>
            <p class="text-xs text-gray-500">Code: {{ current?.act }}</p>
          </div>
          <button class="btn-outline" @click="closeEditor">Close</button>
        </div>

        <div class="px-4 py-3 border-b">
          <div class="inline-flex rounded-md border p-1 bg-gray-50">
            <button :class="seg('email')" @click="channel='email'">Email</button>
            <button :class="seg('sms')" @click="channel='sms'">SMS</button>
            <button :class="seg('push')" @click="channel='push'">Push</button>
          </div>
        </div>

        <div class="p-4 grid gap-4 sm:grid-cols-3">
          <div class="sm:col-span-2 space-y-4">
            <!-- Email -->
            <template v-if="channel==='email'">
              <div>
                <label class="lbl">Subject</label>
                <input v-model="form.subject" type="text" class="input" />
              </div>
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="lbl">From name</label>
                  <input v-model="form.email_sent_from_name" type="text" class="input" />
                </div>
                <div>
                  <label class="lbl">From address</label>
                  <input v-model="form.email_sent_from_address" type="email" class="input" />
                </div>
              </div>
              <div>
                <div class="flex items-center justify-between">
                  <label class="lbl mb-0">Email body</label>
                  <button type="button" class="btn-outline" @click="htmlMode = !htmlMode">{{ htmlMode ? 'WYSIWYG' : 'Edit HTML' }}</button>
                </div>
                <p v-if="htmlMode" class="text-xs text-gray-500 mb-2">This template contains complex HTML (e.g., tables). Editing raw HTML is enabled for fidelity.</p>
                <div v-if="htmlMode">
                  <textarea v-model="form.email_body" class="textarea" rows="12"></textarea>
                </div>
                <div v-else>
                  <RichTextEditor :key="`rte-${current?.id}-${htmlMode ? 'h' : 'w'}`" v-model="form.email_body" />
                </div>
              </div>
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" v-model="form.email_status" /> Enable email
              </label>
            </template>

            <!-- SMS -->
            <template v-else-if="channel==='sms'">
              <div>
                <label class="lbl">Sender</label>
                <input v-model="form.sms_sent_from" type="text" class="input" />
              </div>
              <div>
                <label class="lbl">SMS body</label>
                <textarea v-model="form.sms_body" class="textarea" rows="8"></textarea>
              </div>
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" v-model="form.sms_status" /> Enable SMS
              </label>
            </template>

            <!-- Push -->
            <template v-else>
              <div>
                <label class="lbl">Title</label>
                <input v-model="form.push_title" type="text" class="input" />
              </div>
              <div>
                <label class="lbl">Body</label>
                <textarea v-model="form.push_body" class="textarea" rows="8"></textarea>
              </div>
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" v-model="form.push_status" /> Enable push
              </label>
            </template>
          </div>

          <div class="sm:col-span-1">
            <div class="p-3 rounded border bg-gray-50">
              <h5 class="font-medium text-sm mb-2">Shortcodes</h5>
              <div v-if="!current?.shortcodes" class="text-xs text-gray-500">None</div>
              <ul v-else class="space-y-2">
                <li v-for="(desc, key) in current.shortcodes" :key="key" class="text-xs">
                  <code class="px-1 py-0.5 bg-white border rounded" v-pre>{{ '{{' }} {{ key }} {{ '}}' }}</code>
                  <div class="text-gray-500">{{ desc }}</div>
                </li>
              </ul>
            </div>

            <div v-if="current?.loop_data" class="p-3 mt-3 rounded border bg-gray-50">
              <h5 class="font-medium text-sm mb-2">Loop data</h5>
              <pre class="text-[11px] whitespace-pre-wrap">{{ prettyLoop(current.loop_data) }}</pre>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end gap-3 px-4 py-3 border-t">
          <button type="button" class="btn-outline" @click="closeEditor">Cancel</button>
          <button type="button" class="btn-primary" :disabled="saving || !current?.id" @click="save">{{ saving ? 'Saving…' : 'Save changes' }}</button>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import { useNotificationsAdminStore } from '@/stores/notificationsAdmin'
import RichTextEditor from '@/components/RichTextEditor.vue'

const notify = useNotificationStore()
const store = useNotificationsAdminStore()
const loading = store.templatesLoading
const q = ref('')
const templates = ref([])

const filtered = computed(() => {
  const s = q.value.trim().toLowerCase()
  if (!s) return templates.value
  return templates.value.filter(t =>
    t.name?.toLowerCase().includes(s) || t.act?.toLowerCase().includes(s)
  )
})

const badge = (on) => on ? 'inline-flex px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700' : 'inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600'

const editing = ref(false)
const current = ref(null)
const channel = ref('email')
const saving = ref(false)
const htmlMode = ref(false)
const form = reactive({
  // email
  subject: '', email_body: '', email_sent_from_name: '', email_sent_from_address: '', email_status: false,
  // sms
  sms_body: '', sms_sent_from: '', sms_status: false,
  // push
  push_title: '', push_body: '', push_status: false,
})

const seg = (k) => `px-3 py-1 text-sm rounded ${channel.value===k ? 'bg-white border' : 'text-gray-600'}`

const prettyLoop = (val) => {
  try {
    if (typeof val === 'string') return JSON.stringify(JSON.parse(val), null, 2)
    return JSON.stringify(val, null, 2)
  } catch {
    return val
  }
}

const load = async () => {
  try {
    templates.value = await store.fetchTemplates()
  } catch (e) {
    console.debug('Templates load failed', e?.message)
    notify.error('Failed to load templates')
  }
}

onMounted(load)

const openEditor = (t) => {
  current.value = JSON.parse(JSON.stringify(t))
  form.subject = t.subject || ''
  form.email_body = t.email_body || ''
  form.email_sent_from_name = t.email_sent_from_name || ''
  form.email_sent_from_address = t.email_sent_from_address || ''
  form.email_status = !!t.email_status

  form.sms_body = t.sms_body || ''
  form.sms_sent_from = t.sms_sent_from || ''
  form.sms_status = !!t.sms_status

  form.push_title = t.push_title || ''
  form.push_body = t.push_body || ''
  form.push_status = !!t.push_status

  channel.value = 'email'
  // Default to HTML mode if body contains table tags or handlebars blocks Quill might mangle
  const html = (t.email_body || '').toLowerCase()
  htmlMode.value = /<table|<tr|<td|\{\{#each|\{\{#if/.test(html)
  editing.value = true
}

const closeEditor = () => {
  editing.value = false
  current.value = null
}

const save = async () => {
  if (!current.value) return
  const id = current.value.id
  const type = channel.value
  const payload = { type }
  if (type === 'email') {
    Object.assign(payload, {
      subject: form.subject,
      email_body: form.email_body,
      email_sent_from_name: form.email_sent_from_name,
      email_sent_from_address: form.email_sent_from_address,
      email_status: !!form.email_status,
    })
  } else if (type === 'sms') {
    Object.assign(payload, {
      sms_body: form.sms_body,
      sms_sent_from: form.sms_sent_from,
      sms_status: !!form.sms_status,
    })
  } else {
    Object.assign(payload, {
      push_title: form.push_title,
      push_body: form.push_body,
      push_status: !!form.push_status,
    })
  }
  try {
    saving.value = true
    await store.updateTemplate(id, payload)
    notify.success('Template updated')
    // reload list to be safe
    await load()
    closeEditor()
  } catch (e) {
    console.debug('Template update failed', e?.message)
    notify.error('Failed to update template')
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.input { width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.5rem 0.75rem; }
.textarea { width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.5rem 0.75rem; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
.btn-primary { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.375rem; color: #fff; background: #2563eb; }
.btn-outline { display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 0.375rem; border: 1px solid #d1d5db; color: #374151; background: #fff; }
.lbl { display:block; font-size: 0.875rem; margin-bottom: 0.25rem; color: #374151; }
.th { text-align:left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; padding: .75rem .5rem; }
.td { padding: .75rem .5rem; font-size: 0.875rem; color: #111827; }
</style>
