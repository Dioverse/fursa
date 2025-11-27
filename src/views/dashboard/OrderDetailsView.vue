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
            <span class="px-3 py-1 rounded-full text-sm" :class="statusClass(order.status)">
              {{ toUcwords(order.status) }}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
            <p><strong>Date:</strong> {{ formatDate(order.created_at) }}</p>
            <p><strong>Payment Method:</strong> {{ order.payment?.payment_method || 'N/A' }}</p>
            <p><strong>Total:</strong> ₦{{ formatAmount(order.total_amount, 2) }}</p>
            <p><strong>Gateway:</strong> {{ order.payment?.payment_gateway || 'N/A' }}</p>
          </div>

          <!-- 🔁 Requery Button -->
          <div v-if="order.status === 'pending'" class="mt-6">
            <button @click="requeryPayment" :disabled="requerying"
              class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="!requerying">Requery Payment</span>
              <span v-else>Requerying...</span>
            </button>
          </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-white shadow rounded-lg p-6 mb-6 text-gray-600">
          <h3 class="text-md font-semibold mb-4 text-black">Shipping Address</h3>
          <p><strong>Name: </strong>{{ shipping?.full_name }}</p>
          <p><strong>Phone: </strong>{{ shipping?.phone }}</p>
          <p><strong>Address Line One: </strong>{{ shipping?.address_line_one }}</p>
          <p><strong>City: </strong>{{ shipping?.city }}, {{ shipping?.state }}</p>
        </div>

        <!-- Items Ordered -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
          <h3 class="text-md font-semibold mb-4">Items Ordered</h3>
          <div class="divide-y">
            <div v-for="item in order.order_item" :key="item.id" class="flex justify-between items-center py-3">
              <div class="flex items-center gap-4">
                <img :src="getImageUrl(item.product.images?.[0]?.path)" @error="handleImageError" alt="item"
                  class="w-16 h-16 rounded-md object-cover" />
                <div>
                  <p class="font-medium">{{ item.product.name }}</p>
                  <p class="text-sm text-gray-500">Qty: {{ item.quantity }}</p>
                </div>
              </div>
              <p class="font-medium">₦{{ formatAmount(item.unit_price, 2) }}</p>
            </div>
          </div>
        </div>

        <!-- Order Progress -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
          <h3 class="text-lg font-semibold mb-4">Track Your Order</h3>
          <div class="flex items-center justify-between">
            <div v-for="(step, index) in steps" :key="index" class="flex-1 flex items-center">
              <div class="flex flex-col items-center text-center w-full"
                :class="step.completed ? 'text-green-600' : 'text-gray-400'">
                <div class="w-8 h-8 flex items-center justify-center rounded-full border-2" :class="step.completed
                  ? 'bg-green-600 text-white border-green-600'
                  : 'border-gray-400'">
                  <font-awesome-icon v-if="step.completed && step.number < currentStep" icon="check"
                    class="text-white" />
                  <span v-else>{{ step.number }}</span>
                </div>
                <p class="text-xs mt-2 truncate max-w-[8rem]" :class="[
                  step.number === currentStep
                    ? 'block'
                    : 'hidden md:block text-gray-400'
                ]">
                  {{ step.label }}
                </p>
              </div>

              <!-- Connecting line -->
              <div v-if="index < steps.length - 1" class="flex-1 h-1 bg-gray-300">
                <div class="h-1 bg-green-600" :style="{ width: step.completed ? '100%' : '0%' }"></div>
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
import { useRoute, useRouter } from "vue-router";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import { formatAmount, formatDate, getImageUrl, handleImageError, toUcwords } from "@/utils/helpers.js";
import apiClient from "@/services/api";
import { useToast } from "vue-toastification";

const route = useRoute();
const router = useRouter();
const toast = useToast()

const order = ref(null);
const shipping = ref(null);
const steps = ref([]);
const currentStep = ref(1);
const loading = ref(true);
const requerying = ref(false);
const error = ref("");

onMounted(async () => {
  await fetchOrder();
  if (order.value?.status_hstry) {
    generateSteps(order.value.status_hstry);
  }
});

const fetchOrder = async () => {
  loading.value = true;
  error.value = null;

  try {
    const orderId = route.params.id;
    const { data } = await apiClient.get(`/orders/${orderId}`);
    order.value = data.data;
    shipping.value = JSON.parse(data.data.shipping_address)

    // Setup progress steps
    steps.value = [
      { label: "Order Placed", completed: true },
      { label: "Processing", completed: order.value.status !== "pending" },
      { label: "Shipped", completed: ["shipped", "delivered"].includes(order.value.status) },
      { label: "Delivered", completed: order.value.status === "delivered" },
    ];
  } catch (err) {
    console.error("Failed to load order details:", err);
    error.value = "Failed to load order details.";
    toast.error("Unable to load order details. Please try again.");
  } finally {
    loading.value = false;
  }
};

const requeryPayment = async () => {
  if (!order.value?.payment?.payment_gateway || !order.value?.trans_ref) {
    toast.error("Cannot requery this order. Missing payment details.");
    return;
  }

  try {
    requerying.value = true;
    const gateway = order.value.payment.payment_gateway;
    const reference = order.value.trans_ref;
    const orderId = order.value.id;

    const response = await apiClient.post(`/checkout/${gateway}/${reference}/${orderId}`, {});
    const resData = response.data;

    if (resData.error) {
      toast.error(resData.message || "Requery failed. Please try again later.");
    } else {
      toast.success(resData.message || "Payment reverified successfully!");
      await fetchOrder(); // refresh data
    }
  } catch (err) {
    console.error("Requery error:", err);
    toast.error(err.response?.data?.message || "An error occurred while requering payment.");
  } finally {
    requerying.value = false;
  }
};

const statusClass = (status) => {
  switch (status?.toLowerCase()) {
    case "pending":
      return "bg-yellow-100 text-yellow-700";
    case "confirmed":
    case "processing":
      return "bg-blue-100 text-blue-700";
    case "shipping":
    case "shipped":
      return "bg-purple-100 text-purple-700";
    case "out for delivery":
      return "bg-indigo-100 text-indigo-700";
    case "delivered":
      return "bg-green-100 text-green-700";
    case "cancelled":
    case "failed":
      return "bg-red-100 text-red-700";
    default:
      return "bg-gray-100 text-gray-700";
  }
};

const generateSteps = (statusHistory) => {
  const allStatuses = [
    "pending",
    "confirmed",
    "processing",
    "shipping",
    "shipped",
    "out for delivery",
    "delivered",
  ];

  const lastStatus = statusHistory[statusHistory.length - 1]?.status?.toLowerCase();

  steps.value = allStatuses.map((status, index) => ({
    number: index + 1,
    label: toUcwords(status),
    completed: statusHistory.some((s) => s.status.toLowerCase() === status),
  }));

  // Handle cancelled/failed jump
  const lastHistStatus = statusHistory[statusHistory.length - 1]?.status?.toLowerCase();
  if (["cancelled", "failed"].includes(lastHistStatus)) {
    steps.value.push({
      number: steps.value.length + 1,
      label: toUcwords(lastHistStatus),
      completed: true,
    });
  }

  // Set current step
  currentStep.value = steps.value.findIndex((s) => !s.completed) + 1 || steps.value.length;
};


</script>
