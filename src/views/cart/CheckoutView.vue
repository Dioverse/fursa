<template>
  <DefaultLayout>
    <div class="min-h-screen mx-auto bg-gray-50 container lg:px-20 px-3">
      <div>
        <div class="container mx-auto px-4 py-8">
          <!-- Loader -->
          <div v-if="loading"
            class="flex items-center justify-center py-20 w-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent"></div>
            <span class="ml-3 text-gray-600">
              <p>{{ $t('checkout.initializing') }}</p>
              <span class="text-xs">{{ $t('checkout.please_wait') }}</span>
            </span>
          </div>

          <div v-else-if="unavailable" class="text-center py-20 w-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
            <p class="text-red-600 font-semibold mb-4">
              {{ unavailableProducts ? $t('checkout.unavailable_some') : $t('checkout.unavailable_failed') }}
            </p>

            <div v-if="unavailableProducts"
              class="bg-red-50 border border-red-200 rounded-lg p-4 mx-auto max-w-lg mb-6 text-left">
              <p class="font-semibold text-red-800 mb-2">{{ $t('checkout.remove_to_proceed') }}</p>
              <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                <li v-for="(item, index) in unavailableProducts" :key="index" class="pl-2">
                  {{ $t('checkout.items_unavailable_line', { name: item.name, requested: item.requested, available: item.available }) }}
                </li>
              </ul>
            </div>
            <button @click="initCheckout" class="px-5 py-2 bg-primary text-white rounded hover:bg-mprimary-600 transition">
              {{ $t('checkout.retry') }}
            </button>
          </div>

          <!-- Error -->
          <div v-else-if="error" class="text-center py-20 w-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
            <p class="text-red-600 font-semibold mb-4">{{ error }}</p>
            <button @click="initCheckout" class="px-5 py-2 bg-primary text-white rounded hover:bg-mprimary-600 transition">
              {{ $t('checkout.retry') }}
            </button>
          </div>

          <!-- Success Content -->
          <div v-else>
            <h1 class="lg:text-2xl md:text-xl text-lg font-bold mb-8">{{ $t('checkout.title') }}</h1>

            <!-- Guest Shipping Address Display -->
            <div v-if="isGuest && guestShippingAddress" class="mb-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm lg:text-lg md:text-lg font-semibold">{{ $t('checkout.shipping_address') }}</h3>
                <button @click="openGuestAddressModal"
                  class="px-3 py-1 text-sm bg-primary text-white rounded hover:bg-mprimary-600 transition">
                  {{ $t('checkout.edit') }}
                </button>
              </div>
              
              <div class="bg-white rounded-lg border border-primary bg-mprimary-50 p-4">
                <div class="flex items-start gap-3">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                      <label class="font-semibold text-xs md:text-sm lg:text-sm text-gray-800">
                        {{ guestShippingAddress.full_name }}
                      </label>
                    </div>
                    <p class="text-gray-600 text-xs md:text-sm lg:text-sm">{{ guestShippingAddress.address_line_one }}</p>
                    <p v-if="guestShippingAddress.address_line_two" class="text-gray-600 text-xs md:text-sm lg:text-sm">
                      {{ guestShippingAddress.address_line_two }}
                    </p>
                    <p class="text-gray-600 text-xs md:text-sm lg:text-sm">
                      {{ guestShippingAddress.city }}, {{ guestShippingAddress.state }} {{ guestShippingAddress.postal_code }}
                    </p>
                    <p class="text-gray-500 text-xs md:text-sm lg:text-sm">{{ guestShippingAddress.country }}</p>
                    <p class="text-gray-600 text-xs md:text-sm lg:text-sm font-medium mt-1">{{ guestShippingAddress.phone }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Logged-in User Address Section -->
            <div v-if="!isGuest" class="mb-6">
              <!-- No Address Warning -->
              <div v-if="shippingAddresses.length === 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-yellow-600 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                      clip-rule="evenodd" />
                  </svg>
                  <div class="flex-1">
                    <p class="font-semibold text-yellow-800">{{ $t('checkout.no_address') }}</p>
                    <p class="text-yellow-700 text-sm mt-1">{{ $t('checkout.add_address_prompt') }}</p>
                    <button @click="openAddressModal"
                      class="mt-3 px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition text-sm font-medium">
                      {{ $t('checkout.add_address') }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- Saved Addresses -->
              <div v-else class="space-y-3">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-sm lg:text-lg md:text-lg font-semibold">{{ $t('checkout.shipping_address') }}</h3>
                  <button @click="openAddressModal"
                    class="px-3 py-1 text-sm bg-primary text-white rounded hover:bg-mprimary-600 transition">
                    {{ $t('checkout.add_new') }}
                  </button>
                </div>

                <div v-for="addr in shippingAddresses" :key="addr.id"
                  class="bg-white rounded-lg border p-4 cursor-pointer transition hover:shadow-md"
                  :class="{ 'border-primary bg-mprimary-50': selectedAddressId === addr.id }"
                  @click="selectAddress(addr.id)">
                  <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 flex-1">
                      <input type="radio" :id="`addr-${addr.id}`" :value="addr.id" v-model="selectedAddressId"
                        @change="setDefaultAddress(addr.id)" class="mt-1 cursor-pointer text-primary focus:ring-primary"
                        :aria-label="$t('checkout.aria.select_shipping_for', { name: addr.full_name })" />
                      <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                          <label :for="`addr-${addr.id}`"
                            class="font-semibold text-xs md:text-sm lg:text-sm text-gray-800 cursor-pointer">
                            {{ addr.full_name }}
                          </label>
                          <span v-if="addr.is_default == 1"
                            class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded"
                            :aria-label="$t('checkout.aria.default_address_badge')">
                            {{ $t('checkout.default') }}
                          </span>
                        </div>
                        <p class="text-gray-600 text-xs md:text-sm lg:text-sm">{{ addr.address_line_one }}</p>
                        <p v-if="addr.address_line_two" class="text-gray-600 text-xs md:text-sm lg:text-sm">
                          {{ addr.address_line_two }}
                        </p>
                        <p class="text-gray-600 text-xs md:text-sm lg:text-sm">
                          {{ addr.city }}, {{ addr.state }} {{ addr.postal_code }}
                        </p>
                        <p class="text-gray-500 text-xs md:text-sm lg:text-sm">{{ addr.country }}</p>
                        <p class="text-gray-600 text-xs md:text-sm lg:text-sm font-medium mt-1">{{ addr.phone }}</p>
                      </div>
                    </div>
                    <button @click.stop="editAddress(addr)"
                      class="px-3 py-1 text-sm text-primary hover:text-mprimary-600 hover:bg-mprimary-50 rounded transition">
                      {{ $t('checkout.edit') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Notice & Delivery -->
            <div class="flex gap-x-3 text-xs md:text-sm lg:text-sm">
              <div class="w-7/12 bg-mprimary-50 border border-mprimary-100 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-primary mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd" />
                  </svg>
                  <div>
                    <p class="font-semibold text-secondary">{{ $t('checkout.confirm_items') }}</p>
                    <p class="text-secondary text-sm mt-1">{{ $t('checkout.ensure_correct') }}</p>
                  </div>
                </div>
              </div>
              <div class="w-5/12 bg-gold-50 border border-gold-100 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                  <font-awesome-icon icon="truck" class="text-gold-600" />
                  <div>
                    <p class="font-semibold text-secondary">{{ $t('checkout.estimated_delivery') }}</p>
                    <p v-if="shippingDateRange.text" class="text-secondary mt-1">
                      <span><strong>{{ shippingDateRange.text }}</strong>&nbsp;</span><br class="hidden xs:block sm:block" />
                      <span class="font-medium"><strong>{{ shippingDateRange.range }}</strong></span>
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              <!-- Payment Section -->
              <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                  <h2 class="lg:text-xl md:text-lg text-sm font-semibold mb-4">{{ $t('checkout.select_payment') }}</h2>

                  <div class="space-y-3 relative">
                    <fieldset class="space-y-3 text-xs md:text-sm lg:text-sm">
                      <legend class="sr-only">{{ $t('checkout.payment_options_legend') }}</legend>

                      <label
                        class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                        :class="{ 'border-primary bg-mprimary-50': paymentMethod === 'pay_on_delivery' }">
                        <input v-model="paymentMethod" type="radio" value="pay_on_delivery"
                          class="text-primary focus:ring-primary"
                          :aria-label="$t('checkout.pay_on_delivery')" />
                        <div class="flex-1 flex items-center gap-3">
                          <span class="font-medium">{{ $t('checkout.pay_on_delivery') }}</span>
                        </div>
                      </label>
                    </fieldset>
                  </div>

                  <button @click="placeOrder" 
                    :disabled="(!selectedAddressId && !isGuest) || (isGuest && !guestShippingAddress) || isProcessingPayment"
                    class="mt-6 w-full bg-primary text-white text-sm py-3 rounded font-semibold hover:bg-mprimary-600 transition disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span v-if="isProcessingPayment"
                      class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                    {{ isProcessingPayment ? $t('checkout.processing') : $t('checkout.place_order', { amount: formatAmount(cartData?.payable) }) }}
                  </button>

                  <p v-if="!selectedAddressId && !isGuest" class="text-center text-red-600 text-sm mt-2" role="alert">
                    <span class="inline-block mr-1">⚠️</span> {{ $t('checkout.select_address_alert') }}
                  </p>
                  
                  <p v-if="isGuest && !guestShippingAddress" class="text-center text-red-600 text-sm mt-2" role="alert">
                    <span class="inline-block mr-1">⚠️</span> Please provide shipping address
                  </p>
                </div>
              </div>

              <!-- Order Summary -->
              <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-20">
                  <h3 class="text-sm lg:text-lg md:text-lg font-semibold mb-6">{{ $t('checkout.order_summary') }}</h3>

                  <div class="border-t pt-4 space-y-3 text-xs sm:text-sm md:text-sm lg:text-sm">
                    <div class="flex justify-between font-bold">
                      <span class="text-gray-600">{{ $t('checkout.subtotal') }}</span>
                      <span>₦{{ formatAmount(cartData?.amount) }}</span>
                    </div>

                    <div v-if="cartData?.originalAmount && parseFloat(cartData.originalAmount) > parseFloat(cartData.amount)"
                      class="flex justify-between font-bold text-gray-500 line-through">
                      <span>{{ $t('checkout.original_price') }}</span>
                      <span>₦{{ formatAmount(cartData.originalAmount) }}</span>
                    </div>

                    <div v-if="cartData?.originalAmount && parseFloat(cartData.originalAmount) > parseFloat(cartData.amount)"
                      class="flex justify-between font-bold text-green-600">
                      <span>{{ $t('checkout.saved') }}</span>
                      <span>₦{{ formatAmount(parseFloat(cartData.originalAmount) - parseFloat(cartData.amount)) }}</span>
                    </div>

                    <div class="flex justify-between font-bold">
                      <span class="text-gray-600">{{ $t('checkout.shipping') }}</span>
                      <span>₦{{ formatAmount(cartData?.shippingCost?.cost) }}</span>
                    </div>

                    <div class="flex justify-between font-bold">
                      <span class="text-gray-600">{{ $t('checkout.tax') }}</span>
                      <span>
                        <span class="text-red-600">({{ ((isNaN(cartData?.tax_value) ? 0 : cartData?.tax_value) * 100).toFixed(2) }}%)</span>
                        ₦{{ formatAmount(cartData?.tax) }}
                      </span>
                    </div>

                    <div class="flex justify-between font-bold text-lg border-t pt-3">
                      <span>{{ $t('checkout.total') }}</span>
                      <span class="text-primary">₦{{ formatAmount(cartData?.payable) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Guest Shipping Address Modal (Non-closeable) -->
      <Teleport to="body">
        <div v-if="showGuestModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        @click.self="closeGuestModal">
          <div class="bg-white rounded-lg p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto" role="dialog"
            aria-modal="true" aria-labelledby="guest-address-title">
            <h3 id="guest-address-title" class="text-xl font-bold mb-2">
              Shipping Information Required
            </h3>
            <p class="text-sm text-gray-600 mb-4">Please provide your shipping details to continue with checkout.</p>

            <form @submit.prevent="handleGuestAddressSubmit" class="space-y-3" novalidate>
              <!-- Full Name -->
              <div>
                <label for="guestFullName" class="block text-sm font-medium text-gray-700 mb-1">
                  {{ $t('checkout.full_name') }} <span class="text-red-500" aria-label="required">*</span>
                </label>
                <input v-model="addressForm.full_name" id="guestFullName" type="text" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 text-sm focus:ring-primary focus:border-transparent outline-none"
                  :class="{ 'border-red-500': formErrors.full_name }" :placeholder="$t('checkout.full_name')" />
                <p v-if="formErrors.full_name" class="text-red-600 text-xs mt-1">{{ formErrors.full_name }}</p>
              </div>

              <!-- Phone Number -->
              <div v-if="isGuest" class="flex row gap-x-3">
                <div class="w-1/2">
                  <label for="guestEmail" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $t('checkout.email') }} <span class="text-red-500" aria-label="required">*</span>
                  </label>
                  <input v-model="addressForm.email" id="guestEmail" type="tel" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 text-sm focus:ring-primary focus:border-transparent outline-none"
                    :class="{ 'border-red-500': formErrors.email }" :placeholder="$t('checkout.email_address')" />
                  <p v-if="formErrors.email" class="text-red-600 text-xs mt-1">{{ formErrors.email }}</p>
                </div>
                <div class="w-1/2">
                  <label for="guestPhone" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $t('checkout.phone_number') }} <span class="text-red-500" aria-label="required">*</span>
                  </label>
                  <input v-model="addressForm.phone" id="guestPhone" type="tel" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 text-sm focus:ring-primary focus:border-transparent outline-none"
                    :class="{ 'border-red-500': formErrors.phone }" :placeholder="$t('checkout.phone_number')" />
                  <p v-if="formErrors.phone" class="text-red-600 text-xs mt-1">{{ formErrors.phone }}</p>
                </div>
              </div>
              <div v-else>
                <label for="guestPhone" class="block text-sm font-medium text-gray-700 mb-1">
                  {{ $t('checkout.phone_number') }} <span class="text-red-500" aria-label="required">*</span>
                </label>
                <input v-model="addressForm.phone" id="guestPhone" type="tel" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 text-sm focus:ring-primary focus:border-transparent outline-none"
                  :class="{ 'border-red-500': formErrors.phone }" :placeholder="$t('checkout.phone_number')" />
                <p v-if="formErrors.phone" class="text-red-600 text-xs mt-1">{{ formErrors.phone }}</p>
              </div>


              <!-- Country & State -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $t('checkout.country') }} <span class="text-red-500" aria-label="required">*</span>
                  </label>
                  <select v-model="addressForm.country" id="country" @change="loadStatesProvinces" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 text-sm focus:ring-primary focus:border-transparent outline-none"
                    :class="{ 'border-red-500': formErrors.country }" aria-describedby="country-error">
                    <option value="">{{ $t('checkout.select_country') }}</option>
                    <option v-for="c in countries" :key="c.id" :value="c.country">
                      {{ c.country }}
                    </option>
                  </select>
                  <p v-if="formErrors.country" id="country-error" class="text-red-600 text-xs mt-1">{{
                    formErrors.country }}</p>
                </div>

                <div>
                  <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $t('checkout.state_province') }} <span class="text-red-500" aria-label="required">*</span>
                  </label>
                  <VueMultiselect v-model="addressForm.selectedStateProvince" :options="formattedOptions"
                    :multiple="false" :group-values="'items'" :loading="loadingStates" :group-label="'label'"
                    :searchable="true" :allow-empty="false" :custom-label="option => option.name || option.label"
                    :filter="customFilter" label="name" track-by="code" :placeholder="$t('checkout.select_state_province')"
                    :disabled="!addressForm.country || loadingStates" :class="{ 'multiselect-error': formErrors.state }"
                    aria-describedby="state-error">
                    <template #option="{ option }">
                      <div v-if="option.$groupLabel" class="multiselect__option--group">
                        <strong>{{ option.$groupLabel }}</strong>
                      </div>
                      <div v-else class="multiselect__option--item">
                        - {{ option.name }}
                      </div>
                    </template>
                  </VueMultiselect>
                  <p v-if="formErrors.state" id="state-error" class="text-red-600 text-xs mt-1">{{ formErrors.state }}
                  </p>
                </div>
              </div>

              <!-- City & Postal Code -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $t('checkout.city') }} <span class="text-red-500" aria-label="required">*</span>
                  </label>
                  <input v-model="addressForm.city" id="city" type="text" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 text-sm focus:ring-primary focus:border-transparent outline-none"
                  :class="{ 'border-red-500': formErrors.city }" :placeholder="$t('checkout.city')" aria-describedby="city-error" />
                  <p v-if="formErrors.city" id="city-error" class="text-red-600 text-xs mt-1">{{ formErrors.city }}</p>
                </div>

                <div>
                  <label for="postalCode" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $t('checkout.postal_code') }}
                  </label>
                  <input v-model="addressForm.postal_code" id="postalCode" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 text-sm focus:ring-primary focus:border-transparent outline-none"
                    :placeholder="$t('checkout.postal_code')" />
                </div>
              </div>

              <!-- Address Lines -->
              <div>
                <label for="addressLine1" class="block text-sm font-medium text-gray-700 mb-1">
                  {{ $t('checkout.address_line_1') }} <span class="text-red-500" aria-label="required">*</span>
                </label>
                <input v-model="addressForm.address_line_one" id="addressLine1" type="text" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 text-sm focus:ring-primary focus:border-transparent outline-none"
                  :class="{ 'border-red-500': formErrors.address_line_one }"
                  :placeholder="$t('checkout.address_line_1_ph')" aria-describedby="addressLine1-error" />
                <p v-if="formErrors.address_line_one" id="addressLine1-error" class="text-red-600 text-xs mt-1">{{
                  formErrors.address_line_one }}</p>
              </div>

              <div>
                <label for="addressLine2" class="block text-sm font-medium text-gray-700 mb-1">
                  {{ $t('checkout.address_line_2') }}
                </label>
                <input v-model="addressForm.address_line_two" id="addressLine2" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 text-sm focus:ring-primary focus:border-transparent outline-none"
                  :placeholder="$t('checkout.address_line_2_ph')" />
              </div>

              <div class="flex items-center gap-2 pt-1 text-sm text-gray-600">
                <svg class="w-4 h-4 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                  aria-hidden="true">
                  <path fill-rule="evenodd"
                    d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z"
                    clip-rule="evenodd" />
                </svg>
                <span>{{ $t('checkout.new_will_be_default') }}</span>
              </div>

              <!-- Form Actions -->
              <div class="flex gap-3 mt-6 pt-4 border-t">
                <!-- Cancel only visible when guest already has a saved address (editing mode) -->
                <button
                  v-if="hasGuestAddress"
                  type="button"
                  @click="closeGuestModal"
                  class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium text-secondary"
                >
                  {{ $t('checkout.cancel') }}
                </button>

                <button
                  type="submit"
                  :disabled="isSubmittingForm"
                  class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-mprimary-600 transition font-medium disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                  <span
                    v-if="isSubmittingForm"
                    class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"
                  ></span>
                  {{ $t('checkout.add_address_submit') }}
                </button>
              </div>

            </form>
          </div>
        </div>
      </Teleport>
    </div>
  </DefaultLayout>
</template>

<script setup>
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import { formatAmount, splitFullName } from '@/utils/helpers'
import { clearCart } from '@/utils/neut'
import { ref, computed, onMounted, watch } from 'vue'
import VueMultiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import apiClient from '@/services/api'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const authStore = useAuthStore()
const cartStore = useCartStore()
const { t } = useI18n()
const router = useRouter()
const toast = useToast()

// Constants
const PHONE_REGEX = /^[+]?[\d\s\-()]+$/
const MIN_PHONE_LENGTH = 10
const GUEST_ADDRESS_KEY = 'guestShipping'

// Location Cache
class LocationCache {
  constructor() {
    this.countries = null
    this.statesProvinces = {}
  }

  setCountries(data) {
    this.countries = data
  }

  getCountries() {
    return this.countries
  }

  setStatesProvinces(country, data) {
    this.statesProvinces[country] = data
  }

  getStatesProvinces(country) {
    return this.statesProvinces[country]
  }

  hasCountries() {
    return this.countries !== null
  }

  hasStatesProvinces(country) {
    return this.statesProvinces.hasOwnProperty(country)
  }
}

const locationCache = new LocationCache()

// State
const loading = ref(true)
const error = ref(null)
const unavailable = ref(false)
const unavailableProducts = ref(null)
const showAddressModal = ref(false)
const showGuestModal = ref(false)
const loadingStates = ref(false)
const isSubmittingForm = ref(false)
const isProcessingPayment = ref(false)
const paymentMethod = ref('pay_on_delivery')
const selectedAddressId = ref(null)
const gateways = ref([])
const cartData = ref(null)
const shippingAddresses = ref([])
const countries = ref([])
const statesList = ref([])
const editingAddress = ref(null)
const formErrors = ref({})
const guestShippingAddress = ref(null)
const hasGuestAddress = ref(false)

// Computed
const isGuest = computed(() => !authStore.isAuthenticated)

const shippingDateRange = computed(() => {
  const shippingCost = cartData.value?.shippingCost;
  if (!shippingCost || !shippingCost.min_days || !shippingCost.max_days) {
    return { text: '', range: '' };
  }

  const { min_days: minDays, max_days: maxDays } = shippingCost;

  const addDaysUTC = (days) => {
    const now = new Date()
    const futureDate = new Date(now.getTime() + days * 24 * 60 * 60 * 1000)
    return futureDate
  }

  const dateFormatter = new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    timeZone: 'UTC',
  });

  if (maxDays <= 1) {
    const deliveryDate = addDaysUTC(1);
    return {
      text: t('checkout.delivery.in_day', { count: 1 }),
      range: dateFormatter.format(deliveryDate),
    };
  } else {
    const minDate = addDaysUTC(minDays);
    const maxDate = addDaysUTC(maxDays);
    return {
      text: t('checkout.delivery.in_days', { min: minDays, max: maxDays }),
      range: `${dateFormatter.format(minDate)} - ${dateFormatter.format(maxDate)}`,
    };
  }
});

const formattedOptions = computed(() => {
  if (!Array.isArray(statesList.value) || statesList.value.length === 0) return []

  return statesList.value.map(stateData => {
    const stateLabel = stateData.state
    const provinceItems = (stateData.provinces || []).map(prov => ({
      name: prov.province,
      code: `${stateLabel}|${prov.province}`
    }))

    return {
      label: stateLabel,
      items: provinceItems.length > 0
        ? provinceItems
        : [{ name: stateLabel, code: `${stateLabel}|` }]
    }
  })
})

// Helper Functions
const buildGuestCartPayload = () => {
  return cartStore.getCartItemsList()
}

const fetchGuestSummary = async (addressPayload) => {
  const cart = buildGuestCartPayload()

  if (!cart.length) {
    cartData.value = null
    toast.error(t('checkout.toasts.empty_cart'))
    return
  }

  const payload = {
    cart,
    country: addressPayload.country,
    state: addressPayload.state,
    province: addressPayload.province,
  }

  const response = await apiClient.post('/checkout/guest-summary', payload)

  // assuming same structure as /checkout/init: data.data.user_cart
  const data = response.data
  cartData.value = data?.data?.user_cart || data?.data || null
}


const createEmptyAddressForm = () => ({
  full_name: '',
  phone: '',
  address_line_one: '',
  address_line_two: '',
  province: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
  is_default: true,
  selectedStateProvince: null
})

const addressForm = ref(createEmptyAddressForm())

// Watchers
watch(() => addressForm.value.selectedStateProvince, (val) => {
  if (val?.code) {
    const parts = val.code.split('|')
    addressForm.value.state = parts[0] || ''
    addressForm.value.province = parts[1] || ''
  } else {
    addressForm.value.state = ''
    addressForm.value.province = ''
  }
})

// Validation Functions
const validateAddressForm = () => {
  formErrors.value = {}

  if (!addressForm.value.full_name?.trim()) {
    formErrors.value.full_name = t('checkout.errors.full_name_required')
  }

  if (isGuest.value) {
    // Email required
    if (!addressForm.value.email?.trim()) {
      formErrors.value.email = t('checkout.errors.email_required')
    } 
    // Email format check
    else if (!/^\S+@\S+\.\S+$/.test(addressForm.value.email)) {
      formErrors.value.email = t('checkout.errors.email_invalid')
    }
  }

  if (!addressForm.value.phone?.trim()) {
    formErrors.value.phone = t('checkout.errors.phone_required')
  } else if (!PHONE_REGEX.test(addressForm.value.phone)) {
    formErrors.value.phone = t('checkout.errors.phone_invalid')
  } else if (addressForm.value.phone.replace(/\D/g, '').length < MIN_PHONE_LENGTH) {
    formErrors.value.phone = t('checkout.errors.phone_min_length')
  }

  if (!addressForm.value.country) {
    formErrors.value.country = t('checkout.errors.country_required')
  }

  if (!addressForm.value.selectedStateProvince) {
    formErrors.value.state = t('checkout.errors.state_required')
  }

  if (!addressForm.value.city?.trim()) {
    formErrors.value.city = t('checkout.errors.city_required')
  }

  if (!addressForm.value.address_line_one?.trim()) {
    formErrors.value.address_line_one = t('checkout.errors.address_line1_required')
  }

  return Object.keys(formErrors.value).length === 0
}

// Methods
const initCheckout = async () => {
  loading.value = true
  error.value = null
  unavailable.value = false

  try {
    await loadCountries()

    if (isGuest.value) {
      const savedGuestAddress = localStorage.getItem(GUEST_ADDRESS_KEY)
      
      if (savedGuestAddress) {
        try {
          guestShippingAddress.value = JSON.parse(savedGuestAddress)
          hasGuestAddress.value = true
          await fetchGuestSummary(guestShippingAddress.value)
        } catch (parseError) {
          console.error('Failed to parse saved guest address:', parseError)
          localStorage.removeItem(GUEST_ADDRESS_KEY)
          showGuestModal.value = true
        }
      } else {
        showGuestModal.value = true
      }
      return
    }


    const response = await apiClient.post('/checkout/init', {})
    const data = response.data

    cartData.value = data.data.user_cart
    shippingAddresses.value = data.data.shippingAddresses || []

    const defaultAddr = shippingAddresses.value.find(a => a.is_default)
    selectedAddressId.value = defaultAddr?.id || shippingAddresses.value[0]?.id || null
  } catch (err) {
    console.error('Checkout init error:', err)
    const status = err.response?.status
    const errorData = err.response?.data

    if (status === 422 && errorData?.response === 'unavailable') {
      unavailable.value = true
      unavailableProducts.value = errorData?.errors || null
      toast.error(errorData?.message || t('checkout.toasts.init_failed'))
    } else {
      error.value = errorData?.message || t('checkout.toasts.init_failed')
      toast.error(error.value)
    }
  } finally {
    loading.value = false
  }
}

const loadCountries = async () => {
  try {
    if (locationCache.hasCountries()) {
      countries.value = locationCache.getCountries()
      return
    }

    const response = await apiClient.get('/countries')
    countries.value = response.data.countries || []
    locationCache.setCountries(countries.value)
  } catch (err) {
    console.error('Failed to load countries:', err)
    toast.error(t('checkout.toasts.countries_failed'))
  }
}

const loadStatesProvinces = async () => {
  if (!addressForm.value.country) return

  loadingStates.value = true
  try {
    if (locationCache.hasStatesProvinces(addressForm.value.country)) {
      statesList.value = locationCache.getStatesProvinces(addressForm.value.country)
      loadingStates.value = false
      return
    }

    const response = await apiClient.get(`/states-provinces/${addressForm.value.country}`)
    statesList.value = response.data.data || []
    locationCache.setStatesProvinces(addressForm.value.country, statesList.value)
    addressForm.value.selectedStateProvince = null
  } catch (err) {
    console.error('Failed to load states/provinces:', err)
    statesList.value = []
    toast.error(t('checkout.toasts.states_failed'))
  } finally {
    loadingStates.value = false
  }
}

const customFilter = (option, search, label) => {
  const term = search.toLowerCase()
  if (option.$code && option.$code.toLowerCase().includes(term)) return true
  if (option.name && option.name.toLowerCase().includes(term)) return true
  return false
}

const handleGuestAddressSubmit = async () => {
  if (!validateAddressForm()) return

  isSubmittingForm.value = true

  try {
    const payload = { ...addressForm.value }

    // Persist address locally
    localStorage.setItem(GUEST_ADDRESS_KEY, JSON.stringify(payload))
    guestShippingAddress.value = payload
    hasGuestAddress.value = true

    // Fetch cart summary for this address
    await fetchGuestSummary(payload)

    showGuestModal.value = false
    toast.success('Shipping information saved!')
  } catch (err) {
    console.error('Failed to save guest address:', err)
    
    if (err.response?.status === 422 && err.response?.data?.errors) {
      Object.entries(err.response.data.errors).forEach(([field, messages]) => {
        formErrors.value[field] = Array.isArray(messages) ? messages[0] : messages
      })
      toast.error(t('checkout.toasts.fix_errors_below'))
    } else {
      toast.error(err.response?.data?.message || 'Failed to save shipping information')
    }
  } finally {
    isSubmittingForm.value = false
  }
}

const openGuestAddressModal = () => {
  if (guestShippingAddress.value) {
    addressForm.value = {
      ...guestShippingAddress.value,
      selectedStateProvince: {
        name: guestShippingAddress.value.province || guestShippingAddress.value.state,
        code: `${guestShippingAddress.value.state}|${guestShippingAddress.value.province || ''}`,
      },
    }
    loadStatesProvinces()
  } else {
    addressForm.value = createEmptyAddressForm()
  }

  formErrors.value = {}
  showGuestModal.value = true
}

const closeGuestModal = () => {
  // Guests cannot close until they have at least one saved address
  if (isGuest.value && !hasGuestAddress.value) return

  showGuestModal.value = false
  formErrors.value = {}

  // Reset form back to stored address (if any) when cancelling edit
  if (guestShippingAddress.value) {
    addressForm.value = {
      ...guestShippingAddress.value,
      selectedStateProvince: {
        name: guestShippingAddress.value.province || guestShippingAddress.value.state,
        code: `${guestShippingAddress.value.state}|${guestShippingAddress.value.province || ''}`,
      },
    }
  } else {
    addressForm.value = createEmptyAddressForm()
  }
}

const placeOrder = async () => {
  if (isGuest.value && !guestShippingAddress.value) {
    toast.error('Please provide shipping address')
    return
  }
  
  if (!isGuest.value && !selectedAddressId.value) {
    toast.error(t('checkout.select_address_alert'))
    return
  }

  try {
    isProcessingPayment.value = true

    if (isGuest.value) {
      const cart = buildGuestCartPayload()
      const nameParts = splitFullName(guestShippingAddress.value.full_name)
      const response = await apiClient.post('/checkout/guest-order', {
        ...guestShippingAddress.value,
        ...nameParts, cart,
        payment_method: 'pay_on_delivery'
      })

      const resData = response.data

      if (resData.error) {
        toast.error(resData.message || t('checkout.toasts.order_failed'))
        return
      }

      toast.success(resData.message || t('checkout.toasts.order_placed'))
      clearCart()

      if (resData.orderId) {
        router.push(`/login?email=${guestShippingAddress.value.email}`)
      } else {
        router.push('/dashboard/orders')
      }
    } else {
      const response = await apiClient.post('/place-order', {})
      const resData = response.data

      if (resData.error) {
        toast.error(resData.message || t('checkout.toasts.order_failed'))
        return
      }

      toast.success(resData.message || t('checkout.toasts.order_placed'))
      clearCart()

      if (resData.orderId) {
        router.push(`/order/${resData.orderId}`)
      } else {
        router.push('/dashboard/orders')
      }
    }
  } catch (err) {
    console.error('Place order failed:', err)
    toast.error(err.response?.data?.message || t('checkout.toasts.order_failed'))
  } finally {
    isProcessingPayment.value = false
  }
}

const selectAddress = (addressId) => {
  selectedAddressId.value = addressId
}

const setDefaultAddress = async (addressId) => {
  try {
    await apiClient.post(`/set-default-address/${addressId}`, {})

    shippingAddresses.value = shippingAddresses.value.map(addr => ({
      ...addr,
      is_default: addr.id === addressId
    }))
    toast.success(t('checkout.toasts.default_address_updated'))
  } catch (err) {
    console.error('Failed to set default address:', err)
    toast.error(t('checkout.toasts.default_address_update_failed'))
  }
}

const openAddressModal = () => {
  editingAddress.value = null
  addressForm.value = createEmptyAddressForm()
  formErrors.value = {}
  showAddressModal.value = true
}

const canCloseAddressModal = computed(() => {
  // Guests can't close until they've saved at least one address
  if (isGuest.value && !hasGuestAddress.value) return false
  return true
})

const handleBackdropClose = () => {
  if (canCloseAddressModal.value) {
    closeAddressModal()
  }
}

const handleEscapeClose = () => {
  if (canCloseAddressModal.value) {
    closeAddressModal()
  }
}


const editAddress = (address) => {
  editingAddress.value = address
  addressForm.value = {
    ...address,
    selectedStateProvince: {
      name: address.province,
      code: `${address.state}|${address.province}`
    }
  }
  formErrors.value = {}
  showAddressModal.value = true
  loadStatesProvinces()
}

const closeAddressModal = () => {
  showAddressModal.value = false
  addressForm.value = createEmptyAddressForm()
  formErrors.value = {}
  editingAddress.value = null
}

const handleAddAddress = async () => {
  if (!validateAddressForm()) return

  isSubmittingForm.value = true

  try {
    const method = editingAddress.value ? 'put' : 'post'
    const url = editingAddress.value
      ? `/shipping-address/${editingAddress.value.id}`
      : '/shipping-address'

    const response = await apiClient[method](url, addressForm.value)
    const data = response.data.data

    if (editingAddress.value) {
      toast.success(t('checkout.toasts.address_updated'))
    } else {
      toast.success(t('checkout.toasts.address_added'))
    }

    closeAddressModal()
    await initCheckout()
  } catch (err) {
    console.error('Failed to save address:', err)

    if (err.response?.status === 422 && err.response?.data?.errors) {
      Object.entries(err.response.data.errors).forEach(([field, messages]) => {
        formErrors.value[field] = Array.isArray(messages) ? messages[0] : messages
      })
      toast.error(t('checkout.toasts.fix_errors_below'))
    } else {
      toast.error(err.response?.data?.message || t('checkout.toasts.save_address_failed'))
    }
  } finally {
    isSubmittingForm.value = false
  }
}

onMounted(async () => {
  await initCheckout()
})
</script>

<style scoped>
input[type="radio"],
input[type="checkbox"] {
  cursor: pointer;
}

:deep(.multiselect__tags) {
  min-height: 42px;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  padding: 8px 40px 0 8px;
}

:deep(.multiselect--active .multiselect__tags) {
  border-color: transparent !important;
  box-shadow: 0 0 0 2px #b8974f; /* primary */
}

:deep(.multiselect-error .multiselect__tags) {
  border-color: #ef4444 !important;
}

:deep(.multiselect__content-wrapper) {
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
  margin-top: 4px;
}

:deep(.multiselect__option--highlight) {
  background: #fff9e6; /* gold-50 */
  color: #b67a1f; /* gold-600 */
}

:deep(.multiselect__option--highlight:after) {
  content: none !important;
}

:deep(.multiselect__option--group) {
  color: #6b7280;
  padding: 3px 10px;
  font-size: 14px;
  font-weight: 600;
  background: #f3f4f6;
}

:deep(.multiselect__option--item) {
  padding: 3px 10px;
  font-size: 14px;
}

:deep(.multiselect__option--selected) {
  background-color: #ffedbf; /* gold-100 */
  color: #b67a1f; /* gold-600 */
}

:deep(.multiselect__input::placeholder),
:deep(.multiselect__single) {
  font-size: 14px;
}

:deep(.multiselect__single) {
  padding: 4px 0;
}
</style>

<style scoped>
input[type="radio"],
input[type="checkbox"] {
  cursor: pointer;
}

:deep(.multiselect__tags) {
  min-height: 42px;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  padding: 8px 40px 0 8px;
}

:deep(.multiselect--active .multiselect__tags) {
  border-color: transparent !important;
  box-shadow: 0 0 0 2px #b8974f;
}

:deep(.multiselect-error .multiselect__tags) {
  border-color: #ef4444 !important;
}

:deep(.multiselect__content-wrapper) {
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
  margin-top: 4px;
}

:deep(.multiselect__option--highlight) {
  background: #fff9e6;
  color: #b67a1f;
}

:deep(.multiselect__option--highlight:after) {
  content: none !important;
}

:deep(.multiselect__option--group) {
  color: #6b7280;
  padding: 3px 10px;
  font-size: 14px;
  font-weight: 600;
  background: #f3f4f6;
}

:deep(.multiselect__option--item) {
  padding: 3px 10px;
  font-size: 14px;
}

:deep(.multiselect__option--selected) {
  background-color: #ffedbf;
  color: #b67a1f;
}

:deep(.multiselect__input::placeholder),
:deep(.multiselect__single) {
  font-size: 14px;
}

:deep(.multiselect__single) {
  padding: 4px 0;
}
</style>