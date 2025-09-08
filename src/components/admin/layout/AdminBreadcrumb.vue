<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <!-- Home/Dashboard -->
            <li>
                <router-link :to="{ name: 'admin.dashboard' }"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <font-awesome-icon icon="home" class="h-4 w-4" />
                    <span class="sr-only">Dashboard</span>
                </router-link>
            </li>

            <!-- Breadcrumb items -->
            <li v-for="(item, index) in breadcrumbs" :key="index">
                <div class="flex items-center">
                    <font-awesome-icon icon="chevron-right" class="h-4 w-4 text-gray-300 mx-2" />

                    <router-link v-if="item.route && index < breadcrumbs.length - 1" :to="item.route"
                        class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                        {{ item.label }}
                    </router-link>

                    <span v-else class="text-sm font-medium text-gray-900"
                        :class="{ 'text-gray-900': index === breadcrumbs.length - 1 }">
                        {{ item.label }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

// Generate breadcrumbs based on current route
const breadcrumbs = computed(() => {
    const crumbs = []

    // Get route segments
    const pathSegments = route.path.split('/').filter(segment => segment)

    // Remove 'admin' from segments as it's handled by the home icon
    const segments = pathSegments.filter(segment => segment !== 'admin')

    // Generate breadcrumbs based on route name and segments
    if (segments.length > 0) {
        switch (segments[0]) {
            case 'users':
                crumbs.push({
                    label: 'Users',
                    route: { name: 'admin.users' }
                })

                if (segments[1] && segments[1] !== 'create') {
                    crumbs.push({
                        label: 'User Details',
                        route: null
                    })
                } else if (segments[1] === 'create') {
                    crumbs.push({
                        label: 'Create User',
                        route: null
                    })
                }
                break

            case 'products':
                crumbs.push({
                    label: 'Products',
                    route: { name: 'admin.products' }
                })

                if (segments[1] && segments[1] !== 'create') {
                    crumbs.push({
                        label: 'Product Details',
                        route: null
                    })
                } else if (segments[1] === 'create') {
                    crumbs.push({
                        label: 'Create Product',
                        route: null
                    })
                }
                break

            case 'distributors':
                crumbs.push({
                    label: 'Distributors',
                    route: { name: 'admin.distributors' }
                })

                if (segments[1] && segments[1] !== 'create') {
                    crumbs.push({
                        label: 'Distributor Details',
                        route: null
                    })
                } else if (segments[1] === 'create') {
                    crumbs.push({
                        label: 'Create Distributor',
                        route: null
                    })
                }
                break

            case 'orders':
                crumbs.push({
                    label: 'Orders',
                    route: { name: 'admin.orders' }
                })

                if (segments[1] && segments[1] !== 'create') {
                    crumbs.push({
                        label: 'Order Details',
                        route: null
                    })
                } else if (segments[1] === 'create') {
                    crumbs.push({
                        label: 'Create Order',
                        route: null
                    })
                }
                break

            case 'content':
                crumbs.push({
                    label: 'Content Management',
                    route: { name: 'admin.content' }
                })

                if (segments[1]) {
                    const contentTypes = {
                        'pages': 'Pages',
                        'posts': 'Posts',
                        'media': 'Media Library',
                        'menus': 'Menus'
                    }

                    crumbs.push({
                        label: contentTypes[segments[1]] || 'Content',
                        route: null
                    })
                }
                break

            case 'settings':
                crumbs.push({
                    label: 'Settings',
                    route: { name: 'admin.settings' }
                })

                if (segments[1]) {
                    const settingTypes = {
                        'general': 'General Settings',
                        'users': 'User Settings',
                        'payments': 'Payment Settings',
                        'notifications': 'Notification Settings',
                        'security': 'Security Settings'
                    }

                    crumbs.push({
                        label: settingTypes[segments[1]] || 'Settings',
                        route: null
                    })
                }
                break

            case 'profile':
                crumbs.push({
                    label: 'Profile',
                    route: null
                })
                break

            case 'analytics':
                crumbs.push({
                    label: 'Analytics',
                    route: { name: 'admin.analytics' }
                })
                break

            default:
                // Fallback for unknown routes
                if (route.meta?.breadcrumb) {
                    crumbs.push({
                        label: route.meta.breadcrumb,
                        route: null
                    })
                }
        }
    }

    return crumbs
})
</script>