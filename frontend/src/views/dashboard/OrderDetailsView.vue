<template>
    <DashboardLayout>
        <div class="order-details container mx-auto py-8 px-4">
            <!-- Loading / Error States -->
            <div v-if="loading" class="text-center py-10">Loading order details...</div>
            <div v-else-if="error" class="text-center text-red-500 py-10">{{ error }}</div>

            <div v-else>
            <!-- Page Title -->
            <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-6">Order Details</h2>

            <!-- Order Summary -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Order #{{ order.order_id }}</h3>
                <span
                    class="px-3 py-1 rounded-full text-sm"
                    :class="statusClass(order.status)"
                >
                    {{ order.status }}
                </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <p><strong>Date:</strong> {{ formatDate(order.created_at) }}</p>
                <p><strong>Payment Method:</strong> {{ order.payment_method }}</p>
                <p><strong>Total:</strong> ₦{{ Number(order.total_amount).toFixed(2) }}</p>
                <p><strong>Delivery Method:</strong> {{ order.delivery_method }}</p>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
                <p>{{ order.shipping_address?.name }}</p>
                <p>{{ order.shipping_address?.phone }}</p>
                <p>{{ order.shipping_address?.street }}</p>
                <p>
                {{ order.shipping_address?.city }},
                {{ order.shipping_address?.state }}
                </p>
            </div>

            <!-- Items Ordered -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Items Ordered</h3>
                <div class="divide-y">
                <div
                    v-for="item in order.items"
                    :key="item.id"
                    class="flex justify-between items-center py-3"
                >
                    <div class="flex items-center gap-4">
                    <img :src="item.image" alt="item" class="w-16 h-16 rounded-md" />
                    <div>
                        <p class="font-medium">{{ item.name }}</p>
                        <p class="text-sm text-gray-500">Qty: {{ item.quantity }}</p>
                    </div>
                    </div>
                    <p class="font-medium">₦{{ item.price }}</p>
                </div>
                </div>
            </div>

            <!-- Order Progress -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Track Your Order</h3>
                <div class="flex items-center justify-between">
                <div
                    v-for="(step, index) in steps"
                    :key="index"
                    class="flex-1 flex items-center"
                >
                    <div
                    class="flex flex-col items-center text-center w-full"
                    :class="{
                        'text-green-600': step.completed,
                        'text-gray-400': !step.completed
                    }"
                    >
                    <div
                        class="w-8 h-8 flex items-center justify-center rounded-full border-2"
                        :class="step.completed
                        ? 'bg-green-600 text-white border-green-600'
                        : 'border-gray-400'"
                    >
                        {{ index + 1 }}
                    </div>
                    <span class="text-xs mt-2">{{ step.label }}</span>
                    </div>
                    <div v-if="index < steps.length - 1" class="flex-1 h-1 bg-gray-300">
                    <div
                        class="h-1 bg-green-600"
                        :style="{ width: step.completed ? '100%' : '0%' }"
                    ></div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { formatDate } from '@/utils/helpers.js'

const route = useRoute();

const order = ref(null);
const steps = ref([]);
const loading = ref(true);
const error = ref("");

// Replace with your env base URL
const baseURL = import.meta.env.VITE_API_BASE_URL;

onMounted(async () => {
  try {
    const orderId = route.params.id; // from /orders/:id route
    const token = localStorage.getItem('token')
    const response = await axios.get(`${baseURL}/orders/${orderId}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    order.value = response.data.data;
    console.log( 'Order Details: '+ JSON.stringify(order.value.data));

    // Setup steps dynamically or static
    steps.value = [
      { label: "Order Placed", completed: true },
      { label: "Processing", completed: order.value.status !== "pending" },
      { label: "Shipped", completed: order.value.status === "shipped" || order.value.status === "delivered" },
      { label: "Delivered", completed: order.value.status === "delivered" },
    ];
  } catch (err) {
    error.value = "Failed to load order details.";
  } finally {
    loading.value = false;
  }
});

// Dynamic status color
const statusClass = (status) => {
  switch (status) {
    case "Pending":
      return "bg-yellow-100 text-yellow-700";
    case "Processing":
      return "bg-blue-100 text-blue-700";
    case "Shipped":
      return "bg-purple-100 text-purple-700";
    case "Delivered":
      return "bg-green-100 text-green-700";
    case "Cancelled":
      return "bg-red-100 text-red-700";
    default:
      return "bg-gray-100 text-gray-700";
  }
};
</script>

<style scoped>
.order-details {
  font-family: "Inter", sans-serif;
}
</style>
