<template>
    <div>
        <InventoryLogDetail :log="store.log" :loading="store.loading" />
    </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import InventoryLogDetail from '@/components/admin/inventory/InventoryLogDetail.vue'
import { useInventoryLogsStore } from '@/stores/inventoryLogs'

const route = useRoute()
const store = useInventoryLogsStore()

onMounted(() => {
    const id = route.params.id
    if (id) store.fetchLog(id).catch(() => { })
})
</script>
