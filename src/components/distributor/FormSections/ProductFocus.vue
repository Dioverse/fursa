<!-- ProductFocus with Store -->
<template>
    <div class="space-y-6">
    <h3 class="text-xl font-semibold text-primary mb-4">{{ $t('distributor.sections.product_focus') }}</h3>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ $t('distributor.product_focus.categories_question') }} <span class="text-red-500">*</span>
            </label>
            <div v-if="loadingCategories" class="text-sm text-gray-500 mb-2">Loading categories...</div>
            <div v-else class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <label v-for="cat in categories" :key="cat.value"
                    class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input v-model="distributorStore.formData.productFocus.product_categories" type="checkbox" :value="cat.value"
                        class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="truncate">{{ cat.label }}</span>
                </label>
            </div>
            <p v-if="categoriesError" class="text-xs text-red-600 mt-2">{{ categoriesError }}</p>
            <div v-if="distributorStore.formData.productFocus.product_categories.includes('other')" class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $t('distributor.product_focus.other_specify_label') }}
                </label>
                <input v-model="distributorStore.formData.productFocus.other_specify" type="text" :placeholder="$t('distributor.product_focus.other_specify_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ $t('distributor.product_focus.technical_knowledge_question') }} <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.productFocus.has_technical_knowledge" type="radio" :value="1"
                        class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.yes') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.productFocus.has_technical_knowledge" type="radio" :value="0"
                        class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.no') }}</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ $t('distributor.product_focus.training_question') }} <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.productFocus.willing_to_train" type="radio" :value="1" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.yes') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.productFocus.willing_to_train" type="radio" :value="0" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.no') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.productFocus.willing_to_train" type="radio" value="depends" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.depends') }}</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('distributor.product_focus.distribution_start_question') }} <span class="text-red-500">*</span>
            </label>
            <input v-model="distributorStore.formData.productFocus.distribution_start_time" type="date"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ $t('distributor.product_focus.promo_question') }}
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.productFocus.promo_participation" type="radio" value="Yes" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.yes') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.productFocus.promo_participation" type="radio" value="No" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.no') }}</span>
                </label>
            </div>
        </div>

        <!-- Required Documents Section -->
        <div class="border-t pt-6">
            <h4 class="font-semibold text-gray-800 mb-4">
                <font-awesome-icon icon="file-upload" class="text-primary mr-2" />
                {{ $t('distributor.product_focus.upload_title') }} <span class="text-red-500">*</span>
            </h4>
            <p class="text-sm text-gray-600 mb-4">All documents are required for application submission.</p>
            
            <div class="space-y-4">
                <div v-for="doc in documents" :key="doc.name" 
                    class="border-2 border-dashed rounded-lg p-4"
                    :class="{ 'border-red-300 bg-red-50': uploadErrors[doc.name] }">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="font-medium">{{ $t(doc.labelKey) }} <span class="text-red-500">*</span></p>
                            <p class="text-sm text-gray-500">{{ $t(doc.descriptionKey) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ doc.accept }}</p>
                        </div>
                        <label class="cursor-pointer">
                            <input type="file" :accept="doc.accept" @change="handleFileUpload($event, doc.name)"
                                class="hidden">
                            <div
                                class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded hover:bg-gray-200 transition whitespace-nowrap ml-4">
                                <font-awesome-icon icon="upload" />
                                <span>{{ $t('distributor.product_focus.choose_file') }}</span>
                            </div>
                        </label>
                    </div>
                    <div v-if="distributorStore.formData.productFocus.documents[doc.name]" class="text-sm text-green-600 mt-2">
                        <font-awesome-icon icon="check-circle" />
                        {{ distributorStore.formData.productFocus.documents[doc.name].name }}
                    </div>
                    <div v-if="uploadErrors[doc.name]" class="text-sm text-red-600 mt-2">
                        <font-awesome-icon icon="exclamation-circle" />
                        {{ uploadErrors[doc.name] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="border-t pt-6">
            <h4 class="font-semibold text-gray-800 mb-4">
                <font-awesome-icon icon="pen-fancy" class="text-primary mr-2" />
                {{ $t('distributor.product_focus.signature_title') }} <span class="text-red-500">*</span>
            </h4>
            <p class="text-sm text-gray-600 mb-3">Please upload an image of your signature or a scanned signature document.</p>
            
            <div class="border-2 border-dashed rounded-lg p-4"
                :class="{ 'border-red-300 bg-red-50': uploadErrors['signature'] }">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="font-medium">Signature Document <span class="text-red-500">*</span></p>
                        <p class="text-sm text-gray-500">Accepted formats: PDF, JPG, PNG (Max 5MB)</p>
                    </div>
                    <label class="cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="handleFileUpload($event, 'signature')"
                            class="hidden">
                        <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded hover:bg-gray-200 transition whitespace-nowrap ml-4">
                            <font-awesome-icon icon="upload" />
                            <span>{{ $t('distributor.product_focus.choose_file') }}</span>
                        </div>
                    </label>
                </div>
                <div v-if="distributorStore.formData.productFocus.documents['signature']" class="text-sm text-green-600 mt-2">
                    <font-awesome-icon icon="check-circle" />
                    {{ distributorStore.formData.productFocus.documents['signature'].name }}
                </div>
                <div v-if="uploadErrors['signature']" class="text-sm text-red-600 mt-2">
                    <font-awesome-icon icon="exclamation-circle" />
                    {{ uploadErrors['signature'] }}
                </div>
            </div>
        </div>

        <!-- Declaration Section -->
        <div class="border-t pt-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <p class="text-sm">
                    <font-awesome-icon icon="exclamation-triangle" class="text-yellow-600 mr-2" />
                    {{ $t('distributor.product_focus.declaration_warning') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $t('distributor.product_focus.declarant_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input v-model="distributorStore.formData.productFocus.declarant_name" type="text" :placeholder="$t('distributor.product_focus.declarant_name_placeholder')"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $t('distributor.product_focus.declaration_date') }} <span class="text-red-500">*</span>
                    </label>
                    <input v-model="distributorStore.formData.productFocus.declaration_date" type="date"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        required>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useDistributorFormStore } from '@/stores/distributorForm'
import productsService from '@/services/products.service.js'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

const distributorStore = useDistributorFormStore()
const { t } = useI18n()
const toast = useToast()

const categories = ref([])
const loadingCategories = ref(false)
const categoriesError = ref('')
const labelsMap = ref({})
const uploadErrors = ref({})

const documents = [
    {
        name: 'cac',
        labelKey: 'distributor.docs.cac',
        descriptionKey: 'distributor.docs.cac_desc',
        accept: 'PDF, JPG, PNG'
    },
    {
        name: 'form_c07',
        labelKey: 'distributor.docs.form_c07',
        descriptionKey: 'distributor.docs.form_c07_desc',
        accept: 'PDF, JPG, PNG'
    },
    {
        name: 'memart',
        labelKey: 'distributor.docs.memart',
        descriptionKey: 'distributor.docs.memart_desc',
        accept: 'PDF, JPG, PNG'
    },
    {
        name: 'tin',
        labelKey: 'distributor.docs.tin',
        descriptionKey: 'distributor.docs.tin_desc',
        accept: 'PDF, JPG, PNG'
    },
    {
        name: 'referee',
        labelKey: 'distributor.docs.referee',
        descriptionKey: 'distributor.docs.referee_desc',
        accept: 'PDF, JPG, PNG'
    }
]

const MAX_FILE_SIZE = 5 * 1024 * 1024 // 5MB

const handleFileUpload = (event, docName) => {
    const file = event.target.files[0]
    uploadErrors.value[docName] = ''

    if (!file) {
        delete distributorStore.formData.productFocus.documents[docName]
        return
    }

    // Validate file size
    if (file.size > MAX_FILE_SIZE) {
        uploadErrors.value[docName] = `File size must be less than 5MB. Your file is ${(file.size / 1024 / 1024).toFixed(2)}MB`
        return
    }

    // Validate file type
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
    if (!allowedTypes.includes(file.type)) {
        uploadErrors.value[docName] = 'Only PDF, JPG, and PNG files are allowed'
        return
    }

    distributorStore.formData.productFocus.documents[docName] = file
    toast.info(`${docName.toUpperCase()} uploaded successfully`)
}

const loadCategories = async () => {
    loadingCategories.value = true
    categoriesError.value = ''
    try {
        const resp = await productsService.getDynamicCategories()
        const raw = resp?.data?.data || resp?.data || []
        const mapped = Array.isArray(raw)
            ? raw.map((c) => ({
                value: c.slug || String(c.id),
                label: c.name || c.title || (c.slug ? c.slug.replace(/[-_]/g, ' ') : String(c.id)),
              }))
            : []

        mapped.push({ value: 'other', label: t('distributor.product_focus.categories.other') })

        categories.value = mapped
        labelsMap.value = mapped.reduce((acc, it) => { acc[it.value] = it.label; return acc }, {})
        distributorStore.formData.productFocus.product_categories_display = (distributorStore.formData.productFocus.product_categories || []).map(v => labelsMap.value[v] || v)
    } catch (e) {
        console.error('Failed to load categories', e)
        categoriesError.value = 'Failed to load categories.'
        const fallback = [{ value: 'other', label: t('distributor.product_focus.categories.other') }]
        categories.value = fallback
        labelsMap.value = { other: fallback[0].label }
        toast.error('Failed to load categories')
    } finally {
        loadingCategories.value = false
    }
}

onMounted(loadCategories)

watch(() => distributorStore.formData.productFocus.product_categories, (vals) => {
    distributorStore.formData.productFocus.product_categories_display = (vals || []).map(v => labelsMap.value[v] || v)
}, { deep: true })

defineExpose({ form: distributorStore.formData.productFocus })
</script>