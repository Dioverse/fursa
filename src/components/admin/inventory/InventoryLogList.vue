<template>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Inventory Log</h3>
            <div>
                <slot name="actions" />
            </div>
        </div>

        <div class="p-4">
            <!-- Dummy loading skeleton -->
            <div v-if="loading">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs text-gray-500">
                            <th class="py-2">#</th>
                            <th class="py-2">Product</th>
                            <th class="py-2">Change</th>
                            <th class="py-2">Qty</th>
                            <th class="py-2">By</th>
                            <th class="py-2">At</th>
                            <th class="py-2">Reason</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="i in 6" :key="i" class="border-t border-gray-100 animate-pulse">
                            <td class="py-2"><div class="h-3 w-6 bg-gray-200 rounded"></div></td>
                            <td class="py-2"><div class="h-3 w-48 bg-gray-200 rounded"></div></td>
                            <td class="py-2"><div class="h-3 w-20 bg-gray-200 rounded"></div></td>
                            <td class="py-2"><div class="h-3 w-10 bg-gray-200 rounded"></div></td>
                            <td class="py-2"><div class="h-3 w-40 bg-gray-200 rounded"></div></td>
                            <td class="py-2"><div class="h-3 w-36 bg-gray-200 rounded"></div></td>
                            <td class="py-2"><div class="h-3 w-24 bg-gray-200 rounded"></div></td>
                            <td class="py-2"><div class="h-3 w-12 bg-gray-200 rounded"></div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else>
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs text-gray-500">
                            <th class="py-2">#</th>
                            <th class="py-2">Product</th>
                            <th class="py-2">Change</th>
                            <th class="py-2">Qty</th>
                            <th class="py-2">By</th>
                            <th class="py-2">At</th>
                            <th class="py-2">Reason</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="log in logs" :key="log.id" class="border-t border-gray-100">
                            <td class="py-2">{{ log.id }}</td>
                            <td class="py-2">{{ log.product_name || (log.product && log.product.name) || '-' }}</td>
                            <td class="py-2">{{ friendlyChange(log) }}</td>
                            <td class="py-2">{{ log.quantity || log.qty || 0 }}</td>
                            <td class="py-2">{{ log.user_name || (log.user && (log.user.first_name + ' ' +
                                log.user.last_name)) || '-' }}</td>
                            <td class="py-2">{{ formatDate(log.created_at || log.createdAt || log.date) }}</td>
                            <td class="py-2">{{ log.reason || '-' }}</td>
                            <td class="py-2">
                                <slot name="row-actions" :log="log">
                                    <router-link :to="{ name: 'admin.inventory.logs.detail', params: { id: log.id } }"
                                        class="text-primary-600">View</router-link>
                                </slot>
                            </td>
                        </tr>
                        <tr v-if="!logs || logs.length === 0">
                            <td colspan="8" class="py-6 text-center text-gray-500">No inventory logs found.</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    <slot name="pagination" :pagination="pagination" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({ logs: { type: Array, default: () => [] }, loading: { type: Boolean, default: false }, pagination: { type: Object, default: () => ({}) } })

const formatDate = (d) => {
    if (!d) return '-'
    const dt = new Date(d)
    return dt.toLocaleString()
}

const friendlyChange = (log) => {
    const raw = String(log.operation || log.change_type || log.type || '').toLowerCase()
    switch (raw) {
        case 'add':
            return 'Added'
        case 'subtract':
        case 'remove':
            return 'Subtracted'
        case 'set':
            return 'Set'
        case 'delete':
            return 'Deleted'
        default:
            return raw || '-'
    }
}
</script>
