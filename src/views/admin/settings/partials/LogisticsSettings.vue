<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold text-gray-900">Shipping Rules</h2>
      <p class="text-sm text-gray-500">Manage delivery rules by country, state and province.</p>
    </div>

    <!-- Filters + actions -->
    <div class="bg-white shadow rounded-lg p-4">
      <div class="grid sm:grid-cols-2 lg:grid-cols-6 gap-3">
        <!-- Country -->
        <select v-model="filters.country" class="input">
          <option value="">All countries</option>
          <option v-for="c in countryOptions" :key="c" :value="c">{{ c }}</option>
        </select>

        <!-- State (depends on country) -->
        <select v-model="filters.state" class="input" :disabled="!filters.country">
          <option value="">All states</option>
          <option v-for="s in stateOptions" :key="s" :value="s">{{ s }}</option>
        </select>

        <!-- Province (depends on state) -->
        <select v-model="filters.province" class="input" :disabled="!filters.state">
          <option value="">All provinces</option>
          <option v-for="p in provinceOptions" :key="p" :value="p">{{ p }}</option>
        </select>

        <input v-model="filters.provider" class="input" placeholder="Provider" />
        <select v-model="filters.status" class="input">
          <option value="">Any status</option>
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
  <button class="btn-outline" @click="applyFilters">Apply</button>
      </div>
    </div>

    <!-- List -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-3 flex items-center justify-between">
        <div class="text-sm text-gray-500">Total: {{ total }} | Active: {{ stats?.active ?? 0 }} | Inactive: {{ stats?.inactive ?? 0 }}</div>
        <div class="flex gap-2">
          <button class="btn-outline" @click="openCreate">Add rule</button>
          <button class="btn-outline" :disabled="selected.length===0" @click="bulkToggle">Toggle selected</button>
          <button class="btn-outline" :disabled="selected.length===0" @click="bulkDelete">Delete selected</button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="th"><input type="checkbox" :checked="allChecked" @change="toggleAll" /></th>
              <th class="th">Country</th>
              <th class="th">State</th>
              <th class="th">Province</th>
              <th class="th">ETA</th>
              <th class="th">Cost</th>
              <th class="th">Provider</th>
              <th class="th">Active</th>
              <th class="th text-right">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="r in items" :key="r.id">
              <td class="td"><input type="checkbox" :value="r.id" v-model="selected" /></td>
              <td class="td">{{ r.country }}</td>
              <td class="td">{{ r.state }}</td>
              <td class="td">{{ r.province }}</td>
              <td class="td">{{ r.min_days }}{{ r.max_days ? '–'+r.max_days : '' }} days</td>
              <td class="td">{{ formatCurrency(r.cost) }}</td>
              <td class="td">{{ r.provider || '—' }}</td>
              <td class="td"><span :class="badge(r.is_active == 1)">{{ r.is_active == 1 ? 'Yes' : 'No' }}</span></td>
              <td class="td text-right">
                <button class="btn-outline" @click="openEdit(r)">Edit</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="px-4 py-3 border-t flex items-center gap-3 text-sm">
        <button class="btn-outline" @click="prev" :disabled="page<=1">Prev</button>
        <div>Page {{ page }} of {{ lastPage }}</div>
        <button class="btn-outline" @click="next" :disabled="page>=lastPage">Next</button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="modal" class="fixed inset-0 z-40 flex items-end sm:items-center justify-center bg-black/30">
      <div class="bg-white w-full sm:max-w-2xl max-h-[90vh] overflow-auto rounded-t-lg sm:rounded-lg shadow-lg">
        <div class="flex items-center justify-between border-b px-4 py-3">
          <h4 class="font-semibold">{{ editId ? 'Edit Rule' : 'Add Rule' }}</h4>
          <button class="btn-outline" @click="closeModal">Close</button>
        </div>
        <div class="p-4 grid sm:grid-cols-2 gap-3">
          <!-- Country select -->
          <select v-model="form.country" class="input">
            <option value="">Select country</option>
            <option v-for="c in countryOptions" :key="`mc-${c}`" :value="c">{{ c }}</option>
          </select>

          <!-- State select (depends on country) -->
          <select v-model="form.state" class="input" :disabled="!form.country">
            <option value="">Select state</option>
            <option v-for="s in stateOptions" :key="`ms-${s}`" :value="s">{{ s }}</option>
          </select>

          <!-- Province select (depends on state) -->
          <select v-model="form.province" class="input" :disabled="!form.state">
            <option value="">Select province</option>
            <option v-for="p in provinceOptions" :key="`mp-${p}`" :value="p">{{ p }}</option>
          </select>
          <input v-model.number="form.min_days" type="number" min="1" class="input" placeholder="Min days" />
          <input v-model.number="form.max_days" type="number" min="1" class="input" placeholder="Max days (optional)" />
          <input v-model.number="form.cost" type="number" min="0" step="0.01" class="input" placeholder="Cost" />
          <input v-model="form.provider" class="input" placeholder="Provider (optional)" />
        </div>
        <div class="flex items-center justify-end gap-3 px-4 py-3 border-t">
          <button type="button" class="btn-outline" @click="closeModal">Cancel</button>
          <button type="button" class="btn-primary" :disabled="saving" @click="confirmSave">{{ saving ? 'Saving…' : 'Save' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import { useShippingAdminStore } from '@/stores/shippingAdmin'

const notify = useNotificationStore()
const store = useShippingAdminStore()

const items = store.items
const page = store.page
const lastPage = store.lastPage
const total = store.total
const stats = store.stats
const filters = store.filters
const saving = store.saving
const filtersOpts = store.filtersOpts

const selected = ref([])
const allChecked = computed(() => {
  const list = Array.isArray(items.value) ? items.value : []
  return list.length > 0 && selected.value.length === list.length
})
const toggleAll = (e) => {
  const list = Array.isArray(items.value) ? items.value : []
  selected.value = e.target.checked ? list.map(i => i.id) : []
}

const load = async (params={}) => {
  await store.fetchList({ page: page.value, ...params })
}

onMounted(() => { load() })

const prev = () => { if (page.value>1) { page.value--; load() } }
const next = () => { if (page.value<lastPage.value) { page.value++; load() } }

const applyFilters = async () => {
  page.value = 1
  await store.fetchFilters({ country: filters.country, state: filters.state })
  await load()
}

const badge = (on) => on ? 'inline-flex px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700' : 'inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600'
const formatCurrency = (n) => new Intl.NumberFormat(undefined, { style:'currency', currency:'NGN' }).format(Number(n||0))

// Modal state
const modal = ref(false)
const editId = ref(null)
const form = reactive({ country:'', state:'', province:'', min_days:1, max_days:null, cost:0, provider:'' })

const openCreate = async () => {
  editId.value = null
  Object.assign(form, { country:'', state:'', province:'', min_days:1, max_days:null, cost:0, provider:'' })
  // prefetch options for empty selection
  await store.fetchFilters()
  modal.value = true
}
const openEdit = async (r) => {
  editId.value = r.id
  Object.assign(form, { country:r.country, state:r.state, province:r.province, min_days:r.min_days, max_days:r.max_days, cost:r.cost, provider:r.provider })
  // prefetch options for given country/state so selects populate
  await store.fetchFilters({ country: r.country, state: r.state })
  modal.value = true
}
const closeModal = () => { modal.value = false }

const confirmSave = async () => {
  try {
    if (editId.value) {
      const payload = { state: form.state, province: form.province, min_days: form.min_days, max_days: form.max_days, cost: form.cost, provider: form.provider }
      await store.updateRule(editId.value, payload)
      notify.success('Rule updated')
    } else {
      await store.createRules([{ ...form }])
      notify.success('Rule created')
    }
    modal.value = false
    await load()
  } catch {
    notify.error('Save failed')
  }
}

const bulkToggle = async () => {
  try {
    await store.bulkAction('toggle', selected.value)
    selected.value = []
    notify.success('Updated selected')
    await load()
  } catch {
    notify.error('Bulk action failed')
  }
}

const bulkDelete = async () => {
  try {
    await store.bulkAction('delete', selected.value)
    selected.value = []
    notify.success('Deleted selected')
    await load()
  } catch {
    notify.error('Bulk action failed')
  }
}

// Dependent dropdown options
const uniq = (arr) => Array.from(new Set((arr || []).filter(Boolean)))
const countryOptions = computed(() => {
  const server = filtersOpts.value?.countries || []
  const fallback = uniq((Array.isArray(items.value) ? items.value : []).map(i => i.country))
  const merged = (server && server.length ? server : fallback) || []
  return uniq([...merged, filters.country, form.country])
})
const stateOptions = computed(() => {
  const server = filtersOpts.value?.states || []
  if (server && server.length) return server
  const baseCountry = form.country || filters.country || null
  const list = Array.isArray(items.value) ? items.value : []
  const filtered = baseCountry ? list.filter(i => i.country === baseCountry) : list
  const fallback = uniq(filtered.map(i => i.state))
  return uniq([...fallback, filters.state, form.state])
})
const provinceOptions = computed(() => {
  const server = filtersOpts.value?.provinces || []
  if (server && server.length) return server
  const baseCountry = form.country || filters.country || null
  const baseState = form.state || filters.state || null
  const list = Array.isArray(items.value) ? items.value : []
  const filtered = list.filter(i => (!baseCountry || i.country === baseCountry) && (!baseState || i.state === baseState))
  const fallback = uniq(filtered.map(i => i.province))
  return uniq([...fallback, filters.province, form.province])
})

// React to filter changes to refresh dependent options
watch(() => filters.country, async (val, old) => {
  if (val !== old) {
    filters.state = ''
    filters.province = ''
    page.value = 1
  await store.fetchFilters({ country: filters.country })
  await load()
  }
})

watch(() => filters.state, async (val, old) => {
  if (val !== old) {
    filters.province = ''
    page.value = 1
  await store.fetchFilters({ country: filters.country, state: filters.state })
  await load()
  }
})

watch(() => filters.province, async (val, old) => {
  if (val !== old) {
    page.value = 1
    await load()
  }
})

// Keep modal dropdowns in sync with available options
watch(() => form.country, async (val, old) => {
  if (val !== old) {
    form.state = ''
    form.province = ''
    await store.fetchFilters({ country: form.country })
  }
})

watch(() => form.state, async (val, old) => {
  if (val !== old) {
    form.province = ''
    await store.fetchFilters({ country: form.country, state: form.state })
  }
})
</script>

<style scoped>
.input { width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.5rem 0.75rem; }
.btn-primary { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.375rem; color: #fff; background: #b8974f; }
.btn-outline { display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 0.375rem; border: 1px solid #d1d5db; color: #374151; background: #fff; }
.th { text-align:left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; padding: .75rem .5rem; }
.td { padding: .75rem .5rem; font-size: 0.875rem; color: #111827; }
</style>
