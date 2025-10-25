<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="relative h-96 bg-gray-900">
            <div class="absolute inset-0">
                <img src="/images/about-hero.png" :alt="$t('about.alt.hero') || 'Fursa Energy Refinery'"
                    class="w-full h-full object-cover opacity-40">
            </div>
            <div class="relative container mx-auto px-4 h-full flex items-center justify-center">
                <h1 class="text-5xl font-bold text-white text-center">{{ $t('about.hero_title') || 'About Fursa Energy' }}</h1>
            </div>
        </section>

        <!-- About Content Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold mb-6">{{ $t('about.about_us') || 'About Us' }}</h2>
                        <p class="text-gray-600 mb-4">{{ $t('about.company.p1') }}</p>
                        <p class="text-gray-600 mb-4">{{ $t('about.company.p2') }}</p>
                        

                        <div class="relative mt-12">
                            <img src="/images/about-image-splash.png" :alt="$t('about.alt.splash') || 'Premium Oil'" class="rounded-lg shadow-xl">
                        </div>
                    </div>
                    <div class="relative bg-gray-100 mx-auto p-8 rounded-lg">
                         <h2 class="text-3xl font-bold mb-6 text-primary">{{ $t('about.our_story') || 'Our Story' }}</h2>
                         <h4 class="text-4xl mb-4">{{ $t('about.story_tagline') || 'Built on Trust, Focused on Trade, Committed to Growth' }}</h4>
                         <p class="text-gray-600 mb-4">{{ $t('about.story.p1') }}</p>
                        <p class="text-gray-600 mb-4">{{ $t('about.story.p2') }}</p>
                        <p class="text-gray-600 mb-4">{{ $t('about.story.p3') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">{{ $t('about.our_story') || 'Our Story' }}</h2>
                <div class="mx-auto">
                        <h3 class="text-2xl font-semibold mb-4">{{ $t('about.story_tagline') || 'Built on Trust, Focused on Trade, Committed to Growth' }}</h3>
                    <p class="text-gray-600 mb-4">{{ $t('about.story_more.p1') }}</p>
                    <p class="text-gray-600 mb-4">{{ $t('about.story_more.p2') }}</p>
                    <p class="text-gray-600">{{ $t('about.story_more.p3') }}</p>
                </div>
            </div>
        </section>

        <!-- Vision & Mission Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-12xl mx-auto">
                    <div class="bg-white rounded-lg shadow-lg p-8 border-t-4 border-primary">
                        <h3 class="lg:text-2xl md:text-xl text-lg font-bold mb-4">{{ $t('about.vision') || 'Vision' }}</h3>
                        <p class="text-gray-600">{{ $t('about.vision_desc') }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-8 border-t-4 border-primary">
                        <h3 class="lg:text-2xl md:text-xl text-lg font-bold mb-4">{{ $t('about.mission') || 'Mission' }}</h3>
                        <p class="text-gray-600">{{ $t('about.mission_desc') }}</p>
                    </div>

                    <div class="bg-brown rounded-lg max-w-xs">
                        <img src="/images/fursa-brown.png" :alt="$t('about.alt.brown') || 'Premium Oil'" class="rounded-lg">
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