<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="relative h-64 bg-gray-900">
            <div class="absolute inset-0">
                <img src="/images/hero-img.png" :alt="$t('contact.hero_alt')" class="w-full h-full object-cover opacity-40">
            </div>
            <div class="relative container mx-auto px-4 h-full flex items-center justify-center">
                <h1 class="text-5xl font-bold text-white text-center">{{ $t('contact.title') }}</h1>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
                    <!-- Left Side - Contact Info & Form -->
                    <div>
                        <h2 class="text-3xl font-bold mb-6">{{ $t('contact.have_question') }}</h2>
                        <p class="text-gray-600 mb-8">{{ $t('contact.intro_text') }}</p>

                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $t('contact.full_name') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input v-model="form.fullName" type="text" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                        :placeholder="$t('contact.full_name_placeholder')">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $t('contact.last_name') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input v-model="form.lastName" type="text" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                        :placeholder="$t('contact.last_name_placeholder')">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $t('contact.email') }} <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.email" type="email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    :placeholder="$t('contact.email_placeholder')">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $t('contact.message') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea v-model="form.message" rows="5" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    :placeholder="$t('contact.message_placeholder')"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full md:w-auto px-8 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition">
                                {{ $t('contact.send_message') }}
                            </button>
                        </form>
                    </div>

                    <!-- Right Side - Get in Touch Box -->
                    <div>
                        <div class="bg-primary text-white rounded-lg p-8">
                            <h3 class="lg:text-2xl md:text-xl text-lg font-bold mb-6">{{ $t('contact.get_in_touch') }}</h3>
                            <p class="mb-8 opacity-90">{{ $t('contact.get_in_touch_text') }}</p>

                            <div class="space-y-6">
                                <div>
                                    <h4 class="font-semibold mb-2">{{ $t('contact.address1') }}</h4>
                                    <p class="opacity-90">
                                        <font-awesome-icon icon="map-marker-alt" class="mr-2" />
                                        {{ $t('footer.contact.address_line_1') }}
                                    </p>
                                </div>

                                <div>
                                    <h4 class="font-semibold mb-2">{{ $t('contact.address2') }}</h4>
                                    <p class="opacity-90">
                                        <font-awesome-icon icon="map-marker-alt" class="mr-2" />
                                        {{ $t('footer.contact.address_line_2') }}
                                    </p>
                                </div>

                                <div>
                                    <h4 class="font-semibold mb-2">{{ $t('contact.email') }}</h4>
                                    <p class="opacity-90">
                                        <font-awesome-icon icon="envelope" class="mr-2" />
                                        {{ $t('footer.contact.email') }}
                                    </p>
                                </div>

                                <div>
                                    <h4 class="font-semibold mb-2">{{ $t('contact.phone') }}</h4>
                                    <p class="opacity-90">
                                        <font-awesome-icon icon="phone" class="mr-2" />
                                        {{ $t('footer.contact.phone') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8">
                    <h3 class="lg:text-2xl md:text-xl text-lg font-bold mb-2">{{ $t('contact.map_title') }}</h3>
                    <p class="text-gray-600">{{ $t('contact.map_subtitle') }}</p>
                </div>

                <div class="max-w-6xl mx-auto">
                    <div class="bg-gray-200 rounded-lg h-96 flex items-center justify-center">
                        <!-- Replace with actual Google Maps embed -->
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3939.8951285891784!2d7.4803!3d9.0574!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOcKwMDMnMjYuNiJOIDfCsDI4JzQ5LjEiRQ!5e0!3m2!1sen!2sng!4v1234567890"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            class="rounded-lg"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bottom CTA Section -->
        <CTA />

        <!-- Download Button -->
        <Brochure/>
    </DefaultLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'

const toast = useToast()
const { t } = useI18n()

const form = ref({
    fullName: '',
    lastName: '',
    email: '',
    message: ''
})

const handleSubmit = () => {
    // Handle form submission
    toast.success(t('contact.toast_success'))
    // Reset form
    form.value = {
        fullName: '',
        lastName: '',
        email: '',
        message: ''
    }
}
</script>