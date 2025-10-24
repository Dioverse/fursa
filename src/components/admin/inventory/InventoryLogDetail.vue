<template>
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/60">
            <div class="flex items-center gap-3">
                <h3 class="text-lg font-semibold text-gray-900">Inventory Log</h3>
                <div v-if="log?.id" class="flex items-center gap-1">
                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200">#{{ log.id }}</span>
                    <button @click="copyId(log.id)" class="ml-1 inline-flex items-center text-xs text-blue-600 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200 rounded px-1.5 py-0.5" title="Copy ID">
                        📋
                    </button>
                    <span v-if="copied" class="text-xs text-green-600">Copied</span>
                </div>
            </div>
            <div>
                <slot name="actions" :log="log" />
            </div>
        </div>

        <div class="p-5 space-y-7">
            <!-- Meta row -->
            <div v-if="!loading" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-1 text-blue-700 ring-1 ring-inset ring-blue-200">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 4a.75.75 0 00-1.5 0v4.19l-2.22 2.22a.75.75 0 101.06 1.06l2.41-2.41A1.5 1.5 0 0010.75 10V6z"/></svg>
                        <span>{{ formatDate(log?.created_at || log?.createdAt || log?.date) }}</span>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-500">Performed by</span>
                    <span class="font-medium text-gray-900">{{ log?.user_name || (log?.user && (log.user.first_name + ' ' + log.user.last_name)) || 'N/A' }}</span>
                </div>
            </div>

            <!-- Loading skeleton -->
            <div v-if="loading" class="space-y-4 animate-pulse">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="h-3 w-20 bg-gray-200 rounded"></div>
                        <div class="mt-2 h-4 w-56 bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-3 w-24 bg-gray-200 rounded"></div>
                        <div class="mt-2 h-4 w-24 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="h-3 w-24 bg-gray-200 rounded"></div>
                        <div class="mt-2 h-4 w-28 bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-3 w-16 bg-gray-200 rounded"></div>
                        <div class="mt-2 h-4 w-40 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="h-3 w-20 bg-gray-200 rounded"></div>
                <div class="h-4 w-full max-w-md bg-gray-200 rounded"></div>
            </div>

            <!-- Details -->
            <div v-else class="space-y-7">
                <!-- Overview -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <div class="lg:col-span-2 rounded-lg border border-gray-100 p-4">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div class="flex flex-col">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Product</dt>
                                <dd class="mt-1 font-medium text-gray-900">
                                    <router-link v-if="productId(log)" :to="{ name: 'admin.products.detail', params: { id: productId(log) } }" class="text-blue-600 hover:text-blue-700 hover:underline">
                                        {{ log?.product_name || log?.product?.name || '-' }}
                                    </router-link>
                                    <span v-else>{{ log?.product_name || log?.product?.name || '-' }}</span>
                                </dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Operation</dt>
                                <dd class="mt-1">
                                    <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset', opBadgeClass(log)]">
                                        {{ friendlyChange(log) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Quantity</dt>
                                <dd class="mt-1 font-medium text-gray-900">{{ toNumber(log?.quantity || log?.qty) }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Reason</dt>
                                <dd class="mt-1 text-gray-900">{{ log?.reason || 'None' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Stock change card -->
                    <div class="rounded-lg border border-gray-100 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Stock change</div>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <div class="min-w-[4rem] text-center">
                                <div class="text-xs text-gray-500">Before</div>
                                <div class="font-semibold text-gray-900">{{ toNumber(log?.stock_before) }}</div>
                            </div>
                            <div class="flex-1 flex items-center justify-center gap-2">
                                <span :class="['inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold', deltaClass(log)]">
                                    <svg v-if="delta(log) > 0" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M3 12l7-7 7 7H3z"/></svg>
                                    <svg v-else-if="delta(log) < 0" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M17 8l-7 7-7-7h14z"/></svg>
                                    <span>{{ delta(log) > 0 ? '+' : '' }}{{ delta(log) }}</span>
                                </span>
                            </div>
                            <div class="min-w-[4rem] text-center">
                                <div class="text-xs text-gray-500">After</div>
                                <div class="font-semibold text-gray-900">{{ toNumber(log?.stock_after) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Context -->
                <div class="rounded-lg border border-gray-100 p-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="flex flex-col">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Performed by</dt>
                            <dd class="mt-1 font-medium text-gray-900">{{ log?.user_name || (log?.user && (log.user.first_name + ' ' + log.user.last_name)) || 'N/A' }}</dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Recorded at</dt>
                            <dd class="mt-1 text-gray-900">{{ formatDate(log?.created_at || log?.createdAt || log?.date) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({ log: { type: Object, default: null }, loading: { type: Boolean, default: false } })

const copied = ref(false)

const copyId = async (id) => {
    try {
        await navigator.clipboard.writeText(String(id))
        copied.value = true
        setTimeout(() => (copied.value = false), 1200)
    } catch {
        // no-op
    }
}

const formatDate = (d) => {
    if (!d) return '-'
    const dt = new Date(d)
    return dt.toLocaleString()
}

const toNumber = (v) => {
    const n = parseFloat(v)
    return isNaN(n) ? 0 : n
}

const delta = (log) => {
    const before = toNumber(log?.stock_before)
    const after = toNumber(log?.stock_after)
    return after - before
}

const friendlyChange = (log) => {
    const raw = String(log?.operation || log?.change_type || log?.type || '').toLowerCase()
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

const opBadgeClass = (log) => {
    const raw = String(log?.operation || log?.change_type || log?.type || '').toLowerCase()
    switch (raw) {
        case 'add':
            return 'bg-green-50 text-green-700 ring-green-200'
        case 'subtract':
        case 'remove':
            return 'bg-red-50 text-red-700 ring-red-200'
        case 'set':
            return 'bg-amber-50 text-amber-700 ring-amber-200'
        case 'delete':
            return 'bg-gray-100 text-gray-700 ring-gray-200'
        default:
            return 'bg-gray-50 text-gray-700 ring-gray-200'
    }
}

const deltaClass = (log) => {
    const d = delta(log)
    if (d > 0) return 'bg-green-50 text-green-700'
    if (d < 0) return 'bg-red-50 text-red-700'
    return 'bg-gray-100 text-gray-700'
}

const productId = (log) => log?.product_id || log?.product?.id || null
</script>
