<template>
  <DashboardLayout>
    <div class="order-details container mx-auto md:gap-6 lg:gap-8 gap-3">
      <!-- Loading / Error States -->
      <div v-if="loading" class="text-center py-10">Loading order details...</div>
      <div v-else-if="error" class="text-center text-red-500 py-10">{{ error }}</div>

      <div v-else>
        <!-- Page Title -->
        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-6">Order Details</h2>

        <!-- Order Summary -->
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100 mb-6 hover:shadow-2xl transition-shadow duration-300">
                <!-- Header with Gradient -->
                <div class="bg-gradient-to-r from-gold-500 to-mprimary-600 px-6 py-5 flex justify-between items-center cursor-[pointer]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <font-awesome-icon icon="fa-box" class="text-white text-md"></font-awesome-icon>
                        </div>
                        <div>
                            <p class="text-blue-100 text-xs font-medium">Order Number</p>
                            <h3 class="text-md font-bold text-white">#{{ order.order_id }}</h3>
                        </div>
                    </div>
                    <span :class="['px-3 py-2 rounded-full text-xs font-semibold shadow-md', statusClass(order.status)]">
                        {{ toUcwords(order.status) }}
                    </span>
                </div>

                <!-- Order Details Grid -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 cursor-[pointer]">
                        <!-- Date -->
                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition-colors">
                                <font-awesome-icon icon="fa-calendar" class="text-blue-600 text-md"></font-awesome-icon>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Order Date</p>
                                <p class="text-gray-800 font-bold">{{ formatDate(order.created_at) }}</p>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-green-100 transition-colors">
                                <font-awesome-icon icon="fa-credit-card" class="text-green-600 text-md"></font-awesome-icon>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Payment Method</p>
                                <p class="text-gray-800 font-bold">{{ formatDeliveryLabel(order.payment?.payment_method) || 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Total Amount -->
                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-purple-100 transition-colors">
                                <font-awesome-icon icon="fa-dollar-sign" class="text-purple-600 text-md"></font-awesome-icon>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Total Amount</p>
                                <p class="text-xl font-bold text-gray-800">₦{{ formatAmount(order.total_amount, 2) }}</p>
                            </div>
                        </div>

                        <!-- Gateway -->
                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-orange-100 transition-colors">
                                <font-awesome-icon icon="fa-server" class="text-orange-600 text-md"></font-awesome-icon>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Payment Gateway</p>
                                <p class="text-gray-800 font-bold">{{ formatDeliveryLabel(order.payment?.payment_gateway) || 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Requery Button -->
                    <div v-if="order.status === 'pending'" class="mt-6 pt-6 border-t border-gray-100">
                        <button
                            @click="requeryPayment"
                            :disabled="requerying"
                            class="w-full md:w-auto bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold px-6 py-3 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-sync" :class="{ 'fa-spin': requerying }"></i>
                            <font-awesome-icon icon="fa-sync" :class="{ 'fa-spin': requerying }"></font-awesome-icon>
                            <span>{{ requerying ? 'Requerying...' : 'Requery Payment Status' }}</span>
                        </button>
                    </div>
                </div>
            </div>

        <!-- Shipping Address -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
          <div class="flex items-center justify-between mb-5 pb-4 border-b border-gray-100">
              <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                  <font-awesome-icon icon="fa-map-marker-alt"></font-awesome-icon>
                  Shipping Address
              </h3>
          </div>
          <div class="space-y-4">
              <!-- Name & Phone Row -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-3 py-2">
                  <div class="flex items-start gap-3">
                      <div class="mt-1 text-blue-500">
                        <font-awesome-icon icon="fa-user"></font-awesome-icon>
                      </div>
                      <div>
                          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Full Name</p>
                          <p class="text-gray-800 font-semibold">{{ shipping?.full_name }}</p>
                      </div>
                  </div>
                  <div class="flex items-start gap-3">
                      <div class="mt-1 text-green-500">
                        <font-awesome-icon icon="fa-phone"></font-awesome-icon>
                      </div>
                      <div>
                          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Phone Number</p>
                          <p class="text-gray-800 font-semibold">{{ shipping?.phone }}</p>
                      </div>
                  </div>
              </div>

              <!-- Address Lines -->
              <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-100">
                  <div class="flex items-start gap-3 mb-3">
                      <div class="rounded-full mt-1 text-purple-500">
                        <font-awesome-icon icon="fa-home"></font-awesome-icon>
                      </div>
                      <div class="flex-1">
                          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Address</p>
                          <p class="text-gray-800 font-medium">{{ shipping?.address_line_one }}</p>
                          <p class="text-gray-600 mb-3">{{ shipping?.address_line_two }}</p>
                      </div>
                  </div>
                  <div class="flex gap-6">
                    <div class="flex items-start gap-3">
                      <div class="flex justify-center rounded-full mt-1 text-orange-500">
                        <font-awesome-icon icon="fa-city"></font-awesome-icon>
                      </div>
                      <div>
                          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">City & State</p>
                          <p class="text-gray-800 font-semibold">{{ shipping?.city }}, {{ shipping?.state }}</p>
                      </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="mt-1 text-red-500">
                            <font-awesome-icon icon="fa-flag"></font-awesome-icon>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Country</p>
                            <p class="text-gray-800 font-semibold">{{ shipping?.country }}</p>
                        </div>
                    </div>
                  </div>
              </div>

              <!-- City, State & Country -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-3 py-2">
                  
              </div>
          </div>
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
                <div class="flex items-center justify-center rounded-full border-2" :class="step.completed
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
import { formatAmount, formatDate, formatDeliveryLabel, getImageUrl, handleImageError, toUcwords } from "@/utils/helpers.js";
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
    console.log(shipping.value);

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
