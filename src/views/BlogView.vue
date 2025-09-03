<template>
  <DefaultLayout>
    <div class="font-serif text-gray-900">
      <div id="app" class="font-sans antialiased text-gray-800 bg-gray-100 min-h-screen">
        <div class="container mx-auto p-4 md:p-8">
          <!-- Header -->
          <header class="flex flex-col md:flex-row items-center justify-between py-4 border-b border-gray-300 mb-6">
            <div class="text-sm text-gray-500 mb-2 md:mb-0">
              {{ formatDate(new Date()) }}
            </div>
            <div class="flex items-center space-x-3">
              <a href="#" class="text-gray-600 hover:text-gray-800 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                </svg>
              </a>
              <a href="#" class="text-gray-600 hover:text-gray-800 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path
                    d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z">
                  </path>
                </svg>
              </a>
              <a href="#" class="text-gray-600 hover:text-gray-800 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                  <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                </svg>
              </a>
            </div>
          </header>

          <!-- Navigation -->
          <nav class="flex justify-between items-center py-4 border-b border-gray-300">
            <!-- Search Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>

            <!-- Dynamic Nav Links -->
            <ul class="flex-1 flex justify-center space-x-4 md:space-x-8 text-sm md:leading-[0.75]">
              <li v-for="cat in navLinks" :key="cat.slug">
                <a href="#" class="hover:text-blue-600" @click.prevent="applyCategory(cat.slug)">
                  {{ cat.name }}
                </a>
              </li>
            </ul>

            <!-- Subscribe Button -->
            <button
              class="bg-gray-800 text-white text-xs md:text-sm font-bold px-4 py-2 rounded-full hover:bg-gray-600 transition-colors duration-200">
              SUBSCRIBE
            </button>
          </nav>

          <!-- <header class="border-b">
            <div class="container mx-auto px-4 flex items-center justify-between py-4">
              <div class="text-sm text-gray-500">
                {{ formatDate(new Date()) }}
              </div>
              <div class="flex items-center space-x-6">
                <img src="/logo.png" alt="stacker" class="h-6" />
                <nav class="hidden md:flex space-x-6 font-medium">
                  <a v-for="cat in navLinks" :key="cat.slug" href="#" class="hover:text-blue-600"
                    @click.prevent="applyCategory(cat.slug)">
                    {{ cat.name }}
                  </a>
                </nav>
              </div>
              <div class="flex items-center space-x-3">
                <button>
                  <i class="fas fa-search"></i>
                </button>
                <button class="bg-black text-white text-sm px-4 py-2 rounded">
                  SUBSCRIBE
                </button>
              </div>
            </div>
          </header> -->

          <!-- Main Grid -->
          <main class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-8 mt-8">
            <!-- Left Column -->
            <div class="flex flex-col space-y-8">
              <div v-if="postLoading">
                <SkeletonCardBlog v-for="i in 2" :key="i" />
              </div>
              <div v-else>
                <router-link v-for="(post, index) in posts.slice(1).filter((_, i) => i % 3 === 0)" :key="post.id"
                  :to="`/blog/${post.slug}`" class="flex-1 flex flex-col">
                  <img :src="post.featured_image ? IMG_URL + post.featured_image : IMG_URL + post.thumbnail"
                    :alt="post.title" class="w-full h-auto object-cover rounded-lg mb-4" />
                  <span class="text-xs text-gray-500 font-bold uppercase tracking-wide">
                    {{ post.category?.name }}
                  </span>
                  <h2 class="text-2xl font-bold mt-2">
                    {{ post.title }}
                  </h2>
                </router-link>
              </div>
            </div>

            <!-- Center Column - Featured -->
            <router-link v-if="!postLoading && posts.length" :to="`/blog/${posts[0].slug}`"
              class="col-span-1 lg:col-span-2 flex flex-col items-center sticky sm:top-[9rem] h-[fit-content]">
              <img :src="posts[0].featured_image ? IMG_URL + posts[0].featured_image : IMG_URL + posts[0].thumbnail"
                :alt="posts[0].title" class="w-full h-auto object-cover rounded-lg mb-4" />
              <div class="w-full text-center">
                <span class="text-xs text-gray-500 font-bold uppercase tracking-wide">
                  {{ posts[0].category?.name }}
                </span>
                <h1 class="text-4xl font-bold mt-2">
                  {{ posts[0].title }}
                </h1>
              </div>
            </router-link>

            <!-- Right Column - Sidebar -->
            <div v-if="!postLoading" class="lg:block">
              <div class="flex flex-col space-y-4">
                <router-link v-for="(post, index) in posts.slice(1).filter((_, i) => i % 3 !== 0)" :key="post.id"
                  :to="`/blog/${post.slug}`" class="flex items-center space-x-4">
                  <img :src="post.featured_image ? IMG_URL + post.featured_image : IMG_URL + post.thumbnail"
                    :alt="post.title" class="w-24 h-16 object-cover rounded-lg" />
                  <div class="flex flex-col">
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-wide">
                      {{ post.category?.name }}
                    </span>
                    <h3 class="text-sm font-bold mt-1 line-clamp-2">
                      {{ post.title }}
                    </h3>
                  </div>
                </router-link>
              </div>
            </div>

          </main>


        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { usePostStore } from "@/stores/posts";
import DefaultLayout from "@/layouts/DefaultLayout.vue";
import SkeletonCardBlog from "@/components/common/SkeletonCardBlog.vue";
import { formatDate } from "@/utils/helpers";
import { IMG_URL } from "@/utils/urls";

const postsStore = usePostStore();
const postLoading = ref(true);
const posts = ref([]);
const links = ref([]);
const navLinks = ref([]);

// fetch posts
const loadPosts = async (query = {}) => {
  try {
    posts.value = [];
    postLoading.value = true;
    const res = await postsStore.fetchPosts(query);
    posts.value = res.posts?.data || res.posts || [];
    links.value = res.posts?.links || [];
    navLinks.value = res.filters?.categories || [];
  } catch (err) {
    console.error("Error fetching posts:", err);
  } finally {
    postLoading.value = false;
  }
};

// filter by category
const applyCategory = (slug) => {
  loadPosts({ category: slug });
};

onMounted(() => {
  loadPosts();
});
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
