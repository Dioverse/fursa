<template>
    <DefaultLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-row lg:flex-row md:gap-6 lg:gap-8 gap-3">
                <DashboardSidebar />

                <!-- Main Content -->
                <main class="flex-1 text-sm md:text-md">
                    <slot />
                </main>
            </div>
        </div>
        <div class="bg-white p-2 mt-5">

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 text-xs md:text-md sm:grid-cols-4 md:grid-cols-4 gap-3 md:gap-4 lg:gap-6">
                <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition cursor-pointer">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon icon="shield-alt" size="2x" class="text-yellow-600" />
                    </div>
                    <h3 class="font-semibold mb-2">{{ $t('cta.secure_payment.title') }}</h3>
                    <p class="text-sm text-gray-600">{{ $t('cta.secure_payment.desc') }}</p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition cursor-pointer">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon icon="star" size="2x" class="text-blue-600" />
                    </div>
                    <h3 class="font-semibold mb-2">{{ $t('cta.reviews.title') }}</h3>
                    <p class="text-sm text-gray-600">{{ $t('cta.reviews.desc') }}</p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition cursor-pointer">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon icon="headset" size="2x" class="text-green-600" />
                    </div>
                    <h3 class="font-semibold mb-2">{{ $t('cta.support.title') }}</h3>
                    <p class="text-sm text-gray-600">{{ $t('cta.support.desc') }}</p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition cursor-pointer">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <font-awesome-icon icon="undo" size="2x" class="text-purple-600" />
                    </div>
                    <h3 class="font-semibold mb-2">{{ $t('cta.returns.title') }}</h3>
                    <p class="text-sm text-gray-600">{{ $t('cta.returns.desc') }}</p>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<script setup>
import DefaultLayout from './DefaultLayout.vue';
import DashboardSidebar from '@/components/dashboard/DashboardSidebar.vue'
</script>