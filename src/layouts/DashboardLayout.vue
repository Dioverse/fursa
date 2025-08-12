<template>
    <DefaultLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar -->
                <aside class="lg:w-64">
                    <DashboardSidebar />
                </aside>

                <!-- Main Content -->
                <main class="flex-1">
                    <slot />
                </main>
            </div>
        </div>
    </DefaultLayout>
</template>

<script setup>
import DefaultLayout from './DefaultLayout.vue'
import DashboardSidebar from '@/components/dashboard/DashboardSidebar.vue'
</script>