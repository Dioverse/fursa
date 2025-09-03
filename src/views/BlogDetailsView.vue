<template>
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="relative h-96 bg-gray-900">
            <div class="absolute inset-0">
                <img :src="IMG_URL + post.featured_image || '/images/hero-img.png'" :alt="post.title"
                    class="w-full h-full object-cover opacity-60">
            </div>
            <div class="relative container mx-auto px-4 h-full flex items-center">
                <div class="text-white max-w-3xl">
                    <div class="flex items-center gap-4 mb-4">
                        <span class="bg-primary px-3 py-1 rounded text-sm">{{ post.category?.name }}</span>
                        <span class="opacity-75">{{ post?.readTime }} min read</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ post.title }}</h1>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <img :src="post?.authorImage || '/images/avatar.jpg'" :alt="`${post.author?.first_name || 'Media Team'} ${post.author?.last_name || '' }`"
                                class="w-10 h-10 rounded-full">
                            <span>{{ `${post.author?.first_name || 'Media Team'} ${post.author?.last_name || '' }` }}</span>
                        </div>
                        <span class="opacity-75">{{ formatDate(post.published_at) }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Article Content -->
        <article class="py-16 bg-white">
            <div class="container mx-auto px-4">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 max-w-7xl mx-auto">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <!-- <div class="flex items-center justify-center bg-gray-100 rounded-lg mb-12">
                            <img src="/images/engine-3d.png" alt="" class="w-full h-full object-cover">
                        </div> -->
                        <div class="prose prose-lg max-w-none">
                            <!-- <p class="lead text-xl text-primary mb-6">
                                {{ post.excerpt }}
                            </p> -->

                            <div v-html="post.body" class="space-y-4"></div>

                            <!-- Tags -->
                            <div class="flex flex-wrap gap-2 my-8">
                                <span v-for="tag in post.tags" :key="tag"
                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-primary hover:text-white transition cursor-pointer">
                                    #{{ tag }}
                                </span>
                            </div>

                            <!-- Share Section -->
                            <div class="border-t border-b py-6 my-8">
                                <p class="font-semibold mb-4">Share this article:</p>
                                <div class="flex gap-4">
                                    <button
                                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                        <font-awesome-icon :icon="['fab', 'facebook']" />
                                        Facebook
                                    </button>
                                    <button
                                        class="flex items-center gap-2 px-4 py-2 bg-blue-400 text-white rounded hover:bg-blue-500 transition">
                                        <font-awesome-icon :icon="['fab', 'twitter']" />
                                        Twitter
                                    </button>
                                    <button
                                        class="flex items-center gap-2 px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800 transition">
                                        <font-awesome-icon :icon="['fab', 'linkedin']" />
                                        LinkedIn
                                    </button>
                                </div>
                            </div>

                            <!-- Author Bio -->
                            <div class="bg-gray-50 rounded-lg p-6 my-8">
                                <div class="flex items-start gap-4">
                                    <img :src="post.author?.image || '/images/avatar.jpg'" :alt="`${post.author?.first_name || 'Media Team'} ${post.author?.last_name || '' }`"
                                        class="w-20 h-20 rounded-full">
                                    <div>
                                        <h4 class="font-bold text-lg mb-1">{{ `${post.author?.first_name || 'Media Team'} ${post.author?.last_name || '' }` }}</h4>
                                        <p class="text-gray-600 text-sm mb-3">{{ post.authorTitle }}</p>
                                        <p class="text-gray-700">{{ post.authorBio }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="lg:col-span-1">

                        <!-- Related Posts -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-bold mb-4">Related Articles</h3>
                            <div class="space-y-4">
                                <article v-for="related in relatedPosts" :key="related.id" class="group">
                                    <RouterLink :to="`/blog/${related.id}`" class="flex gap-3">
                                        <div class="w-20 h-20 bg-gray-200 rounded flex-shrink-0 overflow-hidden">
                                            <img v-if="related.featured_image" :src="IMG_URL+related.featured_image" :alt="related.title"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                            <div v-else class="w-full h-full flex items-center justify-center">
                                                <span class="text-2xl">🛢️</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-sm mb-1 line-clamp-2 group-hover:text-primary transition">
                                                {{ related.title }}
                                            </h4>
                                            <p class="text-xs text-gray-500">{{ formatDate(related.published_at, 'long') }}</p>
                                        </div>
                                    </RouterLink>
                                </article>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </article>

        <!-- Comments Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto">
                    <h3 class="text-2xl font-bold mb-8">Comments ({{ comments.length }})</h3>

                    <!-- Comment Form -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h4 class="font-semibold mb-4">Leave a Comment</h4>
                        <form @submit.prevent="submitComment" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input v-model="commentForm.name" type="text" placeholder="Your Name"
                                    class="px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required>
                                <input v-model="commentForm.email" type="email" placeholder="Your Email"
                                    class="px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required>
                            </div>
                            <textarea v-model="commentForm.message" rows="4" placeholder="Your Comment"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                required></textarea>
                            <button type="submit"
                                class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-opacity-90 transition">
                                Post Comment
                            </button>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div class="space-y-6">
                        <div v-for="comment in comments" :key="comment.id" class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-start gap-4">
                                <img :src="comment.avatar || '/images/avatar.jpg'" :alt="comment.name"
                                    class="w-12 h-12 rounded-full">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-semibold">{{ comment.name }}</h5>
                                        <span class="text-sm text-gray-500">{{ comment.date }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ comment.message }}</p>
                                </div>
                            </div>
                        </div>
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
import { IMG_URL } from '@/utils/urls'
import { usePostStore } from '@/stores/posts'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'
import { formatDate } from '@/utils/helpers'

const route = useRoute()
const slug = route.params.slug

const toast = useToast()

const postsStore = usePostStore()
const postLoading = ref(postsStore.loading)
const post = ref([])
const relatedPosts = ref([])

// fetch posts and filters
const loadPostDetails = async () => {
  try {
    post.value = []
    postLoading.value = true
    const res = await postsStore.fetchPostDetails(slug)
    post.value = res.post || res.post || []
    relatedPosts.value = res.related || res.related || []
  } catch (err) {
    console.error('Error fetching posts:', err)
  } finally {
    postLoading.value = false
  }
}

// run both on mount
onMounted(() => {
  loadPostDetails()
})



// const post = ref({
//     id: 1,
//     title: 'Choosing the Right Engine Oil: What Retailers Need to Know',
//     excerpt: 'When it comes to engine performance and longevity, few things are more critical than engine oil. Whether you\'re a retailer guiding customers or an end-user selecting oil for your vehicle, understanding the role of engine oil and choosing the right type is essential.',
//     content: `
//     <h2 id="understanding-viscosity">Understanding Oil Viscosity</h2>
//     <p>Oil viscosity is one of the most important factors to consider when selecting engine oil. The viscosity grade, such as 5W-30 or 10W-40, indicates how the oil flows at different temperatures.</p>
    
//     <h2 id="synthetic-vs-conventional">Synthetic vs Conventional Oil</h2>
//     <p>The debate between synthetic and conventional oil continues to be relevant for retailers and consumers alike. Here's what you need to know about each type...</p>
    
//     <h2 id="oil-change-intervals">Recommended Oil Change Intervals</h2>
//     <p>Understanding when to change engine oil is crucial for maintaining engine health and performance...</p>
//   `,
//     category: 'Engine Maintenance',
//     author: 'Precious Adesanya',
//     authorTitle: 'Technical Specialist',
//     authorBio: 'Precious has over 10 years of experience in the lubricants industry and specializes in engine performance optimization.',
//     authorImage: null,
//     date: new Date('2024-01-15'),
//     readTime: 5,
//     tags: ['Engine Oil', 'Maintenance', 'Viscosity', 'Synthetic Oil'],
//     keyTakeaways: [
//         'Understanding viscosity ratings is crucial for proper oil selection',
//         'Synthetic oils offer superior protection in extreme conditions',
//         'Regular oil changes are essential for engine longevity',
//         'Always consult your vehicle manual for specifications'
//     ],
//     tableOfContents: [
//         { id: 'understanding-viscosity', title: 'Understanding Oil Viscosity' },
//         { id: 'synthetic-vs-conventional', title: 'Synthetic vs Conventional Oil' },
//         { id: 'oil-change-intervals', title: 'Recommended Oil Change Intervals' }
//     ]
// })

// const relatedPosts = ref([
//     {
//         id: 2,
//         title: 'Understanding Oil Viscosity Ratings: A Complete Guide',
//         date: '2 days ago',
//         image: null
//     },
//     {
//         id: 3,
//         title: '5 Signs Your Engine Oil Needs Changing',
//         date: '1 week ago',
//         image: null
//     },
//     {
//         id: 4,
//         title: 'The Benefits of Synthetic Oil for Modern Engines',
//         date: '2 weeks ago',
//         image: null
//     }
// ])

const comments = ref([
    {
        id: 1,
        name: 'John Doe',
        avatar: null,
        date: '2 days ago',
        message: 'Great article! This really helped me understand the differences between oil types.'
    },
    {
        id: 2,
        name: 'Jane Smith',
        avatar: null,
        date: '1 week ago',
        message: 'Very informative. I\'ve been using the wrong viscosity for my climate. Thanks for the clarification!'
    }
])

const commentForm = ref({
    name: '',
    email: '',
    message: ''
})

const submitComment = () => {
    // Add comment logic
    comments.value.unshift({
        id: Date.now(),
        name: commentForm.value.name,
        avatar: null,
        date: 'Just now',
        message: commentForm.value.message
    })

    toast.success('Comment posted successfully!')

    // Reset form
    commentForm.value = {
        name: '',
        email: '',
        message: ''
    }
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose h2 {
    @apply text-2xl font-bold mt-8 mb-4;
}

.prose h3 {
    @apply text-xl font-semibold mt-6 mb-3;
}

.prose p {
    @apply mb-4 text-gray-700 leading-relaxed;
}

.prose ul {
    @apply list-disc ml-6 mb-4 space-y-2;
}

.prose ol {
    @apply list-decimal ml-6 mb-4 space-y-2;
}
</style>