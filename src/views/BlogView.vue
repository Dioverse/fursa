<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="relative h-64 bg-gray-900">
            <div class="absolute inset-0">
                <img src="/images/hero-img.png" alt="Our Blog" class="w-full h-full object-cover opacity-40">
            </div>
            <div class="relative container mx-auto px-4 h-full flex items-center justify-center text-center">
                <div>
                    <h1 class="text-5xl font-bold text-white mb-4">{{ language.bread_crumb_header || 'Our Blog' }}</h1>
                    <p class="text-white opacity-90">
                        {{ language.breadcrumb_paragraph || 'Stay updated with the latest news, tips, and insights from Fursa Energy' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Blog Content -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <!-- Top Blog -->
                        <h2 class="text-2xl font-bold mb-6">{{ language.top_blog_header_1 || 'Top Blog' }}</h2>

                        <!-- Blog Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                            <div v-if="posts.length < 1" class="min-h-[300px] flex items-center justify-center text-center text-gray-500">
                                <h2 class="text-xl font-semibold">No posts available at the moment.</h2>
                            </div>
                            <article v-for="post in posts" :key="post.id"
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                                <RouterLink :to="`/blog/${post.slug}`" class="h-48 bg-gray-200 relative">
                                    <img v-if="post.image" :src="post.image" :alt="post.title"
                                        class="w-full h-full object-cover">
                                    <div v-else class="items-center justify-center">
                                        <img :src="IMG_URL + post.featured_image" :alt="post.title" class="w-full h-full object-cover">
                                    </div>
                                </RouterLink>
                                <div class="p-6">
                                    <h3 class="font-bold text-lg mb-2 line-clamp-2">{{ post.title }}</h3>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ post.excerpt }}</p>
                                    <div class="flex items-center justify-between">
                                            <div class="text-sm text-gray-500">
                                            <span>{{ `${post.author?.first_name || 'Media Team'} ${post.author?.last_name || '' }` }}</span> •
                                            <span>{{ formatDate(post.published_at) }}</span>
                                        </div>
                                        <RouterLink :to="`/blog/${post.slug}`"
                                            class="text-primary hover:underline inline-flex items-center">
                                            {{ language.buttons || 'Learn more' }} <font-awesome-icon icon="arrow-right" class="ml-2 text-sm" />
                                        </RouterLink>
                                    </div>
                                </div>
                            </article>
                                
                        </div>
                        <!-- Pagination -->
                        <div v-if="links && links.length > 0" class="flex justify-center mt-8">
                            <nav class="inline-flex space-x-2">
                                <button
                                v-for="(link, index) in links"
                                :key="index"
                                :disabled="!link.url"
                                @click="goToPage(link)"
                                v-html="link.label"
                                class="px-3 py-1 rounded-md border text-sm"
                                :class="[
                                    link.active
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                />
                            </nav>
                        </div>

                        <!-- Latest Post -->
                        <h2 class="text-2xl font-bold mb-6">{{ language.latest_post_header_1 || 'Latest Post' }}</h2>

                        <div class="space-y-6">
                            <article v-for="post in latestPosts" :key="post.id"
                                class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="h-48 md:h-auto bg-gray-200 rounded-lg">
                                        <img v-if="post.image" :src="post.image" :alt="post.title"
                                            class="w-full h-full object-cover rounded-lg">
                                        <div v-else class="w-full h-full flex items-center justify-center">
                                            <img src="/images/engine-3d.png" alt=""
                                                class="w-full h-full object-cover rounded-lg">
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h3 class="font-bold text-xl mb-3">{{ post.title }}</h3>
                                        <p class="text-gray-600 mb-4">{{ post.excerpt }}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-gray-500">
                                                <span>{{ post.author }}</span> •
                                                <span>{{ post.date }}</span>
                                            </div>
                                            <RouterLink :to="`/blog/${post.id}`"
                                                class="text-primary hover:underline inline-flex items-center">
                                                {{ language.buttons || 'Learn more' }} <font-awesome-icon icon="arrow-right" class="ml-2 text-sm" />
                                            </RouterLink>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6 mb-3">
                            <h3 class="text-xl font-bold mb-4">
                                {{ language.sidebar_search_header || 'Search' }}
                            </h3>
                            <form @submit.prevent="applyFilters" class="space-y-2">
                                <input
                                type="text"
                                class="form-text w-full outline-none text-blue-600 rounded-sm border-gray-300 focus:ring-blue-500"
                                placeholder="Enter keyword..."
                                v-model="filters.search"
                                >
                                <button
                                type="submit"
                                class="w-full bg-blue-600 text-white font-medium py-2 px-4 rounded-lg hover:bg-blue-700 transition"
                                >
                                Search
                                </button>
                            </form>
                        </div>

                        <div class="bg-white rounded-lg shadow-md p-6 mb-3">
                            <h3 class="text-xl font-bold mb-4">
                                {{ language.sidebar_categories_header || 'Categories' }}
                            </h3>
                            <div class="space-y-2">
                                <label 
                                    v-for="category in availableFilters.categories" 
                                    :key="category.slug" 
                                    class="flex items-center space-x-3 px-4 py-2 bg-blue-50 text-blue-700 rounded-md transition-all duration-200 ease-in-out cursor-pointer hover:bg-blue-100"
                                >
                                    <input 
                                    type="checkbox"
                                    class="form-checkbox h-5 w-5 text-blue-600 rounded-sm border-gray-300 focus:ring-blue-500"
                                    :value="category.slug"
                                    v-model="filters.categories"
                                    @change="applyFilters"
                                    >
                                    <span class="text-base font-medium">{{ category.name }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- <div class="bg-white rounded-lg shadow-md p-6 mb-3">
                            <h3 class="text-xl font-bold mb-4">
                                {{ language.sidebar_per_page_header || 'Posts per page' }}
                            </h3>
                            <div class="space-y-2">
                                <select 
                                    v-model="filters.per_page" 
                                    @change="applyFilters"
                                    class="form-select w-full px-3 py-2 border rounded-md text-blue-700 bg-blue-50 border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option :value="1">1</option>
                                    <option :value="2">2</option>
                                    <option :value="3">3</option>
                                    <option :value="4">4</option>
                                </select>
                            </div>
                        </div> -->

                        <!-- Tags -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-3">
                            <h3 class="text-xl font-bold mb-4">{{ language.sidebar_tags_header || 'Tags' }}</h3>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="tag in tags" :key="tag"
                                    class="px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-full text-sm">
                                    {{ tag }}
                                </span>
                            </div>
                        </div>

                        <!-- Popular Blogs -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-bold mb-4">{{ language.sidebar_popular_blogs_header || 'Popular Blogs' }}</h3>
                            <div class="space-y-4">
                                <article v-for="post in popularPosts" :key="post.id" class="flex gap-3">
                                    <div class="w-20 h-20 bg-gray-200 rounded flex-shrink-0">
                                        <img v-if="post.image" :src="post.image" :alt="post.title"
                                            class="w-full h-full object-cover rounded">
                                        <div v-else class="w-full h-full flex items-center justify-center text-2xl">
                                            🛢️
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-sm mb-1 line-clamp-2">{{ post.title }}</h4>
                                        <p class="text-xs text-gray-500">{{ post.date }}</p>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                <div class="mt-12">
                    <h2 class="text-2xl font-bold mb-6">{{ language.related_posts_header || 'Related Post' }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <article v-for="post in relatedPosts" :key="post.id"
                            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                            <div class="grid grid-cols-2">
                                <div class="h-48 bg-gray-200">
                                    <img v-if="post.image" :src="post.image" :alt="post.title"
                                        class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <img src="/images/engine-3d.png" alt="" class="w-full h-full object-cover">
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="font-bold mb-2">{{ post.title }}</h3>
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ post.excerpt }}</p>
                                    <RouterLink :to="`/blog/${post.id}`"
                                        class="text-primary hover:underline inline-flex items-center text-sm">
                                        {{ language.buttons || 'Learn more' }} <font-awesome-icon icon="arrow-right" class="ml-2 text-xs" />
                                    </RouterLink>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bottom CTA Section -->
        <CTA />

        <!-- Download Button -->
        <Brochure/>
    </DefaultLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { IMG_URL } from '@/utils/urls'
import { useLanguageStore } from '@/stores/language'
import { usePostStore } from '@/stores/posts'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'
import { formatDate } from '@/utils/helpers'

const languageStore = useLanguageStore()
const postsStore = usePostStore()

const language = ref({})
const posts = ref([])
const links = ref([])
const filters = reactive({
  search: '',
  categories: [],
  sort: 'latest',
  per_page: 10,
  page: 1
})
const availableFilters = ref({
  categories: [],
  sort: []
})

// fetch language content
const loadLanguage = async () => {
  try {
    const data = await languageStore.getContent('blog')
    language.value = data.blog || data
  } catch (err) {
    console.error('Error fetching language content:', err)
  }
}

// fetch posts and filters
const loadPosts = async (query = {}) => {
  try {
    const res = await postsStore.fetchPosts(query)
    posts.value = res.posts?.data || res.posts || []
    links.value = res.posts?.links || []
    availableFilters.value = res.filters || { categories: [], sort: [] }
  } catch (err) {
    console.error('Error fetching posts:', err)
  }
}

// run both on mount
onMounted(() => {
  loadLanguage()
  loadPosts()
})

// handler for filter form submission
const applyFilters = () => {
  const query = {
    ...filters,
    categories: filters.categories.join(',') // <-- convert array to comma separated string
  }
  loadPosts(query)
}

const goToPage = (link) => {
  if (!link.url) return
  console.log(link);

  // extract the page number from the URL
  const page = new URL(link.url).searchParams.get("page")

  // update filter and re-fetch
  filters.page = page
  applyFilters()
}

const categories = ref([
    'Motor Oil',
    'Engine Maintenance',
    'Industrial',
    'Automotive'
])

const tags = ref([
    'Oil Change',
    'Engine Care',
    'Maintenance',
    'Performance',
    'Lubricants'
])

const latestPosts = ref([
    {
        id: 5,
        title: 'Choosing the Right Engine Oil: What Retailers Need to Know',
        excerpt: 'When it comes to engine performance and longevity, few things are more critical than engine oil. Whether you\'re a retailer guiding customers or an end-user selecting oil for your vehicle, understanding the role of engine oil and choosing the right type is essential.',
        author: 'Precious Adesanya',
        date: 'Life Of Engine Oil',
        image: null
    },
    {
        id: 6,
        title: 'Choosing the Right Engine Oil: What Retailers Need to Know',
        excerpt: 'When it comes to engine performance and longevity, few things are more critical than engine oil. Whether you\'re a retailer guiding customers or an end-user selecting oil for your vehicle, understanding the role of engine oil and choosing the right type is essential.',
        author: 'Precious Adesanya',
        date: 'Life Of Engine Oil',
        image: null
    }
])

const popularPosts = ref([
    {
        id: 7,
        title: 'Understanding Oil Viscosity Ratings',
        date: '2 days ago',
        image: null
    },
    {
        id: 8,
        title: 'Synthetic vs Conventional Oil',
        date: '1 week ago',
        image: null
    },
    {
        id: 9,
        title: 'Engine Maintenance Tips',
        date: '2 weeks ago',
        image: null
    },
    {
        id: 10,
        title: 'Industrial Lubricants Guide',
        date: '3 weeks ago',
        image: null
    },
    {
        id: 11,
        title: 'Oil Change Intervals',
        date: '1 month ago',
        image: null
    }
])

const relatedPosts = ref([
    {
        id: 12,
        title: 'Choosing the Right Engine Oil: What Retailers Need to Know',
        excerpt: 'When it comes to engine performance and longevity, few things are more critical than selecting the right engine oil for your vehicle...',
        image: null
    },
    {
        id: 13,
        title: 'Choosing the Right Engine Oil: What Retailers Need to Know',
        excerpt: 'When it comes to engine performance and longevity, few things are more critical than selecting the right engine oil for your vehicle...',
        image: null
    }
])
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>