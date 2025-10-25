//OrderDetailView.vue
<template>
  <div class="space-y-6">
    <OrderDetailPanel :order="order" :loading="ordersStore.loading" :allowedStatuses="allowedStatuses"
      @update-delivery="handleUpdateDelivery" @update-status="handleUpdateStatus" />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useOrdersStore } from '@/stores/orders'
import OrderDetailPanel from '@/components/admin/orders/OrderDetailPanel.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
const route = useRoute()
const ordersStore = useOrdersStore()
const order = ref(null)
const allowedStatuses = ref(null)

onMounted(async () => {
  const id = route.params.id
  if (id) {
    try {
      await ordersStore.fetchOrder(id)
      order.value = ordersStore.order
      allowedStatuses.value = ordersStore.allowedStatuses || []
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

const handleUpdateStatus = async (payload) => {
  if (!order.value?.id) return
  try {
    await ordersStore.updateOrder(order.value.id, { 
      status: payload.status,
      notify: payload.notify 
    })
    await refreshOrder()
  } catch (err) {
    // handled by store/api toasts
    toast.error(err.message)
  }
}
</script>
