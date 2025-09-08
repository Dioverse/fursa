<template>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Order #{{ order?.id || order?.order_number }}</h3>
            <div>
                <slot name="actions" :order="order" />
            </div>
        </div>

        <div class="p-4">
            <div v-if="!order" class="text-sm text-gray-500">No order selected</div>
            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Customer</h4>
                    <div class="text-sm text-gray-900">{{ order.customer || order.customer_name || (order.user &&
                        (order.user.first_name + ' ' + order.user.last_name)) }}</div>
                    <div class="text-sm text-gray-500">{{ order.email || (order.user && order.user.email) }}</div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700">Totals</h4>
                    <div class="text-sm text-gray-900">Subtotal: {{ formatCurrency(order.subtotal || order.sub_total ||
                        order.total || 0) }}</div>
                    <div class="text-sm text-gray-900">Shipping: {{ formatCurrency(order.shipping || 0) }}</div>
                    <div class="text-sm text-gray-900">Total: {{ formatCurrency(order.total || order.total_amount || 0)
                        }}</div>
                </div>

                <div class="sm:col-span-2">
                    <h4 class="text-sm font-medium text-gray-700">Items</h4>
                    <ul class="divide-y divide-gray-100">
                        <li v-for="item in order.items || order.order_items || []" :key="item.id"
                            class="py-2 flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-900">{{ item.name || item.title }}</div>
                                <div class="text-xs text-gray-500">Qty: {{ item.quantity || item.qty }} • {{
                                    formatCurrency(item.price || item.unit_price || item.total_price) }}</div>
                            </div>
                            <div class="text-sm text-gray-900">{{ formatCurrency((item.quantity || item.qty || 1) *
                                (item.price || item.unit_price || 0)) }}</div>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700">Status</h4>
                    <div class="text-sm text-gray-900">{{ order.status }}</div>
                    <div class="text-sm text-gray-500">Placed: {{ formatDate(order.created_at) }}</div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700">Shipping Address</h4>
                    <div class="text-sm text-gray-900">{{ order.shipping_address?.address || order.address || 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-500">{{ order.shipping_address?.city || '' }} {{
                        order.shipping_address?.state || '' }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({ order: { type: [Object, null], default: null } })

const formatCurrency = (v) => {
    if (v == null) return '-'
    return Number(v).toLocaleString(undefined, { style: 'currency', currency: 'USD' })
}

const formatDate = (d) => {
    if (!d) return '-'
    const dt = new Date(d)
    return dt.toLocaleString()
}
</script>
