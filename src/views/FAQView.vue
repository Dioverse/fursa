<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">{{ $t('faq.title') }}</h1>
                <p class="text-gray-600 max-w-3xl mx-auto">{{ $t('faq.subtitle') }}</p>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 max-w-6xl mx-auto">
                    <!-- FAQ Accordion -->
                    <div class="lg:col-span-2">
                        <div class="space-y-4">
                            <div v-for="(faq, index) in faqs" :key="index"
                                class="bg-white rounded-lg shadow-md overflow-hidden">
                                <button @click="toggleAccordion(index)"
                                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition"
                                    :class="{ 'bg-primary text-white hover:bg-primary': activeIndex === index }">
                                    <span class="font-semibold">{{ index + 1 }} - {{ faq.question }}</span>
                                    <font-awesome-icon :icon="activeIndex === index ? 'times' : 'plus'"
                                        class="text-lg" />
                                </button>

                                <transition enter-active-class="transition-all duration-300 ease-in-out"
                                    leave-active-class="transition-all duration-300 ease-in-out"
                                    enter-from-class="max-h-0 opacity-0" enter-to-class="max-h-96 opacity-100"
                                    leave-from-class="max-h-96 opacity-100" leave-to-class="max-h-0 opacity-0">
                                    <div v-if="activeIndex === index" class="px-6 py-4 bg-gray-50">
                                        <p class="text-gray-700 leading-relaxed" v-html="faq.answer"></p>
                                    </div>
                                </transition>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                            <h3 class="lg:text-2xl md:text-xl text-lg font-bold mb-4">{{ $t('faq.any_questions') }}</h3>
                            <p class="text-gray-600 mb-6">{{ $t('faq.contact') }}</p>

                            <div class="space-y-4">
                                <a href="tel:+2348028318302"
                                    class="flex items-center gap-3 text-gray-700 hover:text-primary transition">
                                    <font-awesome-icon icon="phone" class="text-primary" />
                                    <span>+2348028318302</span>
                                </a>

                                <a href="mailto:support@fursaenergy.com"
                                    class="flex items-center gap-3 text-gray-700 hover:text-primary transition">
                                    <font-awesome-icon icon="envelope" class="text-primary" />
                                    <span>support@fursaenergy.com</span>
                                </a>
                            </div>

                            <div class="mt-8 pt-8 border-t">
                                                                <h4 class="font-semibold mb-3">{{ $t('faq.business_hours') }}</h4>
                                                                <p class="text-sm text-gray-600">{{ $t('faq.mon_fri') }}</p>
                                                                <p class="text-sm text-gray-600">{{ $t('faq.saturday') }}</p>
                                                                <p class="text-sm text-gray-600">{{ $t('faq.sunday') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import DefaultLayout from '@/layouts/DefaultLayout.vue'

const activeIndex = ref(null)
const { t } = useI18n()

const faqs = computed(() => ([
        {
                question: t('faq.q1'),
                answer: t('faq.a1_html')
        },
        {
                question: t('faq.q2'),
                answer: t('faq.a2_html')
        },
        {
                question: t('faq.q3'),
                answer: t('faq.a3_html')
        },
        {
                question: t('faq.q4'),
                answer: t('faq.a4_html')
        },
        {
                question: t('faq.q5'),
                answer: t('faq.a5_html')
        },
        {
                question: t('faq.q6'),
                answer: t('faq.a6_html')
        }
]))

const toggleAccordion = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index
}
</script>

<style scoped>
.transition-all {
    overflow: hidden;
}
</style>