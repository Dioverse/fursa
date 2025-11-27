<template>
  <DashboardLayout>
    <div class="space-y-6 relative">
      <!-- Preloader Overlay -->
      <div v-if="loading" class="absolute inset-0 bg-white/80 flex flex-col items-center justify-center z-[1]">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
        <p class="mt-3 text-gray-600 text-sm font-medium">Loading documents...</p>
      </div>
      <h1 class="lg:text-2xl md:text-xl text-lg font-bold">{{ $t('profile.title') }}</h1>

      <!-- REJECTED STATUS ALERT -->
      <div v-if="distributorStatus === 'rejected'" class="bg-red-50 border-l-4 border-red-500 rounded-lg shadow-md p-6">
        <div class="flex items-start gap-4">
          <font-awesome-icon icon="times-circle" class="text-red-600 text-2xl mt-1" />
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-red-800 mb-2">{{ $t('profile.distributor.rejected_title') }}</h3>
            <p class="text-red-700 mb-4">{{ rejectionReason }}</p>
            <p class="text-sm text-red-600">{{ $t('profile.distributor.rejected_message') }}</p>
          </div>
        </div>
      </div>

      <!-- PENDING STATUS ALERT -->
      <div v-else-if="distributorStatus === 'pending'"
        class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg shadow-md p-6">
        <div class="flex items-start gap-4">
          <font-awesome-icon icon="hourglass-half" class="text-yellow-600 text-2xl mt-1" />
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">{{ $t('profile.distributor.pending_title') }}</h3>
            <p class="text-yellow-700">{{ $t('profile.distributor.pending_message') }}</p>
          </div>
        </div>
      </div>

      <!-- APPROVED STATUS BANNER -->
      <div v-else-if="distributorStatus === 'approved'"
        class="bg-green-50 border-l-4 border-green-500 rounded-lg shadow-md p-6">
        <div class="flex items-start gap-4">
          <font-awesome-icon icon="check-circle" class="text-green-600 text-2xl mt-1" />
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-green-800">{{ $t('profile.distributor.approved_title') }}</h3>
            <p class="text-green-700 text-sm">{{ $t('profile.distributor.approved_message') }}</p>
          </div>
        </div>
      </div>

      <!-- USER BASIC INFO (READ-ONLY) -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('profile.distributor.user_info') }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
          <div v-for="(value, label) in userFields" :key="label">
            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1 capitalize">
              {{ label }}
            </label>
            <input :value="value" type="text" disabled
              class="w-full bg-gray-50 px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg cursor-not-allowed" />
          </div>
        </div>
      </div>

      <!-- DISTRIBUTOR DETAILS (APPROVED ONLY - READ-ONLY) -->
      <div v-if="distributorStatus === 'approved'" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('profile.distributor.business_info') }}</h3>
        <div class="space-y-6">
          <!-- Business Information Section -->
          <div>
            <h4 class="font-medium text-gray-800 mb-3 flex items-center gap-2">
              <font-awesome-icon icon="building" />
              {{ $t('profile.distributor.company_details') }}
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
              <ReadOnlyField label="Company Name" :value="distributorData.company_name" />
              <ReadOnlyField label="Registered Name" :value="distributorData.registered_name" />
              <ReadOnlyField label="RC Number" :value="distributorData.rc_number" />
              <ReadOnlyField label="Company Type" :value="distributorData.company_type" />
              <ReadOnlyField label="Business Address" :value="distributorData.business_address" col-span="2" />
              <ReadOnlyField label="Office Phone" :value="distributorData.office_phone" />
              <ReadOnlyField label="Website" :value="distributorData.website || 'N/A'" />
            </div>
          </div>

          <!-- Contact Person Section -->
          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-3 flex items-center gap-2">
              <font-awesome-icon icon="user" />
              {{ $t('profile.distributor.contact_details') }}
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
              <ReadOnlyField label="Contact Full Name" :value="distributorData.contact_full_name" />
              <ReadOnlyField label="Position" :value="distributorData.contact_position" />
              <ReadOnlyField label="Mobile" :value="distributorData.contact_mobile" />
              <ReadOnlyField label="ID Type" :value="distributorData.means_of_id" />
              <ReadOnlyField label="ID Number" :value="distributorData.id_number" />
              <ReadOnlyField label="Years in Business" :value="distributorData.years_in_business" />
            </div>
          </div>

          <!-- Distribution Capacity Section -->
          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-3 flex items-center gap-2">
              <font-awesome-icon icon="boxes" />
              {{ $t('profile.distributor.distribution_capacity') }}
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
              <ReadOnlyField label="Monthly Capacity" :value="`${distributorData.monthly_capacity} units`" />
              <ReadOnlyField label="Number of Sales Staff" :value="distributorData.number_of_sales_staff" />
              <ReadOnlyField label="Has Warehouse" :value="distributorData.has_warehouse ? 'Yes' : 'No'" />
              <ReadOnlyField label="Has Vehicles" :value="distributorData.has_vehicles ? 'Yes' : 'No'" />
              <ReadOnlyField label="Regions Covered" :value="distributorData.regions_covered" col-span="2" />
              <ReadOnlyField label="Preferred States" :value="formatArray(distributorData.preferred_states)"
                col-span="2" />
            </div>
          </div>

          <!-- Product Information Section -->
          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-3 flex items-center gap-2">
              <font-awesome-icon icon="cube" />
              {{ $t('profile.distributor.product_info') }}
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
              <ReadOnlyField label="Product Categories" :value="formatArray(distributorData.product_categories)"
                col-span="2" />
              <ReadOnlyField label="Current Product Lines" :value="distributorData.current_product_lines"
                col-span="2" />
              <ReadOnlyField label="Has Technical Knowledge"
                :value="distributorData.has_technical_knowledge ? 'Yes' : 'No'" />
              <ReadOnlyField label="Willing to Train" :value="distributorData.willing_to_train" />
              <ReadOnlyField label="Distribution Start Time"
                :value="formatDate(distributorData.distribution_start_time)" />
              <ReadOnlyField label="Promo Participation" :value="distributorData.promo_participation" />
            </div>
          </div>

          <!-- Banking Information Section -->
          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-3 flex items-center gap-2">
              <font-awesome-icon icon="bank" />
              {{ $t('profile.distributor.banking_info') }}
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
              <ReadOnlyField label="Bank Name" :value="distributorData.bank_name" />
              <ReadOnlyField label="Account Name" :value="distributorData.account_name" />
              <ReadOnlyField label="Account Number" :value="maskAccountNumber(distributorData.account_number)" />
              <ReadOnlyField label="BVN" :value="maskBVN(distributorData.bvn)" />
            </div>
          </div>

          <!-- Documents Section -->
          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-3 flex items-center gap-2">
              <font-awesome-icon icon="file-alt" />
              {{ $t('profile.distributor.documents') }}
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
              <DocumentLink label="CAC Certificate" :url="distributorData.cac_certificate" />
              <DocumentLink label="Form CO7" :url="distributorData.form_co7" />
              <DocumentLink label="MEMART" :url="distributorData.memart" />
              <DocumentLink label="Utility Bill" :url="distributorData.utility_bill" />
              <DocumentLink label="TIN Certificate" :url="distributorData.tin_certificate" />
              <DocumentLink label="ID of Contact" :url="distributorData.id_of_contact" />
              <DocumentLink label="Referee Letter" :url="distributorData.referee_letter" />
              <DocumentLink label="Signature" :url="distributorData.signature" />
            </div>
          </div>
        </div>
      </div>

      <!-- EDITABLE FORM (REJECTED STATUS ONLY) -->
      <div v-else-if="distributorStatus === 'rejected'" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('profile.distributor.update_application') }}</h3>

        <form @submit.prevent="submitUpdatedApplication" class="space-y-6">
          <div>
            <h4 class="font-medium text-gray-800 mb-4 flex items-center gap-2">
              <font-awesome-icon icon="building" />
              Business Information
            </h4>
            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Company Name</label>
                  <input v-model="editableData.company_name" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Registered Name</label>
                  <input v-model="editableData.registered_name" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">RC Number</label>
                  <input v-model="editableData.rc_number" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Company Type</label>
                  <input v-model="editableData.company_type" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Email</label>
                  <input v-model="editableData.email" type="email"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Office Phone</label>
                  <input v-model="editableData.office_phone" type="tel"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Website (Optional)</label>
                  <input v-model="editableData.website" type="url"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
              </div>

              <div>
                <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Business Address</label>
                <textarea v-model="editableData.business_address" rows="3"
                  class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
              </div>
            </div>
          </div>

          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-4 flex items-center gap-2">
              <font-awesome-icon icon="user" />
              Contact Person
            </h4>
            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Full Name</label>
                  <input v-model="editableData.contact_full_name" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Position</label>
                  <input v-model="editableData.contact_position" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Mobile</label>
                  <input v-model="editableData.contact_mobile" type="tel"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">ID Type</label>
                  <input v-model="editableData.means_of_id" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">ID Number</label>
                  <input v-model="editableData.id_number" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Years in Business</label>
                  <input v-model="editableData.years_in_business" type="number"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
              </div>
            </div>
          </div>

          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-4 flex items-center gap-2">
              <font-awesome-icon icon="boxes" />
              Distribution Capacity
            </h4>
            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Monthly Capacity</label>
                  <input v-model="editableData.monthly_capacity" type="number"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Number of Sales Staff</label>
                  <input v-model="editableData.number_of_sales_staff" type="number"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Has Warehouse</label>
                  <select v-model="editableData.has_warehouse"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option :value="null">Select</option>
                    <option :value="1">Yes</option>
                    <option :value="0">No</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Has Vehicles</label>
                  <select v-model="editableData.has_vehicles"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option :value="null">Select</option>
                    <option :value="1">Yes</option>
                    <option :value="0">No</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Regions Covered</label>
                  <input v-model="editableData.regions_covered" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Preferred Region</label>
                  <input v-model="editableData.preferred_region" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
              </div>

              <div v-if="editableData.has_vehicles">
                <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Vehicle Details
                  (Optional)</label>
                <textarea v-model="editableData.vehicle_details" rows="2"
                  class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
              </div>
            </div>
          </div>

          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-4 flex items-center gap-2">
              <font-awesome-icon icon="cube" />
              Product Information
            </h4>
            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Current Product Lines</label>
                  <textarea v-model="editableData.current_product_lines" rows="2" placeholder="Comma separated list"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Partnerships (Optional)</label>
                  <textarea v-model="editableData.partnerships" rows="2"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Has Technical Knowledge</label>
                  <select v-model="editableData.has_technical_knowledge"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option :value="null">Select</option>
                    <option :value="1">Yes</option>
                    <option :value="0">No</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Willing to Train</label>
                  <select v-model="editableData.willing_to_train" type="text" placeholder="e.g., Yes, No, Depends"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="depends">Depends on Arrangement</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Distribution Start Time</label>
                  <input v-model="editableData.distribution_start_time" type="date"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Promo Participation</label>
                  <input v-model="editableData.promo_participation" type="text" placeholder="e.g., Yes, No"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
              </div>
            </div>
          </div>

          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-4 flex items-center gap-2">
              <font-awesome-icon icon="bank" />
              Banking & KYC
            </h4>
            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                  <input v-model="editableData.bank_name" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Account Name</label>
                  <input v-model="editableData.account_name" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Account Number</label>
                  <input v-model="editableData.account_number" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">BVN</label>
                  <input v-model="editableData.bvn" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
              </div>
            </div>
          </div>

          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-4 flex items-center gap-2">
              <font-awesome-icon icon="file-alt" />
              Declaration
            </h4>
            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Declarant Name</label>
                  <input v-model="editableData.declarant_name" type="text"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
                <div>
                  <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">Declaration Date</label>
                  <input v-model="editableData.declaration_date" type="date"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
                </div>
              </div>
            </div>
          </div>

          <div class="border-t pt-6">
            <h4 class="font-medium text-gray-800 mb-4 flex items-center gap-2">
              <font-awesome-icon icon="file-alt" />
              Documents
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-6">
              <DocumentUpload label="CAC Certificate" @file-selected="(file) => editableData.cac_certificate = file"
                :current-file="distributorData.cac_certificate" />
              <DocumentUpload label="Form CO7" @file-selected="(file) => editableData.form_co7 = file"
                :current-file="distributorData.form_co7" />
              <DocumentUpload label="MEMART" @file-selected="(file) => editableData.memart = file"
                :current-file="distributorData.memart" />
              <DocumentUpload label="Utility Bill" @file-selected="(file) => editableData.utility_bill = file"
                :current-file="distributorData.utility_bill" />
              <DocumentUpload label="TIN Certificate" @file-selected="(file) => editableData.tin_certificate = file"
                :current-file="distributorData.tin_certificate" />
              <DocumentUpload label="ID of Contact" @file-selected="(file) => editableData.id_of_contact = file"
                :current-file="distributorData.id_of_contact" />
              <DocumentUpload label="Referee Letter" @file-selected="(file) => editableData.referee_letter = file"
                :current-file="distributorData.referee_letter" />
              <DocumentUpload label="Signature" @file-selected="(file) => editableData.signature = file"
                :current-file="distributorData.signature" />
            </div>
          </div>

          <div class="flex justify-end gap-4 pt-4 border-t">
            <button type="button" @click="resetEditableData"
              class="text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
              {{ $t('profile.buttons.cancel') }}
            </button>
            <button type="submit" :disabled="submittingUpdate"
              class="text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition disabled:opacity-50">
              <font-awesome-icon v-if="submittingUpdate" icon="spinner" spin class="mr-2" />
              <font-awesome-icon v-else icon="save" class="mr-2" />
              {{ $t('profile.buttons.save_changes') }}
            </button>
          </div>
        </form>
      </div>

      <!-- PASSWORD UPDATE FORM (ALWAYS VISIBLE) -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('profile.password.title') }}</h3>

        <form @submit.prevent="submitPassword" class="space-y-4">
          <div>
            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
              {{ $t('profile.password.current') }}
            </label>
            <input v-model="passwordForm.current" type="password" placeholder="Enter current password"
              class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
          </div>

          <div>
            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
              {{ $t('profile.password.new') }}
            </label>
            <input v-model="passwordForm.new" type="password" placeholder="Enter new password"
              class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
          </div>

          <div>
            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
              {{ $t('profile.password.confirm') }}
            </label>
            <input v-model="passwordForm.confirm" type="password" placeholder="Confirm new password"
              class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" />
          </div>

          <div class="flex justify-end gap-4 pt-4 border-t mt-4">
            <button type="button" @click="resetPasswordForm"
              class="text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
              {{ $t('profile.buttons.cancel') }}
            </button>
            <button type="submit" :disabled="submittingPassword"
              class="text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition disabled:opacity-50">
              <font-awesome-icon v-if="submittingPassword" icon="spinner" spin class="mr-2" />
              <font-awesome-icon v-else icon="key" class="mr-2" />
              {{ $t('profile.buttons.update_password') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { reactive, computed, ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'
import { useRouter } from 'vue-router'
import { getLink } from '@/utils/helpers'

const toast = useToast()
const authStore = useAuthStore()
const { t } = useI18n()
const router = useRouter()
const user = computed(() => authStore.user)

// STATE
const loading = ref(false)
const submittingUpdate = ref(false)
const submittingPassword = ref(false)
const distributorData = ref({})
const distributorStatus = ref(null)
const rejectionReason = ref('')
const editableData = ref({})

// PASSWORD FORM
const passwordForm = reactive({
  current: '',
  new: '',
  confirm: ''
})

// COMPUTED
const userFields = computed(() => ({
  'First Name': user.value?.first_name || '',
  'Last Name': user.value?.last_name || '',
  'Email': user.value?.email || '',
  'Phone': user.value?.phone || ''
}))

// HOOKS
onMounted(() => fetchDistributorDetails())

// METHODS
const fetchDistributorDetails = async () => {
  loading.value = true
  try {
    const response = await api.get('/distributor/profile-details')
    const userData = response.data.user

    if (userData.distributor) {
      distributorData.value = userData.distributor
      distributorStatus.value = userData.status
      rejectionReason.value = userData.distributor.reason || ''

      if (distributorStatus.value === 'rejected') {
        editableData.value = initializeEditableData(userData.distributor)
      }
    }
  } catch (error) {
    console.error('Error fetching distributor details:', error)
    toast.error('Failed to load distributor details')
  } finally {
    loading.value = false
  }
}

const initializeEditableData = (distributor) => ({
  company_name: distributor.company_name || '',
  registered_name: distributor.registered_name || '',
  rc_number: distributor.rc_number || '',
  email: distributor.email || '',
  business_address: distributor.business_address || '',
  office_phone: distributor.office_phone || '',
  website: distributor.website || '',
  company_type: distributor.company_type || '',
  contact_full_name: distributor.contact_full_name || '',
  contact_position: distributor.contact_position || '',
  contact_mobile: distributor.contact_mobile || '',
  id_number: distributor.id_number || '',
  means_of_id: distributor.means_of_id || '',
  years_in_business: distributor.years_in_business || 0,
  monthly_capacity: distributor.monthly_capacity || 0,
  regions_covered: distributor.regions_covered || '',
  number_of_sales_staff: distributor.number_of_sales_staff || 0,
  has_warehouse: distributor.has_warehouse ? 1 : 0,
  preferred_region: distributor.preferred_region || '',
  has_vehicles: distributor.has_vehicles ? 1 : 0,
  vehicle_details: distributor.vehicle_details || '',
  preferred_states: distributor.preferred_states || [],
  product_categories: distributor.product_categories || [],
  current_product_lines: distributor.current_product_lines || '',
  willing_to_train: distributor.willing_to_train || '',
  has_technical_knowledge: distributor.has_technical_knowledge ? 1 : 0,
  distribution_start_time: distributor.distribution_start_time || '',
  promo_participation: distributor.promo_participation || '',
  bank_name: distributor.bank_name || '',
  account_name: distributor.account_name || '',
  account_number: distributor.account_number || '',
  bvn: distributor.bvn || '',
  partnerships: distributor.partnerships || '',
  declarant_name: distributor.declarant_name || '',
  declaration_date: distributor.declaration_date || '',
  cac_certificate: null,
  form_co7: null,
  memart: null,
  utility_bill: null,
  tin_certificate: null,
  id_of_contact: null,
  referee_letter: null,
  signature: null
})

const submitUpdatedApplication = async () => {
  submittingUpdate.value = true
  loading.value = true
  try {
    const formData = new FormData()

    // Add all text fields
    const textFields = {
      company_name: editableData.value.company_name,
      registered_name: editableData.value.registered_name,
      rc_number: editableData.value.rc_number,
      email: editableData.value.email,
      business_address: editableData.value.business_address,
      office_phone: editableData.value.office_phone,
      website: editableData.value.website,
      company_type: editableData.value.company_type,
      contact_full_name: editableData.value.contact_full_name,
      contact_position: editableData.value.contact_position,
      contact_mobile: editableData.value.contact_mobile,
      id_number: editableData.value.id_number,
      means_of_id: editableData.value.means_of_id,
      years_in_business: editableData.value.years_in_business,
      monthly_capacity: editableData.value.monthly_capacity,
      regions_covered: editableData.value.regions_covered,
      number_of_sales_staff: editableData.value.number_of_sales_staff,
      has_warehouse: Boolean(editableData.value.has_warehouse),
      preferred_region: editableData.value.preferred_region,
      has_vehicles: Boolean(editableData.value.has_vehicles),
      vehicle_details: editableData.value.vehicle_details,
      current_product_lines: editableData.value.current_product_lines,
      willing_to_train: editableData.value.willing_to_train,
      has_technical_knowledge: Boolean(editableData.value.has_technical_knowledge),
      distribution_start_time: editableData.value.distribution_start_time,
      promo_participation: editableData.value.promo_participation,
      bank_name: editableData.value.bank_name,
      account_name: editableData.value.account_name,
      account_number: editableData.value.account_number,
      bvn: editableData.value.bvn,
      partnerships: editableData.value.partnerships,
      declarant_name: editableData.value.declarant_name,
      declaration_date: editableData.value.declaration_date
    }

    // Add text fields to FormData
    Object.keys(textFields).forEach(key => {
      formData.append(key, textFields[key])
    })

    // Add documents if they exist
    const documentFields = [
      'cac_certificate',
      'form_co7',
      'memart',
      'utility_bill',
      'tin_certificate',
      'id_of_contact',
      'referee_letter',
      'signature'
    ]

    documentFields.forEach(field => {
      const file = editableData.value[field]
      if (file instanceof File) {
        formData.append(field, file)
      }
    })

    await api.post('/distributor/update-application', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    toast.success('Profile updated successfully! Please wait for approval.')
    resetEditableData()
    await fetchDistributorDetails()
  } catch (error) {
    handleError(error)
  } finally {
    submittingUpdate.value = false
    loading.value = false
  }
}

const submitPassword = async () => {
  if (!passwordForm.current || !passwordForm.new || !passwordForm.confirm) {
    toast.error(t('profile.toasts.fill_all_password_fields'))
    return
  }

  if (passwordForm.new !== passwordForm.confirm) {
    toast.error(t('profile.toasts.passwords_no_match'))
    return
  }

  submittingPassword.value = true
  try {
    await api.post('/change-password', {
      current_password: passwordForm.current,
      new_password: passwordForm.new,
      new_password_confirmation: passwordForm.confirm
    })

    toast.success(t('profile.toasts.password_updated'))
    resetPasswordForm()
    authStore.logout(false)
    router.push('/login')
  } catch (err) {
    toast.error(err.response?.data?.message || 'Password update failed')
  } finally {
    submittingPassword.value = false
  }
}

const resetEditableData = () => {
  if (distributorStatus.value === 'rejected') {
    editableData.value = initializeEditableData(distributorData.value)
  }
}

const resetPasswordForm = () => {
  passwordForm.current = ''
  passwordForm.new = ''
  passwordForm.confirm = ''
}

const handleError = (error) => {
  console.error('Update error:', error)
  if (error.response?.status === 422) {
    const errors = error.response.data.errors
    const firstErrorKey = Object.keys(errors)[0]
    const firstError = errors[firstErrorKey][0]
    toast.error(`${firstErrorKey}: ${firstError}`)
  } else {
    toast.error(error.response?.data?.message || 'Failed to update')
  }
}

// UTILITY HELPERS
const formatArray = (arr) => !arr || !Array.isArray(arr) ? 'N/A' : arr.join(', ')
const formatDate = (dateString) => !dateString ? 'N/A' : new Date(dateString).toLocaleDateString()
const maskAccountNumber = (accountNumber) => {
  if (!accountNumber) return 'N/A'
  const str = accountNumber.toString()
  return str.slice(0, 2) + '****' + str.slice(-2)
}
const maskBVN = (bvn) => {
  if (!bvn) return 'N/A'
  const str = bvn.toString()
  return str.slice(0, 3) + '****' + str.slice(-3)
}
</script>

<script>
// Component for read-only field display
import { defineComponent, h } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faDownload, faEye, faTimes } from '@fortawesome/free-solid-svg-icons'
import { getLink } from '@/utils/helpers'

export const ReadOnlyField = defineComponent({
  props: {
    label: String,
    value: [String, Number],
    colSpan: {
      type: Number,
      default: 1
    }
  },
  render() {
    const colSpanClass = this.colSpan === 2 ? 'sm:col-span-2' : ''
    return h('div', { class: colSpanClass }, [
      h('label', { class: 'block text-xs lg:text-sm font-medium text-gray-700 mb-1' }, this.label),
      h('input', {
        value: this.value || 'N/A',
        type: 'text',
        disabled: true,
        class: 'w-full bg-gray-50 px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg cursor-not-allowed'
      })
    ])
  }
})

export const DocumentLink = defineComponent({
  props: {
    label: String,
    url: String
  },
  setup(props) {
    const showModal = ref(false)
    const openModal = () => (showModal.value = true)
    const closeModal = () => (showModal.value = false)

    return { showModal, openModal, closeModal }
  },
  render() {
    const link = this.url ? getLink(this.url) : null

    return h('div', { class: 'relative' }, [
      // Label
      h('label', { class: 'block text-xs lg:text-sm font-medium text-gray-700 mb-2' }, this.label),

      // When file is uploaded
      this.url
        ? h('div', { class: 'flex items-center gap-3' }, [
          // View button
          h(
            'button',
            {
              onClick: this.openModal,
              class:
                'inline-flex items-center gap-2 px-3 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-xs',
            },
            [
              h(FontAwesomeIcon, { icon: 'eye' }),
              'View',
            ]
          ),

          // Download button
          h(
            'a',
            {
              href: link,
              target: '_blank',
              download: true,
              class:
                'inline-flex items-center gap-2 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-xs',
            },
            [
              h(FontAwesomeIcon, { icon: 'download' }),
              'Download',
            ]
          ),

          // Modal
          this.showModal
            ? h('div', {
              class:
                'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm',
              onClick: this.closeModal,
            }, [
              h('div', {
                class:
                  'bg-white rounded-lg overflow-hidden w-11/12 md:w-3/4 lg:w-1/2 h-[80vh] relative',
                onClick: e => e.stopPropagation(),
              }, [
                // Close button
                h('button', {
                  class:
                    'absolute bottom-3 right-5 text-white hover:bg:black/90 bg-black/50 px-3 py-2 rounded-full',
                  onClick: this.closeModal,
                }, '×'),

                // File iframe
                h('iframe', {
                  src: link,
                  class: 'w-full h-full',
                  frameborder: '0',
                }),
              ]),
            ])
            : null,
        ])
        : h('span', { class: 'text-gray-400 text-xs' }, 'Not Uploaded'),
    ])
  },
})

export const DocumentUpload = defineComponent({
  props: {
    label: String,
    currentFile: String
  },
  emits: ['file-selected'],
  components: { FontAwesomeIcon },
  setup(props, { emit }) {
    const fileName = ref(null)
    const showModal = ref(false)

    const handleFileChange = (event) => {
      const file = event.target.files?.[0]
      if (file) {
        fileName.value = file.name
        emit('file-selected', file)
      }
    }

    return () => h('div', [
      // Label
      h('label', { class: 'block text-xs lg:text-sm font-medium text-gray-700 mb-2' }, props.label),

      // Upload row
      h('div', { class: 'flex items-center gap-2' }, [
        h('input', {
          type: 'file',
          onChange: handleFileChange,
          class: 'flex-1 text-xs px-3 py-2 lg:px-4 lg:py-3 border rounded-lg cursor-pointer'
        }),

        // Status or Actions
        props.currentFile
          ? h('div', { class: 'flex items-center gap-2' }, [
            // View icon
            h('button', {
              class: 'text-blue-600 hover:text-blue-800 transition',
              title: 'View Document',
              onClick: () => (showModal.value = true)
            }, [h(FontAwesomeIcon, { icon: faEye })]),

            // Download icon
            h('a', {
              href: getLink(props.currentFile),
              download: true,
              target: '_blank',
              class: 'text-green-600 hover:text-green-800 transition',
              title: 'Download Document'
            }, [h(FontAwesomeIcon, { icon: faDownload })])
          ])
          : h('span', { class: 'text-gray-400 text-xs' }, 'No file uploaded')
      ]),

      // Selected filename
      fileName.value && h('p', { class: 'text-xs text-gray-500 mt-1' }, fileName.value),

      // Modal
      showModal.value &&
      h('div', {
        class: 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm'
      }, [
        h('div', {
          class: 'bg-white rounded-lg overflow-hidden w-11/12 md:w-3/4 lg:w-1/2 h-[80vh] relative'
        }, [
          // Close button
          h('button', {
            class: 'absolute bottom-3 right-5 text-white hover:bg:black/90 bg-black/50 px-3 py-2 rounded-full',
            onClick: () => (showModal.value = false)
          }, [h(FontAwesomeIcon, { icon: faTimes })]),

          // Iframe
          h('iframe', {
            src: getLink(props.currentFile),
            class: 'w-full h-[80vh] rounded-b-lg',
            frameborder: '0'
          })
        ])
      ])
    ])
  }
})
</script>