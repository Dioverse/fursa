<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="relative h-96 bg-gray-900">
            <div class="absolute inset-0">
                <img src="/images/about-hero.png" alt="Fursa Energy Refinery"
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
                            Fursa Energy is an indigenous Nigerian company focused on the distribution, 
                            marketing, and development of high-performance lubricants for both automotive and 
                            industrial applications. We serve as the official Super Distributor for MRS 
                            Lubricants, one of Africa’s most reputable blending facilities located in 
                            Apapa, Lagos.
                        </p>
                        <p class="text-gray-600 mb-4">
                            With a strong presence across Nigeria, Fursa is positioning itself as 
                            the go-to partner for trade, logistics, and supply of best-in-class 
                            lubricants designed to reduce friction, protect machinery, and 
                            enhance operational efficiency.
                        </p>
                        

                        <div class="relative mt-12">
                            <img src="/images/about-image-splash.png" alt="Premium Oil" class="rounded-lg shadow-xl">
                        </div>
                    </div>
                    <div class="relative bg-gray-100 mx-auto p-8 rounded-lg">
                         <h2 class="text-3xl font-bold mb-6 text-primary">Our Story</h2>
                         <h4 class="text-4xl mb-4">Built on Trust, Focused on Trade, Committed to Growth</h4>
                         <p class="text-gray-600 mb-4">
                            Not all journeys begin on the open road but ours is driven by what fuels it. 
                            Fursa Energy was born out of a vision: to be more than just a 
                            distributor of lubricants, but a catalyst for industrial performance, 
                            economic empowerment, and trusted trade partnerships across Africa. 
                        </p>
                        <p class="text-gray-600 mb-4">
                            In a market where reliability often takes a backseat, Fursa set out to raise the 
                            standard to deliver premium lubricants at scale, while building a commercial 
                            ecosystem where partners thrive. At the heart of our story is motion engines 
                            turning, economies rising, businesses scaling. With the backing of MRS Oil PLC, 
                            one of Africa’s largest lubricant blending plants, Fursa Energy proudly delivers 
                            world-class formulations to power vehicles, machines, and industries that drive 
                            progress.
                        </p>
                        <p class="text-gray-600 mb-4">
                            From the trucking routes of the North to the coastal economies of the South, 
                            we don’t just distribute—we empower a network of dealers, distributors, and 
                            institutions with the tools to succeed. Every drop we supply fuels a larger 
                            story of innovation, excellence, and opportunity. Fursa is not just a brand. 
                            It is a movement of trust, trade, and growth in motion.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Our Story</h2>
                <div class="mx-auto">
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-12xl mx-auto">
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

                    <div class="bg-brown rounded-lg max-w-xs">
                        <img src="/images/fursa-brown.png" alt="Premium Oil" class="rounded-lg">
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <WhyChooseUs />

        <!-- Bottom CTA Section -->
        <CTA />

        <!-- Download Button -->
        <Brochure/>

    </DefaultLayout>
</template>

<script setup>
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import WhyChooseUs from '@/components/common/WhyChooseUs.vue'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'

</script>