<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-r from-secondary to-gray-900 text-white py-20 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <img src="/images/hero-bg.png" alt="" class="w-full h-full object-cover">
            </div>
            <div class="container mx-auto px-4 text-center relative z-10">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in">
                    Fursa Energy is a Nigerian<br>
                    energy company and the<br>
                    official <span class="text-primary">super distributor</span><br>
                    of MRS Lubricants.
                </h1>
                <p class="text-xl mb-8 animate-fade-in-delay">
                    Built on Trust. Focused on Trade. Committed to Growth.
                </p>
                <div class="flex flex-col md:flex-row gap-4 justify-center animate-fade-in-delay-2">
                    <RouterLink to="/shop"
                        class="inline-flex items-center gap-2 bg-primary text-white px-8 py-3 rounded-lg hover:bg-opacity-90 transition">
                        <span>Shop Now</span>
                        <font-awesome-icon icon="arrow-right" />
                    </RouterLink>
                    <RouterLink to="/distributor-registration"
                        class="inline-flex items-center gap-2 border-2 border-white px-8 py-3 rounded-lg hover:bg-white hover:text-secondary transition">
                        <span>Become a Distributor</span>
                    </RouterLink>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Categories</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-6">
                    <div v-for="category in categories" :key="category.id" class="text-center group cursor-pointer"
                        @click="goToCategory(category.slug)">
                        <div
                            class="w-20 h-20 mx-auto mb-3 bg-white rounded-full shadow-md group-hover:shadow-lg transition flex items-center justify-center">
                            <font-awesome-icon :icon="category.icon" size="2x" class="text-primary" />
                        </div>
                        <p class="text-sm font-medium">{{ category.name }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Products -->
        <section class="py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Popular Now</h2>
                <ProductGrid :products="popularProducts" :loading="loading" />
                <div class="text-center mt-8">
                    <RouterLink to="/shop" class="inline-flex items-center gap-2 text-primary hover:underline">
                        <span>View More</span>
                        <font-awesome-icon icon="arrow-right" />
                    </RouterLink>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold mb-6">About Us</h2>
                        <p class="text-gray-600 mb-4">
                            Fursa Energy is a Nigerian-operated energy solutions company and the official cultured super
                            distributor of MRS Lubricants.
                        </p>
                        <p class="text-gray-600 mb-6">
                            With access to one of Africa's largest blending plants, Fursa offers a range of premium
                            motor lubricants, greases, and specialty oils tailored for industrial and automotive
                            applications.
                        </p>
                        <RouterLink to="/about"
                            class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded hover:bg-opacity-90 transition">
                            <span>Learn More</span>
                            <font-awesome-icon icon="arrow-right" />
                        </RouterLink>
                    </div>
                    <div class="relative">
                        <img src="/images/about-image.png" alt="About Fursa Energy" class="rounded-lg shadow-xl">
                        <div class="absolute -bottom-6 -left-6 bg-primary text-white p-6 rounded-lg">
                            <div class="text-3xl font-bold">15+</div>
                            <div>Years of Excellence</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">
                    Why Choose Fursa Energy as Your Trusted Lubricant Distributor
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="feature in features" :key="feature.title" class="text-center">
                        <div
                            class="w-20 h-20 mx-auto mb-4 bg-primary bg-opacity-10 rounded-full flex items-center justify-center">
                            <font-awesome-icon :icon="feature.icon" size="2x" class="text-primary" />
                        </div>
                        <h3 class="font-semibold mb-2">{{ feature.title }}</h3>
                        <p class="text-gray-600 text-sm">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-primary text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold mb-4">Get to Know the Best Lube for Your Engine</h2>
                <p class="mb-8">
                    Answer a few quick questions about your vehicle and driving habits. Our AI will
                    analyze your needs and recommend the perfect lubricant for optimal engine performance.
                </p>
                <div class="flex flex-col md:flex-row gap-4 justify-center">
                    <button class="bg-white text-primary px-8 py-3 rounded hover:bg-gray-100 transition">
                        <font-awesome-icon icon="clipboard" class="mr-2" />
                        Start Engine Assessment
                    </button>
                    <button
                        class="border-2 border-white px-8 py-3 rounded hover:bg-white hover:text-primary transition">
                        <font-awesome-icon icon="box" class="mr-2" />
                        View All Products
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-8 mt-12 max-w-2xl mx-auto">
                    <div>
                        <div class="text-4xl font-bold">3 min</div>
                        <div class="text-sm">Quick Assessment</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold">98%</div>
                        <div class="text-sm">Accuracy Rate</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold">5000+</div>
                        <div class="text-sm">Happy Customers</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter -->
        <section class="py-16 bg-gray-900 text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold mb-4">AI-Powered Engine Analysis</h2>
                <p class="mb-8 max-w-2xl mx-auto">
                    Get personalized lubricant recommendations based on your vehicle specifications and usage patterns
                </p>
                <div class="flex flex-col md:flex-row gap-4 justify-center max-w-md mx-auto">
                    <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded text-gray-900">
                    <button class="bg-primary px-8 py-3 rounded hover:bg-opacity-90 transition">
                        Subscribe
                    </button>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import ProductGrid from '@/components/products/ProductGrid.vue'
import productsService from '@/services/products.service'

const router = useRouter()
const popularProducts = ref([])
const loading = ref(false)

const categories = [
    { id: 1, name: 'Motor Oil', slug: 'motor-oil', icon: 'car' },
    { id: 2, name: 'Heavy Duty', slug: 'heavy-duty', icon: 'truck' },
    { id: 3, name: 'Industrial & Agricultural', slug: 'industrial', icon: 'industry' },
    { id: 4, name: 'Gear Oil', slug: 'gear-oil', icon: 'cog' },
    { id: 5, name: 'Hydraulic & Grease', slug: 'hydraulic', icon: 'oil-can' },
    { id: 6, name: 'Transmission Fluids', slug: 'transmission', icon: 'exchange-alt' },
    { id: 7, name: 'Greases', slug: 'greases', icon: 'fill-drip' }
]

const features = [
    {
        icon: 'globe-africa',
        title: "Backed by Africa's most advanced lubricant blending facility",
        description: 'State-of-the-art technology ensuring premium quality products'
    },
    {
        icon: 'certificate',
        title: 'Premium Quality Standards',
        description: 'All products meet international quality certifications'
    },
    {
        icon: 'chart-line',
        title: 'Strong Digital Infrastructure',
        description: 'Modern onboarding and competitive pricing strategies'
    },
    {
        icon: 'handshake',
        title: 'Integrated Support System',
        description: 'Complete marketing and after-sales support for partners'
    }
]

const goToCategory = (slug) => {
    router.push(`/shop?category=${slug}`)
}

onMounted(async () => {
    loading.value = true
    try {
        // Mock data - replace with actual API call
        popularProducts.value = [
            { id: 1, name: 'MRS 5L Motorcycle engine oil', price: 145000, sku: 'A23WERT5', rating: 4 },
            { id: 2, name: 'MRS Premium Motor Oil 20W-50', price: 125000, sku: 'B45TYUI8', rating: 5 },
            { id: 3, name: 'MRS Diesel Engine Oil', price: 165000, sku: 'C67HJKL2', rating: 4 },
            { id: 4, name: 'MRS Hydraulic Oil ISO 68', price: 155000, sku: 'D89MNOP4', rating: 5 }
        ]
    } catch (error) {
        console.error('Failed to load products:', error)
    } finally {
        loading.value = false
    }
})
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animate-fade-in-delay {
    animation: fade-in 0.8s ease-out 0.3s both;
}

.animate-fade-in-delay-2 {
    animation: fade-in 0.8s ease-out 0.6s both;
}
</style>