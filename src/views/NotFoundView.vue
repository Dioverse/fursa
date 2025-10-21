<template>
    <DefaultLayout>
        <div class="min-h-[60vh] flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="text-center px-4">
                <div class="relative">
                    <h1 class="text-9xl font-bold text-gray-200">404</h1>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-6xl">🛢️</div>
                    </div>
                </div>

                <h2 class="text-3xl font-bold text-gray-800 mb-4 mt-8">Oops! Page Not Found</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Looks like you've taken a wrong turn. The page you're looking for has been moved,
                    deleted, or doesn't exist.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <RouterLink to="/"
                        class="inline-flex items-center justify-center gap-2 bg-primary text-white px-6 py-3 rounded-lg hover:bg-opacity-90 transition">
                        <font-awesome-icon icon="home" />
                        Go Back Home
                    </RouterLink>

                    <RouterLink to="/shop"
                        class="inline-flex items-center justify-center gap-2 border-2 border-primary text-primary px-6 py-3 rounded-lg hover:bg-primary hover:text-white transition">
                        <font-awesome-icon icon="shopping-cart" />
                        Visit Shop
                    </RouterLink>
                </div>

                <div class="mt-12">
                    <p class="text-gray-600 mb-4">Need help? Contact us:</p>
                    <div class="flex justify-center gap-6">
                        <a href="tel:+2348028318302" class="text-primary hover:underline">
                            <font-awesome-icon icon="phone" class="mr-2" />
                            Call Us
                        </a>
                        <a href="mailto:support@fursaenergy.com" class="text-primary hover:underline">
                            <font-awesome-icon icon="envelope" class="mr-2" />
                            Email Us
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suggested Products -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h3 class="lg:text-2xl md:text-xl text-lg font-bold text-center mb-8">You Might Be Interested In</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
                    <div v-for="i in 4" :key="i"
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-4xl">🛢️</span>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold mb-2">MRS Premium Oil</h4>
                            <p class="text-primary font-bold">₦145,000</p>
                            <RouterLink to="/shop"
                                class="mt-3 block text-center bg-primary text-white py-2 rounded hover:bg-opacity-90 transition">
                                View Product
                            </RouterLink>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>

<script setup>
import DefaultLayout from '@/layouts/DefaultLayout.vue'
</script>