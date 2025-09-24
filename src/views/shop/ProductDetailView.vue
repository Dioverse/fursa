<template>
    <DefaultLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <RouterLink to="/" class="text-gray-700 hover:text-primary">Home</RouterLink>
                    </li>
                    <li><font-awesome-icon icon="chevron-right" class="mx-2 text-gray-400" /></li>
                    <li>
                        <RouterLink to="/shop" class="text-gray-700 hover:text-primary">Shop</RouterLink>
                    </li>
                    <li><font-awesome-icon icon="chevron-right" class="mx-2 text-gray-400" /></li>
                    <li class="text-gray-500">{{ product?.name }}</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Product Images -->
                <div>
                    <div class="bg-gray-100 rounded-lg p-8 mb-4">
                        <img v-if="product.image" :src="product.image" :alt="product.name" class="w-full">
                        <div v-else class="h-96 flex items-center justify-center">
                            <font-awesome-icon icon="image" size="4x" class="text-gray-400" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        <button v-for="i in 4" :key="i" class="bg-gray-100 rounded p-2 hover:ring-2 hover:ring-primary">
                            <font-awesome-icon icon="image" size="2x" class="text-gray-400" />
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div>
                    <h1 class="text-3xl font-bold mb-4">{{ product.name }}</h1>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center">
                            <font-awesome-icon v-for="i in 5" :key="i" icon="star"
                                :class="i <= product.rating ? 'text-yellow-400' : 'text-gray-300'" />
                        </div>
                        <span class="text-gray-600">({{ product.reviews }} reviews)</span>
                        <span class="text-green-600">✓ In Stock</span>
                    </div>

                    <div class="text-3xl font-bold text-primary mb-6">
                        ₦{{ product?.price?.toLocaleString() }}
                    </div>

                    <div class="space-y-4 mb-6">
                        <p class="text-gray-600">{{ product.description }}</p>
                        <div>
                            <span class="font-semibold">SKU:</span> {{ product.sku }}
                        </div>
                        <div>
                            <span class="font-semibold">Volume:</span> {{ product.volume }}
                        </div>
                        <div>
                            <span class="font-semibold">Category:</span> Motor Oil
                        </div>
                    </div>

                    <!-- Quantity and Add to Cart -->
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center border rounded-lg">
                            <button @click="quantity--" :disabled="quantity <= 1"
                                class="px-4 py-3 hover:bg-gray-100 disabled:opacity-50">
                                <font-awesome-icon icon="minus" />
                            </button>
                            <input v-model="quantity" type="number" min="1" class="w-20 text-center border-x py-3">
                            <button @click="quantity++" class="px-4 py-3 hover:bg-gray-100">
                                <font-awesome-icon icon="plus" />
                            </button>
                        </div>
                        <button @click="addToCart"
                            class="flex-1 bg-primary text-white py-3 rounded-lg hover:bg-opacity-90 transition">
                            <font-awesome-icon icon="shopping-cart" class="mr-2" />
                            Add to Cart
                        </button>

                        <button @click="saveWishlist" class="p-3 border rounded-lg hover:bg-gray-100">
                            <font-awesome-icon :icon="['fas', wishlistStore.items.find(p => p.id === product.id) ? 'heart' : 'heart']"
                            :class="wishlistStore.items.find(p => p.id === product.id) ? 'text-red-500' : 'text-gray-400'" />
                        </button>

                    </div>

                    <!-- Features -->
                    <div class="border-t pt-6">
                        <h3 class="font-semibold mb-4">Key Features:</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <font-awesome-icon icon="check" class="text-green-600 mt-1" />
                                <span>Premium quality motor oil</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <font-awesome-icon icon="check" class="text-green-600 mt-1" />
                                <span>Suitable for all vehicle types</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <font-awesome-icon icon="check" class="text-green-600 mt-1" />
                                <span>Extended engine protection</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <font-awesome-icon icon="check" class="text-green-600 mt-1" />
                                <span>Improved fuel efficiency</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mt-12">
                <div class="border-b">
                    <nav class="-mb-px flex gap-8">
                        <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition"
                            :class="activeTab === tab.id ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <div class="py-8">
                    <div v-if="activeTab === 'description'" class="prose max-w-none">
                        <p>{{ product.fullDescription }}</p>
                    </div>

                    <div v-if="activeTab === 'specifications'" class="space-y-4">
                        <table class="w-full">
                            <tbody>
                                <tr v-for="spec in product.specifications" :key="spec.name" class="border-b">
                                    <td class="py-3 font-medium">{{ spec.name }}</td>
                                    <td class="py-3">{{ spec.value }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="activeTab === 'reviews'" class="space-y-6">
                        <div v-for="review in product.reviewsList" :key="review.id" class="border-b pb-6">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold">{{ review.author }}</h4>
                                    <div class="flex items-center gap-2">
                                        <div class="flex">
                                            <font-awesome-icon v-for="i in 5" :key="i" icon="star" size="sm"
                                                :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'" />
                                        </div>
                                        <span class="text-sm text-gray-600">{{ review.date }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600">{{ review.comment }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6">Related Products</h2>
                <ProductGrid :products="relatedProducts" />
            </div>
        </div>
        <!-- Bottom CTA Section -->
        <CTA />
        <!-- Download Button -->
        <Brochure/>
    </DefaultLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import ProductGrid from '@/components/products/ProductGrid.vue'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'


const route = useRoute()
const product = ref({})
const relatedProducts = ref([])

const cartStore = useCartStore()
const wishlistStore  = useWishlistStore()
const toast = useToast()

const quantity = ref(1)
const activeTab = ref('description')
// const wishlist = ref(JSON.parse(localStorage.getItem('wishlist')) || [])
const wishlist = ref(
  Array.isArray(JSON.parse(localStorage.getItem('wishlist')))
    ? JSON.parse(localStorage.getItem('wishlist'))
    : []
)

const isInWishlist = ref(false)


const tabs = [
    { id: 'description', label: 'Description' },
    { id: 'specifications', label: 'Specifications' },
    { id: 'reviews', label: 'Reviews' }
]


const addToCart = () => {
    cartStore.addItem({ ...product.value, quantity: quantity.value })
    // toast.success('Product added to cart!')
    quantity.value = 1
}

const saveWishlist = () => {
  const exists = wishlistStore.items.find(p => p.id === product.value.id)
  if (exists) {
    wishlistStore.remove(product.value.id)
    toast.info(`${product.value.name} removed from wishlist`)
  } else {
    wishlistStore.add(product.value)
    toast.success(`${product.value.name} added to wishlist`)
  }
}

// ✅ Save wishlist
// const saveWishlist = () => {

//   if (!Array.isArray(wishlist.value)) {
//     wishlist.value = []
//   }

//   const exists = wishlist.value.find(item => item.id === product.value.id)

//   if (exists) {
//     // Remove from wishlist
//     wishlist.value = wishlist.value.filter(item => item.id !== product.value.id)
//     toast.info(`${product.value.name} removed from wishlist`)
//     isInWishlist.value = false
//   } else {
//     // Add to wishlist
//     wishlist.value.push(product.value)
//     toast.success(`${product.value.name} added to wishlist`)
//     isInWishlist.value = true
//   }

//   localStorage.setItem('wishlist', JSON.stringify(wishlist.value))
// }

onMounted(async () => {
  const id = route.params.id

  try {
    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/products/${id}`)
    const json = await res.json()

    const apiProduct = json.data
    

    // 🔄 Map API to UI structure
    product.value = {
      id: apiProduct.id,
      name: apiProduct.name,
      price: parseFloat(apiProduct.price),
      discountedPrice: parseFloat(apiProduct.discounted_price) || null,
      sku: apiProduct.sku,
      volume: apiProduct.volume || null, // not in API? keep fallback
      rating: apiProduct.rating || 0,   // not in API yet? default 0
      reviews: apiProduct.reviews || 0, // not in API yet? default 0
      description: apiProduct.short_description,
      fullDescription: apiProduct.description,
      specifications: [
        { name: 'Stock Quantity', value: apiProduct.stock_quantity },
        { name: 'Low Stock Threshold', value: apiProduct.low_stock_threshold },
        { name: 'Category', value: apiProduct.category?.name }
      ],
      images: apiProduct.images.map(img => ({
        id: img.id,
        url: `https://fursa.jarustraining.com.ng/storage/${img.path}`
      })),
      reviewsList: [] // your API doesn’t send reviews yet
    }

    if (!Array.isArray(wishlist.value)) {
        wishlist.value = []
    }

    isInWishlist.value = wishlist.value.some(item => item.id === product.value.id)

    // Related products
    relatedProducts.value = json.related.map(p => ({
      id: p.id,
      name: p.name,
      price: parseFloat(p.price),
      description: p.short_description,
      image: p.images[0]
        ? `https://fursa.jarustraining.com.ng/storage/${p.images[0].path}`
        : null
    }))
  } catch (error) {
    console.error('Error fetching product:', error)
  }
})
</script>