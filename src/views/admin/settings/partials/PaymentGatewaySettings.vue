<template>
  <div class="bg-white shadow rounded-lg p-6 space-y-8">
    <div>
      <h2 class="text-lg font-semibold text-gray-900">Payment Gateways</h2>
      <p class="text-sm text-gray-500">Configure your payment processors and credentials.</p>
    </div>

    <!-- Paystack -->
    <section class="space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-900">Paystack</h3>
        <span class="text-xs px-2 py-1 rounded-full" :class="paystackForm.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
          {{ paystackForm.status || 'Inactive' }}
        </span>
      </div>
      <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select v-model="paystackForm.status" class="input">
            <option value="active">active</option>
            <option value="inactive">inactive</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Currency</label>
          <input v-model="paystackForm.currency" type="text" class="input" placeholder="NGN" />
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Public key</label>
          <input v-model="paystackForm.public_key" type="text" class="input" placeholder="pk_test_..." />
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Secret key</label>
          <input v-model="paystackForm.secret_key" type="text" class="input" placeholder="sk_test_..." />
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Logo/Image URL</label>
          <input v-model="paystackForm.image" type="text" class="input" placeholder="https://.../paystack.jpg" />
          <div class="mt-2 h-12 w-12 rounded bg-gray-100 border flex items-center justify-center overflow-hidden">
            <img v-if="paystackForm.image" :src="paystackForm.image" class="object-contain h-full w-full" alt="Paystack" />
            <span v-else class="text-xs text-gray-500">No image</span>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end">
        <button class="btn-primary" :disabled="saving.paystack" @click="savePaystack">
          {{ saving.paystack ? 'Saving…' : 'Save Paystack' }}
        </button>
      </div>
    </section>

    <hr />

    <!-- Flutterwave -->
    <section class="space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-900">Flutterwave</h3>
        <span class="text-xs px-2 py-1 rounded-full" :class="flutterForm.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
          {{ flutterForm.status || 'inactive' }}
        </span>
      </div>
      <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select v-model="flutterForm.status" class="input">
            <option value="active">active</option>
            <option value="inactive">inactive</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Currency</label>
          <input v-model="flutterForm.currency" type="text" class="input" placeholder="NGN" />
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Public key</label>
          <input v-model="flutterForm.public_key" type="text" class="input" placeholder="FLWPUBK_TEST-..." />
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Secret key</label>
          <input v-model="flutterForm.secret_key" type="text" class="input" placeholder="FLWSECK_TEST-..." />
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Encryption key</label>
          <input v-model="flutterForm.encryption_key" type="text" class="input" placeholder="FLWSECK_TEST..." />
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Logo/Image URL</label>
          <input v-model="flutterForm.image" type="text" class="input" placeholder="https://.../flutterwave.jpg" />
          <div class="mt-2 h-12 w-12 rounded bg-gray-100 border flex items-center justify-center overflow-hidden">
            <img v-if="flutterForm.image" :src="flutterForm.image" class="object-contain h-full w-full" alt="Flutterwave" />
            <span v-else class="text-xs text-gray-500">No image</span>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end">
        <button class="btn-primary" :disabled="saving.flutterwave" @click="saveFlutterwave">
          {{ saving.flutterwave ? 'Saving…' : 'Save Flutterwave' }}
        </button>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import { useGatewayStore } from '@/stores/gateways'

const notify = useNotificationStore()
const gatewayStore = useGatewayStore()

const paystackForm = reactive({ status: 'inactive', currency: 'NGN', public_key: '', secret_key: '', image: '' })
const flutterForm = reactive({ status: 'inactive', currency: 'NGN', public_key: '', secret_key: '', encryption_key: '', image: '' })

const saving = reactive({ paystack: false, flutterwave: false })

onMounted(async () => {
  try {
    const g = await gatewayStore.fetchGateways()
    if (g.paystack) Object.assign(paystackForm, g.paystack)
    if (g.flutterwave) Object.assign(flutterForm, g.flutterwave)
  } catch {
    notify.error('Failed to load gateways')
  }
})

const savePaystack = async () => {
  try {
    saving.paystack = true
    await gatewayStore.updatePaystack(paystackForm)
    notify.success('Paystack settings saved')
  } catch (err) {
    notify.error(err?.response?.data?.message || 'Failed to save Paystack')
  } finally {
    saving.paystack = false
  }
}

const saveFlutterwave = async () => {
  try {
    saving.flutterwave = true
    await gatewayStore.updateFlutterwave(flutterForm)
    notify.success('Flutterwave settings saved')
  } catch (err) {
    notify.error(err?.response?.data?.message || 'Failed to save Flutterwave')
  } finally {
    saving.flutterwave = false
  }
}
</script>

<style scoped>
.input { width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.5rem 0.75rem; }
.btn-primary { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.375rem; color: #fff; background: #b8974f; }
</style>
