<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-primary mb-4">Section 4: Distribution Strategy & Product Focus</h3>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Which category of Fursa/MRS Lubricants are you most interested in? (Please tick all that apply)
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label v-for="category in productCategories" :key="category"
                    class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input v-model="form.categories" type="checkbox" :value="category"
                        class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span>{{ category }}</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Do You Have Technical Knowledge About Lubricants or a Team That Does?
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.technicalKnowledge" type="radio" value="yes"
                        class="text-primary focus:ring-primary">
                    <span>Yes</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.technicalKnowledge" type="radio" value="no"
                        class="text-primary focus:ring-primary">
                    <span>No</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Are You Willing to Take Product Training from Fursa?
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.training" type="radio" value="yes" class="text-primary focus:ring-primary">
                    <span>Yes</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.training" type="radio" value="no" class="text-primary focus:ring-primary">
                    <span>No</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.training" type="radio" value="depends" class="text-primary focus:ring-primary">
                    <span>Depends on Arrangement</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                How Soon Can You Commence Distribution Post-Onboarding?
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.commenceTime" type="radio" value="immediately"
                        class="text-primary focus:ring-primary">
                    <span>Immediately</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.commenceTime" type="radio" value="2weeks"
                        class="text-primary focus:ring-primary">
                    <span>Within 2 Weeks</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.commenceTime" type="radio" value="month"
                        class="text-primary focus:ring-primary">
                    <span>Within a Month</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Preferred States You Would Like to Cover:
            </label>
            <textarea v-model="form.preferredStates" rows="3" placeholder="List the states you plan to distribute in..."
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-4">
                Upload Required Documents
            </label>
            <div class="space-y-4">
                <div v-for="doc in documents" :key="doc.name" class="border-2 border-dashed rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ doc.label }}</p>
                            <p class="text-sm text-gray-500">{{ doc.description }}</p>
                        </div>
                        <label class="cursor-pointer">
                            <input type="file" :accept="doc.accept" @change="handleFileUpload($event, doc.name)"
                                class="hidden">
                            <div
                                class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded hover:bg-gray-200 transition">
                                <font-awesome-icon icon="upload" />
                                <span>Choose File</span>
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
                    I declare that the information provided herein is accurate and complete to the best of my knowledge.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Signature (Upload Image or Draw):
                    </label>
                    <div class="border-2 border-dashed rounded-lg p-4 text-center">
                        <input type="file" accept="image/*" @change="handleSignature" class="hidden"
                            id="signature-upload">
                        <label for="signature-upload" class="cursor-pointer">
                            <font-awesome-icon icon="upload" size="2x" class="text-gray-400 mb-2" />
                            <p class="text-sm text-gray-600">Click to upload signature</p>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Date:
                    </label>
                    <input v-model="form.date" type="date"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        required>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'

const productCategories = ['Automotive', 'Industrial', 'Agricultural', 'Marine']

const documents = [
    {
        name: 'cac',
        label: 'CAC Certificate',
        description: 'Upload your Certificate of Incorporation',
        accept: '.pdf,.jpg,.png'
    },
    {
        name: 'form_c07',
        label: 'Form C07 (List of Directors)',
        description: 'Upload your list of directors document',
        accept: '.pdf,.jpg,.png'
    },
    {
        name: 'memart',
        label: 'MEMART',
        description: 'Utility Bill (Office Address, not older than 3 months)',
        accept: '.pdf,.jpg,.png'
    },
    {
        name: 'tin',
        label: 'Business Tax Identification Number (TIN)',
        description: 'Upload your TIN certificate',
        accept: '.pdf,.jpg,.png'
    },
    {
        name: 'referee',
        label: 'Letter of Introduction from a Referee',
        description: 'Upload referee letter',
        accept: '.pdf,.jpg,.png'
    }
]

const form = reactive({
    categories: [],
    technicalKnowledge: '',
    training: '',
    commenceTime: '',
    preferredStates: '',
    documents: {},
    signature: null,
    date: new Date().toISOString().split('T')[0]
})

const handleFileUpload = (event, docName) => {
    const file = event.target.files[0]
    if (file) {
        form.documents[docName] = file
    }
}

const handleSignature = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.signature = file
    }
}

defineExpose({ form })
</script>