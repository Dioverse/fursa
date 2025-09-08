<template>
  <div class="space-y-6">
    <OrderDetailPanel :order="order" />
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
</script>
