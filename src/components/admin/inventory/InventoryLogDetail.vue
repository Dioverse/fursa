<template>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Inventory Log #{{ log?.id || '-' }}</h3>
            <div>
                <slot name="actions" :log="log" />
            </div>
        </div>

        <div class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-500">Product</div>
                    <div class="mt-1 font-medium">{{ log?.product_name || log?.product?.name || '-' }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Quantity</div>
                    <div class="mt-1 font-medium">{{ log?.quantity || log?.qty || 0 }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-500">Change Type</div>
                    <div class="mt-1 font-medium">{{ log?.change_type || log?.type || '-' }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">By</div>
                    <div class="mt-1 font-medium">{{ log?.user_name || (log?.user && (log.user.first_name + ' ' +
                        log.user.last_name)) || '-' }}</div>
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Reason</div>
                <div class="mt-1">{{ log?.reason || '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Recorded at</div>
                <div class="mt-1">{{ formatDate(log?.created_at || log?.createdAt || log?.date) }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({ log: { type: Object, default: null } })

const formatDate = (d) => {
    if (!d) return '-'
    const dt = new Date(d)
    return dt.toLocaleString()
}
</script>
