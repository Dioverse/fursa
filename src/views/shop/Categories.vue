<template>
  <ShopLayout>
    <div class="min-h-screen mx-auto bg-gray-50 container lg:px-20 bg-transparent">
      <!-- BREADCRUMB -->
      <div class="bg-transparent border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-gray-600">
          <div class="px-4">
            Home &gt; Categories
          </div>
        </div>
      </div>
      <div class="categories-page">
        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>Loading categories...</p>
        </div>
    
        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <p class="error-message">{{ error }}</p>
          <button @click="fetchCategories" class="retry-btn">Retry</button>
        </div>
    
        <!-- Categories Display -->
        <div v-else class="categories-container">
    
          <div v-if="categories.length === 0" class="empty-state">
            <p>No categories available at the moment.</p>
          </div>
    
          <div v-else class="categories-grid">
            <div
              v-for="category in categories"
              :key="category.id"
              class="category-card"
            >
              <!-- Category Header -->
              <RouterLink :to="`/shop/c/${category.slug}`" class="category-header">
                <div class="category-image">
                  <img
                    v-if="category.image"
                    :src="`${storageUrl}${category.image}`"
                    :alt="category.name"
                    @error="handleImageError"
                  />
                  <div v-else class="image-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                      <circle cx="8.5" cy="8.5" r="1.5"/>
                      <polyline points="21 15 16 10 5 21"/>
                    </svg>
                  </div>
                </div>
                <div class="category-info">
                  <h2 class="category-name">{{ category.name }}</h2>
                  <p v-if="category.description" class="category-description">
                    {{ category.description }}
                  </p>
                  <span class="subcategories-count">
                    {{ category.subcategories_count }} subcategories
                  </span>
                </div>
              </RouterLink>
    
              <!-- Subcategories -->
              <div v-if="category.subcategories && category.subcategories.length > 0" class="subcategories-list">
                <!-- <h3 class="subcategories-title">Subcategories</h3> -->
                <div class="subcategories-grid">
                  <div
                    v-for="subcategory in category.subcategories"
                    :key="subcategory.id"
                    class="subcategory-item"
                  >
                    <div class="subcategory-content">
                      <h4 class="subcategory-name">{{ subcategory.name }}</h4>
                      <p v-if="subcategory.description" class="subcategory-description">
                        {{ subcategory.description, 60 }}
                      </p>
                      <span class="products-count">
                        {{ subcategory.products_count }} products
                      </span>
                    </div>
                    <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="9 18 15 12 9 6"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ShopLayout>

</template>

<script setup>
import ShopLayout from '@/layouts/ShopLayout.vue';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const categories = ref([]);
const loading = ref(false);
const error = ref(null);

const baseUrl = import.meta.env.VITE_API_BASE_URL;
const storageUrl = import.meta.env.VITE_STORAGE_URL;

const fetchCategories = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await fetch(`${baseUrl}/cats?sub=true`);
    
    if (!response.ok) {
      throw new Error(`Failed to fetch categories: ${response.statusText}`);
    }

    const result = await response.json();
    categories.value = result.data || [];
  } catch (err) {
    error.value = err.message || 'Failed to load categories. Please try again.';
    console.error('Error fetching categories:', err);
  } finally {
    loading.value = false;
  }
};


const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

const handleImageError = (e) => {
  e.target.style.display = 'none';
};

onMounted(() => {
  fetchCategories();
});
</script>

<style scoped>
.categories-page {
  min-height: 100vh;
  background-color: #f8f9fa;
  padding: 2rem;
}

.loading-container,
.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e0e0e0;
  border-top-color: #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-container p {
  margin-top: 1rem;
  color: #666;
  font-size: 1.1rem;
}

.error-message {
  color: #e74c3c;
  font-size: 1.1rem;
  margin-bottom: 1rem;
}

.retry-btn {
  padding: 0.75rem 1.5rem;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s;
}

.retry-btn:hover {
  background-color: #2980b9;
}

.categories-container {
  max-width: 1400px;
  margin: 0 auto;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 2rem;
  text-align: center;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;
  font-size: 1.2rem;
}

.categories-grid {
  display: grid;
  gap: 2rem;
}

.category-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s;
}

.category-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.category-header {
  display: flex;
  gap: 1.5rem;
  padding: 1.5rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.category-header:hover {
  background-color: #f8f9fa;
}

.category-image {
  flex-shrink: 0;
  width: 120px;
  height: 120px;
  border-radius: 8px;
  overflow: hidden;
  background-color: #f0f0f0;
}

.category-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #bbb;
}

.category-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.category-name {
  font-size: 1.75rem;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
}

.category-description {
  color: #666;
  line-height: 1.6;
  margin: 0;
}

.subcategories-count {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background-color: #e3f2fd;
  color: #1976d2;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  width: fit-content;
}

.subcategories-list {
  padding: 1.5rem;
  background-color: #fafafa;
  border-top: 1px solid #e0e0e0;
}

.subcategories-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #555;
  margin: 0 0 1rem 0;
}

.subcategories-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
}

.subcategory-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  background: white;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  cursor: pointer;
  transition: all 0.2s;
}

.subcategory-item:hover {
  border-color: #3498db;
  box-shadow: 0 2px 8px rgba(52, 152, 219, 0.2);
  transform: translateX(4px);
}

.subcategory-content {
  flex: 1;
}

.subcategory-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2c3e50;
  margin: 0 0 0.25rem 0;
}

.subcategory-description {
  font-size: 0.875rem;
  color: #666;
  margin: 0.25rem 0;
  line-height: 1.4;
}

.products-count {
  display: inline-block;
  font-size: 0.813rem;
  color: #888;
  margin-top: 0.25rem;
}

.arrow-icon {
  flex-shrink: 0;
  color: #bbb;
  transition: color 0.2s;
}

.subcategory-item:hover .arrow-icon {
  color: #3498db;
}

@media (max-width: 768px) {
  .categories-page {
    padding: 1rem;
  }

  .page-title {
    font-size: 2rem;
  }

  .category-header {
    flex-direction: column;
  }

  .category-image {
    width: 100%;
    height: 200px;
  }

  .subcategories-grid {
    grid-template-columns: 1fr;
  }
}
</style>