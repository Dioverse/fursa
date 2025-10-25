<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="relative bg-[#4B4A3F] text-white py-12 md:py-16 lg:py-16 overflow-hidden">
            <!-- Background overlay with oil pouring image -->
            <div class="absolute inset-0">
                <img src="/images/oil-bg.png" alt="Oil pouring" class="w-full h-full object-cover opacity-70">
                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            </div>
            <div
                class="container mx-auto px-4 relative rounded py-4 bg-black/20 md:bg-transparent lg:bg-transparent z-10 flex flex-col md:flex-row items-center">

                <!-- Left Text -->
                <div class="w-full md:w-1/2 space-y-4">
                    <p class="text-sm z-50 relative">{{ $t('home.hero_tagline') }}</p>
                    <h1 class="relative z-20 text-2xl sm:text-3xl md:text-3xl lg:text-4xl font-bold leading-snug" v-html="$t('home.hero_title_html')">
                    </h1>
                    <div class="flex flex-wrap gap-4 mt-8 z-50 relative">
                        <RouterLink to="/shop"
                            class="inline-flex items-center gap-2 bg-primary text-white text-sm sm:text-md px-3 py-2 md:px-5 md:py-2 lg:px-8 lg:py-3 rounded-lg hover:bg-opacity-90 transition">
                            <span>{{ $t('home.shop_now') || 'Shop Now' }}</span>
                            <font-awesome-icon icon="arrow-right" />
                        </RouterLink>
                        <RouterLink to="/distributor-registration"
                            class="inline-flex items-center gap-2 border-2 border-white text-gold-600 text-sm sm:text-md px-3 py-2 md:px-5 md:py-2 lg:px-8 lg:py-3 rounded-lg bg-white hover:opacity-80 transition">
                            <span>{{ $t('home.become_distributor') || 'Become a Distributor' }}</span>
                        </RouterLink>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="w-full md:w-1/2 mt-8 md:mt-0 flex justify-center">
                    <img src="/images/lubricants.png" :alt="$t('home.hero_image_alt')" class="max-w-xs xxxs:w-[15rem] xxs:w-[15rem] md:max-w-md xs:left-[160px] xs:top-[0px] xs:absolute sm:absolute sm:left-[320px] sm:top-[0px] lg:relative lg:left-[0] sm:top-[0px]">
                </div>
            </div>
        </section>


        <!-- Categories Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="grid text-2xl sm:text-2xl md:text-3xl lg:text-3xl font-bold text-center mb-12">{{ $t('home.categories') || 'Categories' }}
                </h2>
                <!-- Loading Skeleton -->
                <template v-if="loading">
                    <div class="flex justify-center gap-10 place-items-center" :style="{ placeItems: center }">

                        <div v-for="n in 4" :key="n" class="text-center animate-pulse">
                            <div class="w-20 h-20 mx-auto mb-3 bg-gray-200 rounded-full shadow-md"></div>
                            <p class="h-4 bg-gray-200 rounded w-3/4 mx-auto"></p>
                        </div>
                    </div>
                </template>
                <div v-else
                    class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-6 gap-6 place-items-center"
                    :style="{ placeItems: center }">
                    <!-- Real Categories -->
                    <div v-if="categories.length === 0" class="text-center group py-12">
                        <font-awesome-icon icon="box" size="3x" class="text-gray-400 mb-4" />
                        <p class="text-gray-600">No categories found</p>
                    </div>
                    <div v-else v-for="category in categories" :key="category.id"
                        class="text-center group cursor-pointer" @click="goToCategory(category.slug)">
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
                <h2 class="grid text-2xl sm:text-2xl md:text-3xl lg:text-3xl font-bold text-center mb-12">{{ $t('home.popular_now') || 'Popular Now' }}
                </h2>
                <ProductGrid :products="popularProducts" :loading="loading" :makeSwiper="true" />
                <div class="text-center mt-8">
                    <RouterLink to="/shop"
                        class="inline-flex items-center gap-2 p-2 rounded bg-gray-300 text-mprimary-600 hover:opacity-60">
                        <span>{{ $t('home.view_more') || 'View More' }}</span>
                        <font-awesome-icon icon="arrow-right" />
                    </RouterLink>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="py-12 bg-[#B49457]">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">

                    <!-- Left: Image -->
                    <div class="flex justify-center lg:justify-start">
                        <img src="/images/about-image.png" :alt="$t('home.about_image_alt')" class="max-w-full">
                    </div>

                    <!-- Right: Text -->
                    <div class="text-white lg:pl-8">
                        <h2 class="text-2xl font-semibold mb-4">{{ $t('home.about_us') || 'About Us' }}</h2>
                        <p class="mb-4">{{ $t('home.about_p1') }}</p>
                        <p class="mb-6">{{ $t('home.about_p2') }}</p>
                        <RouterLink to="/about"
                            class="inline-flex items-center gap-2 bg-white text-[#B49457] px-5 py-2 rounded hover:bg-gray-100 transition">
                            <span>{{ $t('home.learn_more') || 'Learn More' }}</span>
                            <font-awesome-icon icon="arrow-right" />
                        </RouterLink>
                    </div>

                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <WhyChooseUs />


        <!-- CTA Section -->
        <section class="py-16 bg-[#B49457] text-white">
            <div class="container mx-auto px-4 text-center">

                <!-- Tagline Badge -->
                <div class="inline-flex items-center px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs mb-4">
                    <font-awesome-icon icon="bolt" class="mr-1 text-white" />
                    {{ $t('home.ai_badge') }}
                </div>

                <!-- Heading -->
                <h2 class="text-3xl md:text-4xl font-bold mb-4" v-html="$t('home.ai_heading_html')">
                </h2>

                <!-- Description -->
                <p class="text-base md:text-lg mb-8 max-w-3xl mx-auto">{{ $t('home.ai_description') }}</p>

                <!-- Buttons -->
                <div class="flex flex-col md:flex-row gap-4 justify-center mb-10">
                    <button
                        class="bg-white text-[#B49457] px-6 py-3 rounded-lg flex items-center justify-center gap-2 hover:bg-gray-100 transition">
                        <font-awesome-icon icon="search" />
                        {{ $t('home.ai_start_assessment') }}
                    </button>
                    <button
                        class="border border-white px-6 py-3 rounded-lg flex items-center justify-center gap-2 hover:bg-white hover:text-[#B49457] transition">
                        <font-awesome-icon icon="box" />
                        {{ $t('home.ai_view_products') }}
                    </button>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-8 max-w-lg mx-auto text-white">
                    <div>
                        <div class="text-xl font-bold">{{ $t('home.ai_stat_time') }}</div>
                        <div class="text-sm opacity-90">{{ $t('home.ai_stat_time_label') }}</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold">{{ $t('home.ai_stat_accuracy') }}</div>
                        <div class="text-sm opacity-90">{{ $t('home.ai_stat_accuracy_label') }}</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold">{{ $t('home.ai_stat_customers') }}</div>
                        <div class="text-sm opacity-90">{{ $t('home.ai_stat_customers_label') }}</div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Chairman Message -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="container mx-auto px-4">
                    <h2 class="text-3xl font-bold text-center mb-12">{{ $t('home.chairman_section_title') }}</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-center">

                    <!-- Left: Image -->
                    <div class="flex justify-center lg:justify-start">
                        <img src="/images/fursa-chairman.jpg" :alt="$t('home.chairman_image_alt')"
                            class="max-w-full rounded-lg">
                    </div>

                    <!-- Right: Text -->
                    <div class="text-black lg:pl-8">
                        <h2 class="text-2xl font-semibold mb-4">{{ $t('home.chairman_title') }}</h2>
                        <p class="mb-4">{{ $t('home.chairman_p1') }}</p>
                        <p class="mb-6">{{ $t('home.chairman_p2') }}</p>
                        <p class="mb-6">{{ $t('home.chairman_p3') }}</p>
                        <p class="mb-6" v-html="$t('home.chairman_signature_html')"></p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Partners-->
        <section class="py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-6">{{ $t('home.our_partners') || 'Our Strategic Partners' }}</h2>
                <p class="text-center mb-6">{{ $t('home.partners_desc') }}</p>

                <!-- Flex wrap to center partners -->
                <div class="flex flex-wrap justify-center gap-20">
                    <div v-for="partner in partners" :key="partner.title" class="text-center">
                        <div class="w-80 h-80 mb-4 bg-opacity-10 rounded-full flex items-center justify-center">
                            <img :src="partner.icon" alt="" class="max-h-full max-w-full object-contain">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Business model -->
        <section class="py-16 bg-[#b39250] text-white">
            <div class="container mx-auto px-4 text-center">
                <!-- Heading -->
                <h2 class="text-3xl font-bold mb-2">{{ $t('home.business_model') || 'Business Type & Model' }}</h2>
                <p class="mb-12 text-sm">{{ $t('home.business_model_desc') }}</p>

                <!-- Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <!-- B2B Card -->
                    <div class="bg-white text-black rounded-lg shadow-lg p-6 text-left">
                        <div class="flex items-start gap-4">
                            <!-- <font-awesome-icon icon="industry" class="text-2xl text-primary" /> -->
                            <font-awesome-icon icon="shopping-cart" class="text-2xl text-primary" />
                            <div>
                                <h3 class="font-semibold mb-2">{{ $t('home.b2b') || 'B2B (Primary)' }}</h3>
                                <p class="text-sm text-gray-700">{{ $t('home.b2b_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- B2C Card -->
                    <div class="bg-white text-black rounded-lg shadow-lg p-6 text-left">
                        <div class="flex items-start gap-4">
                            <font-awesome-icon icon="shopping-cart" class="text-2xl text-primary" />
                            <div>
                                <h3 class="font-semibold mb-2">{{ $t('home.b2c') || 'B2C (Emerging)' }}</h3>
                                <p class="text-sm text-gray-700">{{ $t('home.b2c_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4 text-center">
                <!-- Heading -->
                <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-2">{{ $t('home.testimonials_title') || 'See What Clients Are Saying' }}</h2>
                <p class="text-gray-700">{{ $t('home.testimonials_subtitle1') }}</p>
                <p class="text-gray-500 text-sm mb-12">{{ $t('home.testimonials_subtitle2') }}</p>

                <!-- Testimonials -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Testimonial Card -->
                    <div class="bg-[#b39250] text-white rounded-lg shadow-lg p-6">
                        <p class="text-sm mb-4">{{ $t('home.testimonial1_text') }}</p>
                        <h4 class="font-bold" v-html="$t('home.testimonial1_author_html')"></h4>
                    </div>

                    <div class="bg-[#b39250] text-white rounded-lg shadow-lg p-6">
                        <p class="text-sm mb-4">{{ $t('home.testimonial2_text') }}</p>
                        <h4 class="font-bold" v-html="$t('home.testimonial2_author_html')"></h4>
                    </div>

                    <div class="bg-[#b39250] text-white rounded-lg shadow-lg p-6">
                        <p class="text-sm mb-4">{{ $t('home.testimonial3_text') }}</p>
                        <h4 class="font-bold" v-html="$t('home.testimonial3_author_html')"></h4>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Blog -->
        <section class="py-12 bg-primary">
            <div class="max-w-6xl mx-auto px-4 bg-primary">
                <!-- Section Title -->
                <h2 class="text-center text-2xl font-bold mb-10">{{ $t('home.our_blog') || 'Our Blog' }}</h2>

                <!-- Blog Cards Container -->
                <div class="grid md:grid-cols-3 gap-8">

                    <!-- Card 1 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="bg-blue-900 p-6 flex justify-center">
                            <img src="/images/engine-3d.png" alt="Engine" class="w-40 h-40 object-contain">
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-green-700 mb-3">{{ $t('home.blog_card_title') }}</h3>
                            <p class="text-gray-700 text-sm mb-5">{{ $t('home.blog_card_excerpt') }}</p>
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="font-medium">{{ $t('home.blog_posted_by') }}</p>
                                    <p class="text-gray-500">{{ $t('home.blog_date_sample') }}</p>
                                </div>
                                <a href="#" class="text-yellow-600 font-medium flex items-center gap-1">
                                    {{ $t('home.learn_more') || 'Learn More' }}
                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="bg-yellow-500 p-6 flex justify-center">
                            <img src="/images/engine-3d.png" alt="Engine" class="w-40 h-40 object-contain">
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-green-700 mb-3">{{ $t('home.blog_card_title') }}</h3>
                            <p class="text-gray-700 text-sm mb-5">{{ $t('home.blog_card_excerpt') }}</p>
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="font-medium">{{ $t('home.blog_posted_by') }}</p>
                                    <p class="text-gray-500">{{ $t('home.blog_date_sample') }}</p>
                                </div>
                                <a href="#" class="text-yellow-600 font-medium flex items-center gap-1">
                                    {{ $t('home.learn_more') || 'Learn More' }}
                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="bg-green-800 p-6 flex justify-center">
                            <img src="/images/engine-3d.png" alt="Engine" class="w-40 h-40 object-contain">
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-green-700 mb-3">{{ $t('home.blog_card_title') }}</h3>
                            <p class="text-gray-700 text-sm mb-5">{{ $t('home.blog_card_excerpt') }}</p>
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="font-medium">{{ $t('home.blog_posted_by') }}</p>
                                    <p class="text-gray-500">{{ $t('home.blog_date_sample') }}</p>
                                </div>
                                <a href="#" class="text-yellow-600 font-medium flex items-center gap-1">
                                    {{ $t('home.learn_more') || 'Learn More' }}
                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Brochure -->
        <Brochure />
    </DefaultLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import ProductGrid from '@/components/products/ProductGrid.vue'
import productsService from '@/services/products.service'
import WhyChooseUs from '@/components/common/WhyChooseUs.vue'
import Brochure from '@/components/common/Brochure.vue'
import axios from "axios"

import {
    faCar,
    faTruck,
    faIndustry,
    faCog,
    faOilCan,
    faExchangeAlt,
    faFillDrip,
    faBuilding,
    faHeadset,
    faHandshake,
    faCertificate,
} from '@fortawesome/free-solid-svg-icons'
import { toNumber } from '@/utils/helpers'

const baseUrl = import.meta.env.VITE_API_BASE_URL
const router = useRouter()
const popularProducts = ref([])
const loading = ref(false)

// const categories = [
//     { id: 1, name: 'Motor Oil', slug: 'motor-oil', icon: faCar },
//     { id: 2, name: 'Heavy Duty', slug: 'heavy-duty', icon: faTruck },
//     { id: 3, name: 'Industrial & Agricultural', slug: 'industrial', icon: faIndustry },
//     { id: 4, name: 'Gear Oil', slug: 'gear-oil', icon: faCog },
//     { id: 5, name: 'Hydraulic & Grease', slug: 'hydraulic', icon: faOilCan },
//     { id: 6, name: 'Transmission Fluids', slug: 'transmission', icon: faExchangeAlt },
//     { id: 7, name: 'Greases', slug: 'greases', icon: faFillDrip }
// ]

const categories = ref([]);
const caterror = ref(null);

const fetchCategories = async () => {
    loading.value = true;
    caterror.value = null;

    try {
        const response = await fetch(`${baseUrl}/cats?limit=7&random=true`);

        if (!response.ok) {
            throw new Error(`Failed to fetch categories: ${response.statusText}`);
        }

        const result = await response.json();
        categories.value = result.data || [];
    } catch (err) {
        caterror.value = err.message || 'Failed to load categories. Please try again.';
        console.error('Error fetching categories:', err);
    } finally {
        loading.value = false;
    }
};



const partners = [
    {
        icon: '/images/mrslogo.png',
        title: "MRS",
    },
    {
        icon: '/images/bestaflogo.png',
        title: 'Bestaf',
    }
]


const goToCategory = (slug) => {
    router.push(`/c/${slug}`)
}

onMounted(async () => {
    fetchCategories()
    loading.value = true
    try {
        // Mock data - replace with actual API call
        const response = await axios.get(`${baseUrl}/products?per_page=4`)
        const products = response.data.data.products.data
        popularProducts.value = products.map(p => ({
            id: p.id,
            name: p.name,
            slug: p.slug,
            sku: p.sku,
            price: p.price,//parseFloat(p.base_price), // ensure number
            category: p.category?.name || "Uncategorized",
            image: p.images[0]?.path,
            ...(p.discount
                ? {
                    discount: p.discount,
                    discounted_price: p.discounted_price,
                }
                : { discount: null }),
        }))
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