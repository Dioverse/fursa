<!-- DistributorFilters.vue -->
<template>
    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Search -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </div>
                <input :value="filters.search" @input="updateFilter('search', $event.target.value)" type="text"
                    placeholder="Search distributors..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm" />
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select :value="filters.status" @change="updateFilter('status', $event.target.value)"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="rejected">Rejected</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <ChevronDownIcon class="h-4 w-4 text-gray-400" />
                </div>
            </div>

            <!-- Territory Filter -->
            <div class="relative">
                <select :value="filters.territory" @change="updateFilter('territory', $event.target.value)"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    <option value="">All Territories</option>
                    <option v-for="territory in territories" :key="territory.id" :value="territory.id">
                        {{ territory.name }} ({{ territory.state }})
                    </option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <ChevronDownIcon class="h-4 w-4 text-gray-400" />
                </div>
            </div>

            <!-- Performance Filter -->
            <div class="relative">
                <select :value="filters.performance" @change="updateFilter('performance', $event.target.value)"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    <option value="">All Performance</option>
                    <option value="excellent">Excellent (90%+)</option>
                    <option value="good">Good (70-89%)</option>
                    <option value="average">Average (50-69%)</option>
                    <option value="poor">Poor (&lt;50%)</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <ChevronDownIcon class="h-4 w-4 text-gray-400" />
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="relative">
                <select :value="filters.dateRange" @change="updateFilter('dateRange', $event.target.value)"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter">This Quarter</option>
                    <option value="year">This Year</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <ChevronDownIcon class="h-4 w-4 text-gray-400" />
                </div>
            </div>
        </div>

        <!-- Advanced Filters Toggle -->
        <div class="mt-4 flex items-center justify-between">
            <button @click="showAdvancedFilters = !showAdvancedFilters"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <AdjustmentsHorizontalIcon class="w-4 h-4 mr-2" />
                Advanced Filters
                <ChevronDownIcon :class="[
                    'w-4 h-4 ml-1 transition-transform duration-200',
                    showAdvancedFilters ? 'rotate-180' : ''
                ]" />
            </button>

            <!-- Active Filters Count -->
            <div v-if="activeFiltersCount > 0" class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">
                    {{ activeFiltersCount }} filter{{ activeFiltersCount > 1 ? 's' : '' }} active
                </span>
                <button @click="clearAllFilters"
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Clear all
                    <XMarkIcon class="w-3 h-3 ml-1" />
                </button>
            </div>
        </div>

        <!-- Advanced Filters Panel -->
        <Transition enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in" leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0">
            <div v-if="showAdvancedFilters" class="mt-4 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Revenue Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Revenue Range
                        </label>
                        <div class="flex space-x-2">
                            <input :value="filters.minRevenue" @input="updateFilter('minRevenue', $event.target.value)"
                                type="number" placeholder="Min"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm" />
                            <input :value="filters.maxRevenue" @input="updateFilter('maxRevenue', $event.target.value)"
                                type="number" placeholder="Max"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm" />
                        </div>
                    </div>

                    <!-- Join Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Join Date
                        </label>
                        <div class="flex space-x-2">
                            <input :value="filters.joinDateFrom"
                                @input="updateFilter('joinDateFrom', $event.target.value)" type="date"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm" />
                            <input :value="filters.joinDateTo" @input="updateFilter('joinDateTo', $event.target.value)"
                                type="date"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm" />
                        </div>
                    </div>

                    <!-- Business Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Business Type
                        </label>
                        <select :value="filters.businessType"
                            @change="updateFilter('businessType', $event.target.value)"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="individual">Individual</option>
                            <option value="partnership">Partnership</option>
                            <option value="corporation">Corporation</option>
                            <option value="llc">LLC</option>
                        </select>
                    </div>

                    <!-- Experience Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Experience Level
                        </label>
                        <select :value="filters.experienceLevel"
                            @change="updateFilter('experienceLevel', $event.target.value)"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">All Levels</option>
                            <option value="beginner">Beginner (0-2 years)</option>
                            <option value="intermediate">Intermediate (2-5 years)</option>
                            <option value="advanced">Advanced (5+ years)</option>
                        </select>
                    </div>
                </div>

                <!-- Apply Advanced Filters Button -->
                <div class="mt-4 flex justify-end space-x-3">
                    <button @click="clearAdvancedFilters"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Clear Advanced
                    </button>
                    <button @click="applyAdvancedFilters"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Apply Filters
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Icons
import {
    MagnifyingGlassIcon,
    ChevronDownIcon,
    AdjustmentsHorizontalIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
    filters: {
        type: Object,
        required: true
    },
    territories: {
        type: Array,
        default: () => []
    }
})

// Emits
const emit = defineEmits(['update:filters', 'apply-filters', 'clear-filters'])

// Local state
const showAdvancedFilters = ref(false)

// Computed
const activeFiltersCount = computed(() => {
    const basicFilters = ['search', 'status', 'territory', 'performance', 'dateRange']
    const advancedFilters = ['minRevenue', 'maxRevenue', 'joinDateFrom', 'joinDateTo', 'businessType', 'experienceLevel']

    const activeBasic = basicFilters.filter(key => props.filters[key] && props.filters[key] !== '').length
    const activeAdvanced = advancedFilters.filter(key => props.filters[key] && props.filters[key] !== '').length

    return activeBasic + activeAdvanced
})

// Methods
const updateFilter = (key, value) => {
    const updatedFilters = { ...props.filters, [key]: value }
    emit('update:filters', updatedFilters)

    // Auto-apply basic filters (except search which should be debounced)
    if (key !== 'search') {
        emit('apply-filters')
    }
}

const clearAllFilters = () => {
    const clearedFilters = Object.keys(props.filters).reduce((acc, key) => {
        acc[key] = ''
        return acc
    }, {})

    emit('update:filters', clearedFilters)
    emit('clear-filters')
}

const clearAdvancedFilters = () => {
    const advancedFilterKeys = ['minRevenue', 'maxRevenue', 'joinDateFrom', 'joinDateTo', 'businessType', 'experienceLevel']
    const updatedFilters = { ...props.filters }

    advancedFilterKeys.forEach(key => {
        updatedFilters[key] = ''
    })

    emit('update:filters', updatedFilters)
    emit('apply-filters')
}

const applyAdvancedFilters = () => {
    emit('apply-filters')
}
</script>