<template>
    <DefaultLayout>
       <!-- Hero Section -->
        <section class="relative bg-[#4B4A3F] text-white py-16 overflow-hidden">
            <!-- Background overlay with oil pouring image -->
            <div class="absolute inset-0">
                <img src="/public/images/oil-bg.png" alt="Oil pouring" class="w-full h-full object-cover opacity-70">
                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            </div>

            <div class="container mx-auto px-4 relative z-10 flex flex-col md:flex-row items-center">
                
                <!-- Left Text -->
                <div class="w-full md:w-1/2 space-y-4">
                    <p class="text-sm">Built on Trust. Focused on Trade. Committed to Growth.</p>
                    <h1 class="text-3xl md:text-4xl font-bold leading-snug">
                        Fursa Energy is a Nigerian<br>
                        energy company and the<br>
                        official <span class="text-primary">super distributor</span><br>
                        of MRS Lubricants.
                    </h1>
                    <div class="flex flex-wrap gap-4 mt-8">
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

                <!-- Right Image -->
                <div class="w-full md:w-1/2 mt-8 md:mt-0 flex justify-center">
                    <img src="/public/images/lubricants.png" alt="MRS Lubricants" class="max-w-xs md:max-w-md">
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
        <section class="py-12 bg-[#B49457]">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                    
                    <!-- Left: Image -->
                    <div class="flex justify-center lg:justify-start">
                        <img src="/images/about-image.png" alt="About Fursa Energy" class="max-w-full">
                    </div>

                    <!-- Right: Text -->
                    <div class="text-white lg:pl-8">
                        <h2 class="text-2xl font-semibold mb-4">About Us</h2>
                        <p class="mb-4">
                            Fursa Energy is a Nigerian-owned energy solutions company and the official national super 
                            distributor of MRS Lubricants.
                        </p>
                        <p class="mb-6">
                            With access to one of Africa’s largest blending plants, Fursa offers a range of premium-grade 
                            lubricants, greases, and specialty oils tailored for industrial and automotive applications.
                        </p>
                        <RouterLink to="/about"
                            class="inline-flex items-center gap-2 bg-white text-[#B49457] px-5 py-2 rounded hover:bg-gray-100 transition">
                            <span>Learn More</span>
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
                    AI-Powered Engine Analysis
                </div>

                <!-- Heading -->
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Get to Know the Best Lube <br class="hidden md:block" />
                    <span class="text-[#FFD85E]">for Your Engine</span>
                </h2>

                <!-- Description -->
                <p class="text-base md:text-lg mb-8 max-w-3xl mx-auto">
                    Answer a few quick questions about your vehicle and driving habits. Our AI will analyze your needs and 
                    recommend the perfect lubricant for optimal engine performance.
                </p>

                <!-- Buttons -->
                <div class="flex flex-col md:flex-row gap-4 justify-center mb-10">
                    <button class="bg-white text-[#B49457] px-6 py-3 rounded-lg flex items-center justify-center gap-2 hover:bg-gray-100 transition">
                        <font-awesome-icon icon="search" />
                        Start Engine Assessment
                    </button>
                    <button class="border border-white px-6 py-3 rounded-lg flex items-center justify-center gap-2 hover:bg-white hover:text-[#B49457] transition">
                        <font-awesome-icon icon="box" />
                        View All Products
                    </button>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-8 max-w-lg mx-auto text-white">
                    <div>
                        <div class="text-xl font-bold">3 min</div>
                        <div class="text-sm opacity-90">Quick Assessment</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold">98%</div>
                        <div class="text-sm opacity-90">Accuracy Rate</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold">5000+</div>
                        <div class="text-sm opacity-90">Happy Customers</div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Chairman Message -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="container mx-auto px-4">
                    <h2 class="text-3xl font-bold text-center mb-12">
                        Message from the chairman
                    </h2>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-center">
                    
                    <!-- Left: Image -->    
                    <div class="flex justify-center lg:justify-start">
                        <img src="../../public/images/chairman.png" alt="About Fursa Energy" class="max-w-full">
                    </div>

                    <!-- Right: Text -->
                    <div class="text-black lg:pl-8">
                        <h2 class="text-2xl font-semibold mb-4">Chairman's Message</h2>
                        <p class="mb-4">
                            At FURSA, we believe that quality engine oil is not just a product. 
                            it’s a promise of performance, protection, and reliability. 
                            Our mission is to provide motorists, mechanics, and businesses 
                            with oil solutions they can truly depend on.
                        </p>
                        <p class="mb-6">
                           As you explore our range of fully synthetic, high-mileage, and conventional 
                           oils, know that each product is backed by our commitment to excellence and 
                           customer satisfaction.
                        </p>
                        <p class="mb-6">Thank you for being part of the FURSA journey.</p>
                        <p class="mb-6">Sincerely, <br/> The Chairman <br/> FURSA</p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Partners-->
        <section class="py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-6">
                    Our Strategic Partners
                </h2>
                <p class="text-center mb-6">
                    Fursa Energy collaborates with industry leaders to ensure quality, reliability, and reach across every region we serve.
                </p>

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
                <h2 class="text-3xl font-bold mb-2">Business Type &amp; Model</h2>
                <p class="mb-12 text-sm">
                    We operate through a hybrid business model tailored to serve bulk buyers and everyday users alike.
                </p>

                <!-- Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <!-- B2B Card -->
                    <div class="bg-white text-black rounded-lg shadow-lg p-6 text-left">
                        <div class="flex items-start gap-4">
                            <!-- <font-awesome-icon icon="industry" class="text-2xl text-primary" /> -->
                            <font-awesome-icon icon="shopping-cart" class="text-2xl text-primary" />
                            <div>
                                <h3 class="font-semibold mb-2">B2B (Primary)</h3>
                                <p class="text-sm text-gray-700">
                                    Supply of lubricants to industrial clients, transport companies, construction firms, and government agencies.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- B2C Card -->
                    <div class="bg-white text-black rounded-lg shadow-lg p-6 text-left">
                        <div class="flex items-start gap-4">
                            <font-awesome-icon icon="shopping-cart" class="text-2xl text-primary" />
                            <div>
                                <h3 class="font-semibold mb-2">B2C (Emerging)</h3>
                                <p class="text-sm text-gray-700">
                                    Distributors, dealers, auto workshops, mechanics, fleet operators, and consumers via e-commerce.
                                </p>
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
                <h2 class="text-2xl font-bold mb-2">See What Clients Are Saying</h2>
                <p class="text-gray-700">
                    We are very proud of the service we provide and stand by every product we carry.
                </p>
                <p class="text-gray-500 text-sm mb-12">
                    Read our testimonials from our happy customers.
                </p>

                <!-- Testimonials -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Testimonial Card -->
                    <div class="bg-[#b39250] text-white rounded-lg shadow-lg p-6">
                        <p class="text-sm mb-4">
                            Working with Fursa Energy has completely transformed our operations.
                            Their lubricant quality is top-tier, and their delivery is always reliable.
                            What truly stands out is their customer support — responsive, knowledgeable,
                            and genuinely invested in our success. We've cut downtime, improved engine
                            performance, and gained a dependable partner in the process.
                        </p>
                        <h4 class="font-bold">Ahmed Bello, Fleet Manager,<br>North Logistics Ltd</h4>
                    </div>

                    <div class="bg-[#b39250] text-white rounded-lg shadow-lg p-6">
                        <p class="text-sm mb-4">
                            Working with Fursa Energy has completely transformed our operations.
                            Their lubricant quality is top-tier, and their delivery is always reliable.
                            What truly stands out is their customer support — responsive, knowledgeable,
                            and genuinely invested in our success. We've cut downtime, improved engine
                            performance, and gained a dependable partner in the process.
                        </p>
                        <h4 class="font-bold">Ahmed Bello, Fleet Manager,<br>North Logistics Ltd</h4>
                    </div>

                    <div class="bg-[#b39250] text-white rounded-lg shadow-lg p-6">
                        <p class="text-sm mb-4">
                            Working with Fursa Energy has completely transformed our operations.
                            Their lubricant quality is top-tier, and their delivery is always reliable.
                            What truly stands out is their customer support — responsive, knowledgeable,
                            and genuinely invested in our success. We've cut downtime, improved engine
                            performance, and gained a dependable partner in the process.
                        </p>
                        <h4 class="font-bold">Ahmed Bello, Fleet Manager,<br>North Logistics Ltd</h4>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Blog -->
        <section class="py-12 bg-primary">
            <div class="max-w-6xl mx-auto px-4 bg-primary">
            <!-- Section Title -->
            <h2 class="text-center text-2xl font-bold mb-10">Our Blog</h2>

            <!-- Blog Cards Container -->
            <div class="grid md:grid-cols-3 gap-8">
                
                <!-- Card 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-blue-900 p-6 flex justify-center">
                    <img src="../../public/images/engine-3d.png" alt="Engine" class="w-40 h-40 object-contain">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-green-700 mb-3">
                    Choosing the Right Engine Oil: What Retailers Need to Know.
                    </h3>
                    <p class="text-gray-700 text-sm mb-5">
                    When it comes to engine performance and longevity, few things are more critical than engine oil. Whether you're a retailer guiding customers or a car owner trying to make the right decision for your vehicle, understanding the role of engine oil and choosing the right type is essential...
                    </p>
                    <div class="flex items-center justify-between text-sm">
                    <div>
                        <p class="font-medium">Posted by Adams Farida</p>
                        <p class="text-gray-500">12th Of August 2020</p>
                    </div>
                    <a href="#" class="text-yellow-600 font-medium flex items-center gap-1">
                        Learn More 
                        <span>→</span>
                    </a>
                    </div>
                </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-yellow-500 p-6 flex justify-center">
                    <img src="../../public/images/engine-3d.png" alt="Engine" class="w-40 h-40 object-contain">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-green-700 mb-3">
                    Choosing the Right Engine Oil: What Retailers Need to Know.
                    </h3>
                    <p class="text-gray-700 text-sm mb-5">
                    When it comes to engine performance and longevity, few things are more critical than engine oil. Whether you're a retailer guiding customers or a car owner trying to make the right decision for your vehicle, understanding the role of engine oil and choosing the right type is essential...
                    </p>
                    <div class="flex items-center justify-between text-sm">
                    <div>
                        <p class="font-medium">Posted by Adams Farida</p>
                        <p class="text-gray-500">12th Of August 2020</p>
                    </div>
                    <a href="#" class="text-yellow-600 font-medium flex items-center gap-1">
                        Learn More 
                        <span>→</span>
                    </a>
                    </div>
                </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-green-800 p-6 flex justify-center">
                    <img src="../../public/images/engine-3d.png" alt="Engine" class="w-40 h-40 object-contain">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-green-700 mb-3">
                    Choosing the Right Engine Oil: What Retailers Need to Know.
                    </h3>
                    <p class="text-gray-700 text-sm mb-5">
                    When it comes to engine performance and longevity, few things are more critical than engine oil. Whether you're a retailer guiding customers or a car owner trying to make the right decision for your vehicle, understanding the role of engine oil and choosing the right type is essential...
                    </p>
                    <div class="flex items-center justify-between text-sm">
                    <div>
                        <p class="font-medium">Posted by Adams Farida</p>
                        <p class="text-gray-500">12th Of August 2020</p>
                    </div>
                    <a href="#" class="text-yellow-600 font-medium flex items-center gap-1">
                        Learn More 
                        <span>→</span>
                    </a>
                    </div>
                </div>
                </div>

            </div>
            </div>
        </section>

        <!-- Brochure -->
        <Brochure/>
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

const router = useRouter()
const popularProducts = ref([])
const loading = ref(false)

const categories = [
    { id: 1, name: 'Motor Oil', slug: 'motor-oil', icon: faCar },
    { id: 2, name: 'Heavy Duty', slug: 'heavy-duty', icon: faTruck },
    { id: 3, name: 'Industrial & Agricultural', slug: 'industrial', icon: faIndustry },
    { id: 4, name: 'Gear Oil', slug: 'gear-oil', icon: faCog },
    { id: 5, name: 'Hydraulic & Grease', slug: 'hydraulic', icon: faOilCan },
    { id: 6, name: 'Transmission Fluids', slug: 'transmission', icon: faExchangeAlt },
    { id: 7, name: 'Greases', slug: 'greases', icon: faFillDrip }
]



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
    router.push(`/shop?category=${slug}`)
}

onMounted(async () => {
    loading.value = true
    try {
        // Mock data - replace with actual API call
        const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/products`)
        const products = response.data.data.products.data
        popularProducts.value = products.slice(0, 4).map(p => ({
            id: p.id,
            name: p.name,
            price: Number(p.base_price ?? p.price ?? p.distributor_price ?? 0),//parseFloat(p.base_price), // ensure number
            category: p.category?.name || "Uncategorized",
            image: p.images.length ? p.images[0].url : "../../public/images/mrs_motor_oil.png"
        }))
        // popularProducts.value = [
        //     { id: 1, name: 'MRS 5L Motorcycle engine oil', price: 145000, sku: 'A23WERT5', rating: 4, image: '../../public/images/mrs_motor_oil.png' },
        //     { id: 2, name: 'MRS Premium Motor Oil 20W-50', price: 125000, sku: 'B45TYUI8', rating: 5, image: '../../public/images/mrs_motor_oil.png' },
        //     { id: 3, name: 'MRS Diesel Engine Oil', price: 165000, sku: 'C67HJKL2', rating: 4, image: '../../public/images/mrs_motor_oil.png' },
        //     { id: 4, name: 'MRS Hydraulic Oil ISO 68', price: 155000, sku: 'D89MNOP4', rating: 5, image: '../../public/images/mrs_motor_oil.png' }
        // ]
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