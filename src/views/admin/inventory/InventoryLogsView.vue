<template>
    <div class="space-y-6">

        <!-- Stats (simple counts from current page) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-500">Logs</div>
                <div class="mt-2 text-2xl font-semibold">{{ totalLogs }}</div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-500">Last Change</div>
                <div class="mt-2 text-2xl font-semibold">{{ lastChange }}</div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-500">Total Added</div>
                <div class="mt-2 text-2xl font-semibold">{{ totalAdded }}</div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-500">Total Removed</div>
                <div class="mt-2 text-2xl font-semibold">{{ totalRemoved }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm text-gray-600">Search</label>
                    <input v-model="filters.q" @input="onSearchInput"
                        class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Product, user, reason" />
                </div>

                <div>
                    <label class="block text-sm text-gray-600">Type</label>
                    <select v-model="filters.type"
                        class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All</option>
                        <option value="add">Add</option>
                        <option value="remove">Remove</option>
                        <option value="adjust">Adjust</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-600">From</label>
                    <input type="date" v-model="filters.from_date"
                        class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500" />
                </div>

                <div>
                    <label class="block text-sm text-gray-600">To</label>
                    <input type="date" v-model="filters.to_date"
                        class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500" />
                </div>
            </div>

            <div class="mt-4 flex items-center space-x-2">
                <button @click="applyFilters"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600">Apply</button>
                <button @click="clearFilters"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white">Clear</button>
            </div>
        </div>

        <!-- List -->
        <InventoryLogList :logs="store.logs" :loading="store.loading" :pagination="store.pagination">
            <template #actions>
                <!-- future create button -->
            </template>

            <template #pagination>
                <TablePagination v-bind="store.pagination" @pageChange="handlePageChange" />
            </template>
        </InventoryLogList>
    </div>
</template>

<script setup>
import InventoryLogList from '@/components/admin/inventory/InventoryLogList.vue'
import TablePagination from '@/components/common/TablePagination.vue'
import { useInventoryLogsStore } from '@/stores/inventoryLogs'
import { onMounted, reactive, computed } from 'vue'

const store = useInventoryLogsStore()

const filters = reactive({ q: '', type: '', from_date: '', to_date: '' })
let searchTimer = null

const fetchWithFilters = (page = 1) => {
    const params = { page }
    if (filters.q) params.q = filters.q
    if (filters.type) params.type = filters.type
    if (filters.from_date) params.from_date = filters.from_date
    if (filters.to_date) params.to_date = filters.to_date
    return store.fetchLogs(params).catch(() => { })
}

const applyFilters = () => fetchWithFilters(1)
const clearFilters = () => {
    filters.q = ''
    filters.type = ''
    filters.from_date = ''
    filters.to_date = ''
    fetchWithFilters(1)
}

const onSearchInput = () => {
    if (searchTimer) clearTimeout(searchTimer)
    searchTimer = setTimeout(() => fetchWithFilters(1), 400)
}

const handlePageChange = (page) => fetchWithFilters(page)

onMounted(() => fetchWithFilters().catch(() => { }))

const totalLogs = computed(() => Number(store.pagination?.total ?? (Array.isArray(store.logs) ? store.logs.length : 0)))
const lastChange = computed(() => (Array.isArray(store.logs) && store.logs[0]) ? new Date(store.logs[0].created_at || store.logs[0].date || store.logs[0].createdAt).toLocaleString() : '-')
const totalAdded = computed(() => {
    if (!Array.isArray(store.logs)) return 0
    return store.logs.reduce((acc, l) => acc + ((String(l.change_type || l.type || '').toLowerCase().includes('add')) ? (parseFloat(l.quantity || l.qty || 0) || 0) : 0), 0)
})
const totalRemoved = computed(() => {
    if (!Array.isArray(store.logs)) return 0
    return store.logs.reduce((acc, l) => acc + ((String(l.change_type || l.type || '').toLowerCase().includes('remove') || String(l.change_type || l.type || '').toLowerCase().includes('subtract')) ? (parseFloat(l.quantity || l.qty || 0) || 0) : 0), 0)
})
</script>
