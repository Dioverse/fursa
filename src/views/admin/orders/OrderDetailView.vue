<template>
  <div class="space-y-6">
    <OrderDetailPanel
      :order="order"
      :loading="ordersStore.loading"
      @update-delivery="handleUpdateDelivery"
      @update-status="handleUpdateStatus"
    />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useOrdersStore } from '@/stores/orders'
import OrderDetailPanel from '@/components/admin/orders/OrderDetailPanel.vue'

const route = useRoute()
const ordersStore = useOrdersStore()
const order = ref(null)

onMounted(async () => {
  const id = route.params.id
  if (id) {
    try {
      await ordersStore.fetchOrder(id)
      order.value = ordersStore.order
    } catch {
      // ignore
    }
  }
})

const refreshOrder = async () => {
  const id = route.params.id
  if (!id) return
  await ordersStore.fetchOrder(id)
  order.value = ordersStore.order
}

const handleUpdateDelivery = async (dateStr) => {
  if (!order.value?.id) return
  try {
    await ordersStore.updateOrder(order.value.id, { delivery_date: dateStr })
    await refreshOrder()
  } catch {
    // handled by store/api toasts
  }
}

const handleUpdateStatus = async (newStatus) => {
  if (!order.value?.id) return
  try {
    await ordersStore.updateOrder(order.value.id, { status: newStatus })
    await refreshOrder()
  } catch {
    // handled by store/api toasts
  }
}
</script>
