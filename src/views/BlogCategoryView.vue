<template>
  <BlogLayout>
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
            <img :src="post.featured_image ? IMG_URL + post.featured_image : IMG_URL + post.thumbnail" :alt="post.title"
              class="w-full h-auto object-cover rounded-lg mb-4" />
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
      <div v-else-if="postLoading">
        <SkeletonCardBlogCenter />
      </div>


      <!-- Right Column - Sidebar -->
      <div v-if="!postLoading" class="lg:block">
        <div class="flex flex-col space-y-4">
          <router-link v-for="(post, index) in posts.slice(1).filter((_, i) => i % 3 !== 0)" :key="post.id"
            :to="`/blog/${post.slug}`" class="flex items-center space-x-4">
            <img :src="post.featured_image ? IMG_URL + post.featured_image : IMG_URL + post.thumbnail" :alt="post.title"
              class="w-24 h-16 object-cover rounded-lg" />
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
      <div v-else="postLoading">
        <SkeletonCardBlogRight v-for="i in 2" :key="i" />
      </div>

    </main>
  </BlogLayout>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import { usePostStore } from "@/stores/posts";
import BlogLayout from "@/layouts/BlogLayout.vue";
import SkeletonCardBlog from "@/components/common/SkeletonCardBlog.vue";
import SkeletonCardBlogCenter from "@/components/common/SkeletonCardBlogCenter.vue";
import SkeletonCardBlogRight from "@/components/common/SkeletonCardBlogRight.vue";
import { formatDate } from "@/utils/helpers";
import { IMG_URL } from "@/utils/urls";

const postsStore = usePostStore();
const postLoading = ref(true);
const posts = ref([]);
const route = useRoute();
const categorySlug = ref(route.params.slug || null);

// fetch posts
const loadPosts = async (query = {}) => {
  try {
    posts.value = [];
    postLoading.value = true;
    const res = await postsStore.fetchPosts(query);
    posts.value = res.posts?.data || res.posts || [];
  } catch (err) {
    console.error("Error fetching posts:", err);
  } finally {
    postLoading.value = false;
  }
};

watch(
  () => route.params.slug,
  (newSlug) => {
    categorySlug.value = newSlug;
    loadPosts({ categories: categorySlug.value });
  },
  { immediate: true } // fetch on initial load
);


onMounted(() => {
  loadPosts({ categories: categorySlug.value });
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
