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
                            <article v-for="post in topPosts" :key="post.id"
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                                <div class="h-48 bg-gray-200 relative">
                                    <img v-if="post.image" :src="post.image" :alt="post.title"
                                        class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <img src="/images/engine-3d.png" alt="" class="w-full h-full object-cover">
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="font-bold text-lg mb-2 line-clamp-2">{{ post.title }}</h3>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ post.excerpt }}</p>
                                    <RouterLink :to="`/blog/${post.id}`"
                                        class="text-primary hover:underline inline-flex items-center">
                                        {{ language.buttons || 'Learn more' }} <font-awesome-icon icon="arrow-right" class="ml-2 text-sm" />
                                    </RouterLink>
                                </div>
                            </article>
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
                        <!-- Categories -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                            <h3 class="text-xl font-bold mb-4">{{ language.sidebar_categories_header || 'Categories' }}</h3>
                            <div class="space-y-2">
                                <button v-for="category in categories" :key="category"
                                    class="block w-full text-left px-4 py-2 bg-primary bg-opacity-10 text-primary rounded hover:bg-opacity-20 transition">
                                    {{ category }}
                                </button>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
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
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'

const languageStore = useLanguageStore()
const language = ref({})

onMounted(async () => {
  try {
    const data = await languageStore.getContent('blog'); // call once
    console.log('API response:', data);

    // If your API returns { blog: {...} } instead of just {...}
    language.value = data.blog || data;

    console.log('language populated:', language.value);
  } catch (err) {
    console.error('Error fetching language content:', err)
  }
})


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

const topPosts = ref([
    {
        id: 1,
        title: 'Choosing the Right Engine Oil: What Retailers Need to Know',
        excerpt: 'When it comes to engine performance and longevity, few things are more critical than engine oil...',
        image: null,
        date: 'Life Of Engine Oil'
    },
    {
        id: 2,
        title: 'Choosing the Right Engine Oil: What Retailers Need to Know',
        excerpt: 'When it comes to engine performance and longevity, few things are more critical than engine oil...',
        image: null,
        date: 'Life Of Engine Oil'
    },
    {
        id: 3,
        title: 'Choosing the Right Engine Oil: What Retailers Need to Know',
        excerpt: 'When it comes to engine performance and longevity, few things are more critical than engine oil...',
        image: null,
        date: 'Life Of Engine Oil'
    },
    {
        id: 4,
        title: 'Choosing the Right Engine Oil: What Retailers Need to Know',
        excerpt: 'When it comes to engine performance and longevity, few things are more critical than engine oil...',
        image: null,
        date: 'Life Of Engine Oil'
    }
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