<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <!-- <AppHeader /> -->
        <!-- <main class="flex-grow">
            <div class="blog-layout text-gray-900"> -->
                <div id="app" class="font-serif antialiased text-gray-800 bg-gray-100 min-h-screen">
                    <div class="container mx-auto p-4 md:p-8">
                        <!-- Header -->
                        <header
                            class="flex flex-col md:flex-row items-center justify-between py-4 border-b border-gray-300">
                            <div class="text-sm text-gray-500 mb-2 md:mb-0">
                                {{ formatDate(new Date()) }}
                            </div>
                            <div class="flex items-center space-x-3">
                                <a href="#" class="text-gray-600 hover:text-gray-800 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z">
                                        </path>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-600 hover:text-gray-800 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z">
                                        </path>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-600 hover:text-gray-800 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>

                            <!-- Dynamic Nav Links -->
                            <ul v-if="!navLoading" class="flex-1 flex justify-start space-x-4 px-10 md:space-x-8 text-sm md:leading-[3]">
                                <li v-for="cat in navLinks" :key="cat.slug"
                                    :class="{'font-bold border-t-2 border-gray-800': slug === cat.slug}">
                                    <RouterLink :to="`/blog/c/${cat.slug}`" class="hover:text-gray-500">
                                        {{ cat.name }}
                                    </RouterLink>
                                </li>
                            </ul>
                            <SkeletonCardNav v-else />

                            <!-- Subscribe Button -->
                            <button
                                class="bg-gray-800 text-white text-xs md:text-sm font-bold px-4 py-2 rounded-full hover:bg-gray-600 transition-colors duration-200">
                                SUBSCRIBE
                            </button>
                        </nav>
                        <slot />
                    </div>
                </div>
            <!-- </div>
        </main> -->
        <AppFooter />
    </div>
</template>

<script setup>
import AppHeader from '@/components/common/AppHeader.vue'
import AppFooter from '@/components/common/AppFooter.vue'
import { usePostStore } from "@/stores/posts";
import { ref, onMounted, computed } from 'vue';
import { formatDate } from '@/utils/helpers';
import { useRoute } from 'vue-router';
import SkeletonCardNav from '@/components/common/SkeletonCardNav.vue';


const route = useRoute();
const slug = computed(() => route.params.slug);
const catsStore = usePostStore();
const navLoading = ref(true);
const navLinks = ref([]);


const loadCategories = async () => {
  try {
    navLinks.value = [];
    navLoading.value = true;
    const res = await catsStore.fetchBlogCategories();
    navLinks.value = res.data || [];
  } catch (err) {
    console.error("Error fetching categories:", err);
  } finally {
    navLoading.value = false;
  }
};

onMounted(() => {
  loadCategories();
});

</script>

<style scoped>
.font-serif {
  font-family: 'Georgia', serif;
}
</style>