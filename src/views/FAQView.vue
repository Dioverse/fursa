<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Frequently Asked Questions</h1>
                <p class="text-gray-600 max-w-3xl mx-auto">
                    Explore answers to common questions about our lubricants, distribution process, delivery timeline,
                    and product verification.
                </p>
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
                            <h3 class="text-2xl font-bold mb-4">Any Questions?</h3>
                            <p class="text-gray-600 mb-6">Contact:</p>

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
                                <h4 class="font-semibold mb-3">Business Hours:</h4>
                                <p class="text-sm text-gray-600">Monday - Friday: 8:00 AM - 6:00 PM</p>
                                <p class="text-sm text-gray-600">Saturday: 9:00 AM - 4:00 PM</p>
                                <p class="text-sm text-gray-600">Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>

<script setup>
import { ref } from 'vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'

const activeIndex = ref(null)

const faqs = ref([
    {
        question: 'What types of lubricants does Fursa offer?',
        answer: `Fursa Energy offers a wide range of premium-quality lubricants, including:
      <ul class="mt-3 ml-6 list-disc space-y-2">
        <li><strong>Automotive Lubricants:</strong> Engine oils, gear oils, brake fluids, transmission fluids.</li>
        <li><strong>Industrial Lubricants:</strong> Hydraulic oils, compressor oils, turbine oils, and specialty greases.</li>
        <li><strong>Greases:</strong> Lithium complex greases, multipurpose and heavy-duty greases.</li>
        <li><strong>Marine & Heavy Equipment Oils:</strong> High-performance lubricants for ships, tractors, and construction equipment.</li>
      </ul>
      <p class="mt-3">All products are blended in one of the largest state-of-the-art blending plants in Africa, certified for quality and performance.</p>`
    },
    {
        question: 'How do I become a distributor?',
        answer: `Becoming a Fursa Energy distributor is simple:
      <ol class="mt-3 ml-6 list-decimal space-y-2">
        <li>Click on the "Become a Distributor" button on our website</li>
        <li>Complete the online registration form with your business information</li>
        <li>Submit required documents (CAC certificate, business license, etc.)</li>
        <li>Our team will review your application within 48-72 hours</li>
        <li>Once approved, you'll receive onboarding materials and training</li>
      </ol>
      <p class="mt-3">For more information, contact our distributor support team at partners@fursaenergy.com</p>`
    },
    {
        question: 'What is the delivery timeline for orders?',
        answer: `Our delivery timelines are designed to ensure prompt service:
      <ul class="mt-3 ml-6 list-disc space-y-2">
        <li><strong>Lagos & Abuja:</strong> 24-48 hours for orders placed before 2 PM</li>
        <li><strong>Major Cities:</strong> 2-4 business days</li>
        <li><strong>Other Locations:</strong> 3-7 business days</li>
        <li><strong>Bulk Orders:</strong> Customized delivery schedule based on quantity and location</li>
      </ul>
      <p class="mt-3">Track your order in real-time through our website or contact customer service for updates.</p>`
    },
    {
        question: 'How can I verify my product is genuine?',
        answer: `We take product authenticity seriously. Here's how to verify your Fursa/MRS products:
      <ul class="mt-3 ml-6 list-disc space-y-2">
        <li>Check for the holographic security seal on the packaging</li>
        <li>Scan the QR code on the product label using your smartphone</li>
        <li>Send the unique product code via SMS to our verification number</li>
        <li>Visit our website's product verification page</li>
        <li>Look for authorized dealer stickers at point of purchase</li>
      </ul>
      <p class="mt-3">If you suspect a counterfeit product, report it immediately to security@fursaenergy.com</p>`
    },
    {
        question: 'What payment methods do you accept?',
        answer: `We offer multiple convenient payment options:
      <ul class="mt-3 ml-6 list-disc space-y-2">
        <li>Bank transfers (all major Nigerian banks)</li>
        <li>Credit/Debit cards (Visa, Mastercard, Verve)</li>
        <li>Mobile money payments</li>
        <li>PayPal for international customers</li>
        <li>Cash on delivery (selected locations)</li>
        <li>30-day credit terms for approved distributors</li>
      </ul>`
    },
    {
        question: 'Do you offer technical support and training?',
        answer: `Yes! We provide comprehensive technical support:
      <ul class="mt-3 ml-6 list-disc space-y-2">
        <li>Free product training for distributors and their sales teams</li>
        <li>Technical helpline available Monday-Saturday</li>
        <li>On-site support for bulk customers</li>
        <li>Product selection guides and compatibility charts</li>
        <li>Regular webinars on product applications</li>
      </ul>
      <p class="mt-3">Contact our technical team at technical@fursaenergy.com for assistance.</p>`
    }
])

const toggleAccordion = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index
}
</script>

<style scoped>
.transition-all {
    overflow: hidden;
}
</style>