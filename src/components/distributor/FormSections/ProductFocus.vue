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
                    <input v-model="form.product_categories" type="checkbox" :value="cat.value"
                        class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="truncate">{{ cat.label }}</span>
                </label>
            </div>
            <p v-if="categoriesError" class="text-xs text-red-600 mt-2">{{ categoriesError }}</p>
            <div v-if="form.product_categories.includes('other')" class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $t('distributor.product_focus.other_specify_label') }}
                </label>
                <input v-model="form.other_specify" type="text" :placeholder="$t('distributor.product_focus.other_specify_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ $t('distributor.product_focus.technical_knowledge_question') }} <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.has_technical_knowledge" type="radio" :value="1"
                        class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.yes') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.has_technical_knowledge" type="radio" :value="0"
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
                    <input v-model="form.willing_to_train" type="radio" :value="1" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.yes') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.willing_to_train" type="radio" :value="0" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.no') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.willing_to_train" type="radio" value="depends" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.depends') }}</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('distributor.product_focus.distribution_start_question') }} <span class="text-red-500">*</span>
            </label>
            <input v-model="form.distribution_start_time" type="date"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ $t('distributor.product_focus.promo_question') }}
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.promo_participation" type="radio" value="Yes" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.yes') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.promo_participation" type="radio" value="No" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.no') }}</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-4">
                {{ $t('distributor.product_focus.upload_title') }}
            </label>
            <div class="space-y-4">
                <div v-for="doc in documents" :key="doc.name" class="border-2 border-dashed rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ $t(doc.labelKey) }}</p>
                            <p class="text-sm text-gray-500">{{ $t(doc.descriptionKey) }}</p>
                        </div>
                        <label class="cursor-pointer">
                            <input type="file" :accept="doc.accept" @change="handleFileUpload($event, doc.name)"
                                class="hidden">
                            <div
                                class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded hover:bg-gray-200 transition">
                                <font-awesome-icon icon="upload" />
                                <span>{{ $t('distributor.product_focus.choose_file') }}</span>
                            </div>
                        </label>
                    </div>
                    <p v-if="form.documents[doc.name]" class="text-sm text-green-600 mt-2">
                        <font-awesome-icon icon="check-circle" />
                        {{ form.documents[doc.name].name }}
                    </p>
                </div>
            </div>
        </div>

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
                        {{ $t('distributor.product_focus.declarant_name') }}
                    </label>
                    <input v-model="form.declarant_name" type="text" :placeholder="$t('distributor.product_focus.declarant_name_placeholder')"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $t('distributor.product_focus.declaration_date') }}
                    </label>
                    <input v-model="form.declaration_date" type="date"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        required>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted, watch } from 'vue'
import productsService from '@/services/products.service.js'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

const { t } = useI18n()
const toast = useToast()

const categories = ref([])
const loadingCategories = ref(false)
const categoriesError = ref('')
const labelsMap = ref({})

const documents = [
    {
        name: 'cac',
        labelKey: 'distributor.docs.cac',
        descriptionKey: 'distributor.docs.cac_desc',
        accept: '.pdf,.jpg,.png'
    },
    {
        name: 'form_c07',
        labelKey: 'distributor.docs.form_c07',
        descriptionKey: 'distributor.docs.form_c07_desc',
        accept: '.pdf,.jpg,.png'
    },
    {
        name: 'memart',
        labelKey: 'distributor.docs.memart',
        descriptionKey: 'distributor.docs.memart_desc',
        accept: '.pdf,.jpg,.png'
    },
    {
        name: 'tin',
        labelKey: 'distributor.docs.tin',
        descriptionKey: 'distributor.docs.tin_desc',
        accept: '.pdf,.jpg,.png'
    },
    {
        name: 'referee',
        labelKey: 'distributor.docs.referee',
        descriptionKey: 'distributor.docs.referee_desc',
        accept: '.pdf,.jpg,.png'
    }
]

const form = reactive({
    product_categories: [],
    has_technical_knowledge: null,
    willing_to_train: null,
    distribution_start_time: '',
    promo_participation: '',
    documents: {},
    declarant_name: '',
    declaration_date: new Date().toISOString().split('T')[0],
    other_specify: '',
    // display labels for review step
    product_categories_display: []
})

const handleFileUpload = (event, docName) => {
    const file = event.target.files[0]
    if (file) {
        form.documents[docName] = file
    }
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

        // Append synthetic "Other" option at the end
        mapped.push({ value: 'other', label: t('distributor.product_focus.categories.other') })

        categories.value = mapped
        // prepare label map for quick lookup
        labelsMap.value = mapped.reduce((acc, it) => { acc[it.value] = it.label; return acc }, {})
        // refresh display labels if there are preselected values (unlikely on first load)
        form.product_categories_display = (form.product_categories || []).map(v => labelsMap.value[v] || v)
    } catch (e) {
        console.error('Failed to load categories', e)
        categoriesError.value = 'Failed to load categories.'
        // still provide Other to allow progression
        const fallback = [{ value: 'other', label: t('distributor.product_focus.categories.other') }]
        categories.value = fallback
        labelsMap.value = { other: fallback[0].label }
        toast.error('Failed to load categories')
    } finally {
        loadingCategories.value = false
    }
}

onMounted(loadCategories)

// Keep display labels in sync
watch(() => form.product_categories, (vals) => {
    form.product_categories_display = (vals || []).map(v => labelsMap.value[v] || v)
}, { deep: true })

defineExpose({ form })
</script>