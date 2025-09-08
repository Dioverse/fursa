<!-- DistributorStatsCards.vue -->
<template>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Distributors -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <UsersIcon class="h-6 w-6 text-blue-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Distributors
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ formatNumber(stats.totalDistributors) }}
                                </div>
                                <div v-if="stats.totalDistributorsGrowth" class="ml-2 flex items-baseline text-sm">
                                    <component :is="stats.totalDistributorsGrowth >= 0 ? ArrowUpIcon : ArrowDownIcon"
                                        :class="[
                                            stats.totalDistributorsGrowth >= 0 ? 'text-green-500' : 'text-red-500',
                                            'self-center flex-shrink-0 h-3 w-3'
                                        ]" />
                                    <span :class="[
                                        stats.totalDistributorsGrowth >= 0 ? 'text-green-600' : 'text-red-600',
                                        'font-medium'
                                    ]">
                                        {{ Math.abs(stats.totalDistributorsGrowth) }}%
                                    </span>
                                    <span class="ml-1 text-gray-500">vs last month</span>
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Applications -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <ClockIcon class="h-6 w-6 text-yellow-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Pending Applications
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ formatNumber(stats.pendingApplications) }}
                                </div>
                                <div v-if="stats.pendingApplicationsGrowth" class="ml-2 flex items-baseline text-sm">
                                    <component :is="stats.pendingApplicationsGrowth >= 0 ? ArrowUpIcon : ArrowDownIcon"
                                        :class="[
                                            stats.pendingApplicationsGrowth >= 0 ? 'text-yellow-500' : 'text-green-500',
                                            'self-center flex-shrink-0 h-3 w-3'
                                        ]" />
                                    <span :class="[
                                        stats.pendingApplicationsGrowth >= 0 ? 'text-yellow-600' : 'text-green-600',
                                        'font-medium'
                                    ]">
                                        {{ Math.abs(stats.pendingApplicationsGrowth) }}%
                                    </span>
                                    <span class="ml-1 text-gray-500">vs last month</span>
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <router-link :to="{ name: 'admin.distributors', query: { status: 'pending' } }"
                        class="font-medium text-primary-700 hover:text-primary-900">
                        Review applications
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Active Distributors -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <CheckCircleIcon class="h-6 w-6 text-green-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Active Distributors
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ formatNumber(stats.activeDistributors) }}
                                </div>
                                <div v-if="stats.activeDistributorsGrowth" class="ml-2 flex items-baseline text-sm">
                                    <component :is="stats.activeDistributorsGrowth >= 0 ? ArrowUpIcon : ArrowDownIcon"
                                        :class="[
                                            stats.activeDistributorsGrowth >= 0 ? 'text-green-500' : 'text-red-500',
                                            'self-center flex-shrink-0 h-3 w-3'
                                        ]" />
                                    <span :class="[
                                        stats.activeDistributorsGrowth >= 0 ? 'text-green-600' : 'text-red-600',
                                        'font-medium'
                                    ]">
                                        {{ Math.abs(stats.activeDistributorsGrowth) }}%
                                    </span>
                                    <span class="ml-1 text-gray-500">vs last month</span>
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-gray-500">
                        {{ ((stats.activeDistributors / stats.totalDistributors) * 100).toFixed(1) }}% of total
                    </span>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <CurrencyDollarIcon class="h-6 w-6 text-green-500" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Revenue
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    ${{ formatCurrency(stats.totalRevenue) }}
                                </div>
                                <div v-if="stats.revenueGrowth" class="ml-2 flex items-baseline text-sm">
                                    <component :is="stats.revenueGrowth >= 0 ? ArrowUpIcon : ArrowDownIcon" :class="[
                                        stats.revenueGrowth >= 0 ? 'text-green-500' : 'text-red-500',
                                        'self-center flex-shrink-0 h-3 w-3'
                                    ]" />
                                    <span :class="[
                                        stats.revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600',
                                        'font-medium'
                                    ]">
                                        {{ Math.abs(stats.revenueGrowth) }}%
                                    </span>
                                    <span class="ml-1 text-gray-500">vs last month</span>
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-gray-500">
                        Avg: ${{ formatCurrency(stats.averageRevenuePerDistributor || 0) }} per distributor
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div v-if="showAdditionalStats" class="grid grid-cols-1 gap-5 sm:grid-cols-3 mt-5">
        <!-- Top Performer -->
        <div class="bg-gradient-to-r from-purple-400 to-purple-600 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <TrophyIcon class="h-6 w-6 text-white" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-purple-100 truncate">
                                Top Performer
                            </dt>
                            <dd class="text-lg font-semibold text-white">
                                {{ stats.topPerformer?.name || 'N/A' }}
                            </dd>
                            <dd class="text-sm text-purple-100">
                                ${{ formatCurrency(stats.topPerformer?.revenue || 0) }} revenue
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Performance -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <ChartBarIcon class="h-6 w-6 text-indigo-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Avg Performance
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ (stats.averagePerformanceScore || 0).toFixed(1) }}%
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Territories Covered -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <GlobeAltIcon class="h-6 w-6 text-blue-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Territories Covered
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ stats.territoriesCovered || 0 }}
                            </dd>
                            <dd class="text-sm text-gray-500">
                                of {{ stats.totalTerritories || 0 }} total
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

// Icons
import {
    UsersIcon,
    ClockIcon,
    CheckCircleIcon,
    CurrencyDollarIcon,
    ArrowUpIcon,
    ArrowDownIcon,
    TrophyIcon,
    ChartBarIcon,
    GlobeAltIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    showAdditionalStats: {
        type: Boolean,
        default: true
    }
})

// Methods
const formatNumber = (number) => {
    if (!number) return '0'
    return new Intl.NumberFormat().format(number)
}

const formatCurrency = (amount) => {
    if (!amount) return '0'
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount)
}
</script>

<style scoped>
.gradient-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>