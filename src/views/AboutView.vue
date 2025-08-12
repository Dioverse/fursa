<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="relative h-96 bg-gray-900">
            <div class="absolute inset-0">
                <img src="/images/hero-img.png" alt="Fursa Energy Refinery"
                    class="w-full h-full object-cover opacity-40">
            </div>
            <div class="relative container mx-auto px-4 h-full flex items-center justify-center">
                <h1 class="text-5xl font-bold text-white text-center">About Fursa Energy</h1>
            </div>
        </section>

        <!-- About Content Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold mb-6">About Us</h2>
                        <p class="text-gray-600 mb-4">
                            Fursa Energy is a Nigerian-operated energy solutions company and the
                            official cultured super distributor of MRS Lubricants.
                        </p>
                        <p class="text-gray-600 mb-4">
                            With access to one of Africa's largest blending plants, Fursa offers
                            a range of premium motor lubricants, greases, and specialty oils tailored
                            for industrial and automotive applications.
                        </p>
                        <p class="text-gray-600 mb-4">
                            Fursa offers a strong focus on digital onboarding, retail activation,
                            and price competitive flagship products.
                        </p>
                        <p class="text-gray-600">
                            Integrated marketing and after-sales support.
                        </p>
                    </div>
                    <div class="relative">
                        <img src="/images/oil-pouring.png" alt="Premium Oil" class="rounded-lg shadow-xl">
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Our Story</h2>
                <div class="max-w-4xl mx-auto">
                    <h3 class="text-2xl font-semibold mb-4">Built on Trust, Focused on Trade, Committed to Growth</h3>
                    <p class="text-gray-600 mb-4">
                        Get all at fursa! Begin on the open road. Hit hard in Africa's busiest Port.
                        Push through on their light of a storm. Life's a grind. And when the job demands,
                        unthinkable, compromised, and ceaseless productivity across Africa.
                    </p>
                    <p class="text-gray-600 mb-4">
                        For Fursa's modern blends refuse subpar substitute. Fursa oil & gas'
                        product are high-tech partners, precision-perfected for unrivaled
                        machine protection. From the bustling streets of Lagos to dirt roads of
                        the Sahel, our portfolio covers Africa's unique driving terrain.
                    </p>
                    <p class="text-gray-600">
                        From the bustling cities of the North to the coastal swarths of the
                        South to the dusty Sahel of the west, Fursa's expertise is tailored to African
                        roads. Let us partner you with competent lubricants. Fire up your business,
                        Fursa covers your critical operational needs. Let's drive progress across
                        Africa together.
                    </p>
                </div>
            </div>
        </section>

        <!-- Vision & Mission Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                    <div class="bg-white rounded-lg shadow-lg p-8 border-t-4 border-primary">
                        <h3 class="text-2xl font-bold mb-4">Vision</h3>
                        <p class="text-gray-600">
                            To be West Africa's most dependable and innovative energy solutions provider,
                            delivering outstanding value and fostering partnerships that last.
                        </p>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-8 border-t-4 border-primary">
                        <h3 class="text-2xl font-bold mb-4">Mission</h3>
                        <p class="text-gray-600">
                            To provide premium oil lubricants and energy solutions, supporting businesses with quality
                            products, exceptional service, and sustainable practices that drive growth and development.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Fursa Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">
                    Why Choose Fursa Energy as Your Trusted Lubricant Distributor
                </h2>
                <p class="text-center text-gray-600 mb-12 max-w-3xl mx-auto">
                    Fursa Energy is more than a lubricant distributor—we are a trusted partner for businesses across
                    Nigeria.
                    Backed by MRS Lubricants, one of Africa's leading blending plants, we deliver high-performance
                    products
                    that meet global standards.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div
                            class="w-20 h-20 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <font-awesome-icon icon="globe-africa" size="2x" class="text-primary" />
                        </div>
                        <h4 class="font-semibold mb-2">Backed by Africa's Most Advanced Lubricant Blending Facility</h4>
                    </div>

                    <div class="text-center">
                        <div
                            class="w-20 h-20 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <font-awesome-icon icon="certificate" size="2x" class="text-primary" />
                        </div>
                        <h4 class="font-semibold mb-2">Backed by Africa's most advanced lubricant blending facility</h4>
                    </div>

                    <div class="text-center">
                        <div
                            class="w-20 h-20 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <font-awesome-icon icon="chart-line" size="2x" class="text-primary" />
                        </div>
                        <h4 class="font-semibold mb-2">Backed by Africa's most advanced lubricant blending facility</h4>
                    </div>

                    <div class="text-center">
                        <div
                            class="w-20 h-20 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <font-awesome-icon icon="handshake" size="2x" class="text-primary" />
                        </div>
                        <h4 class="font-semibold mb-2">Strong focus on digital onboarding, retail activation, and price
                            competitive flagship products</h4>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <RouterLink to="/shop"
                        class="inline-block bg-primary text-white px-8 py-3 rounded-lg hover:bg-opacity-90 transition">
                        View More <font-awesome-icon icon="arrow-right" class="ml-2" />
                    </RouterLink>
                </div>
            </div>
        </section>

        <!-- Bottom CTA Section -->
        <section class="py-16 bg-secondary text-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <font-awesome-icon icon="shield-alt" size="2x" />
                        </div>
                        <h4 class="font-semibold mb-2">Secure Payment Gateways</h4>
                        <p class="text-sm opacity-90">48+ gateways to ensure your security</p>
                    </div>

                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <font-awesome-icon icon="star" size="2x" />
                        </div>
                        <h4 class="font-semibold mb-2">Genuine Customer Reviews</h4>
                        <p class="text-sm opacity-90">Find verified reviews showcased on our platforms</p>
                    </div>

                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <font-awesome-icon icon="headset" size="2x" />
                        </div>
                        <h4 class="font-semibold mb-2">24/7 Customer Support</h4>
                        <p class="text-sm opacity-90">Always our support team is available for you</p>
                    </div>

                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <font-awesome-icon icon="undo" size="2x" />
                        </div>
                        <h4 class="font-semibold mb-2">Easy Return Policy</h4>
                        <p class="text-sm opacity-90">If you're not satisfied, return it hassle-free</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Download Button -->
        <section class="py-8 bg-white">
            <div class="container mx-auto px-4 text-center">
                <p class="text-lg mb-4">Click on the Download Button to Download MRS product Brochure</p>
                <button class="bg-primary text-white px-8 py-3 rounded-lg hover:bg-opacity-90 transition">
                    <font-awesome-icon icon="download" class="mr-2" />
                    Download Brochure
                </button>
            </div>
        </section>
    </DefaultLayout>
</template>

<script setup>
import DefaultLayout from '@/layouts/DefaultLayout.vue'
</script>