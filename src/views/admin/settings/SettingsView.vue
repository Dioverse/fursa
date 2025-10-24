<template>
	<div class="space-y-6">
		<!-- Header -->
		<div class="flex items-center justify-between">
			<div>
				<h1 class="text-2xl font-bold text-gray-900">Settings</h1>
				<p class="mt-1 text-sm text-gray-600">Configure platform-wide preferences and services</p>
			</div>
		</div>

		<!-- Tabs -->
		<div class="border-b border-gray-200">
			<nav class="-mb-px flex space-x-6" aria-label="Tabs">
				<button
					v-for="tab in tabs"
					:key="tab.id"
					@click="setActive(tab.id)"
					:class="[
						activeTab === tab.id
							? 'border-primary-600 text-primary-600'
							: 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
						'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
					]"
				>
					<component :is="tab.icon" class="w-4 h-4 inline mr-2" />
					{{ tab.label }}
				</button>
			</nav>
		</div>

		<!-- Panels -->
		<div>
			<component :is="currentView" />
		</div>
	</div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Cog6ToothIcon, BellIcon, MapIcon } from '@heroicons/vue/24/outline'

// Import sub-views synchronously to avoid async root runtime issues in transitions
import LogisticsSettings from './partials/LogisticsSettings.vue'
import NotificationSettings from './partials/NotificationSettings.vue'
import SystemSettings from './partials/SystemSettings.vue'

const route = useRoute()
const router = useRouter()

const tabs = [
	{ id: 'logistics', label: 'Logistics', icon: MapIcon, view: LogisticsSettings },
	{ id: 'notifications', label: 'Notifications', icon: BellIcon, view: NotificationSettings },
	{ id: 'system', label: 'System', icon: Cog6ToothIcon, view: SystemSettings },
]

const activeTab = computed(() => route.query.tab ?? 'logistics')

const currentView = computed(() => {
	const found = tabs.find((t) => t.id === activeTab.value)
	return found ? found.view : LogisticsSettings
})

const setActive = (tabId) => {
	router.replace({ query: { ...route.query, tab: tabId } })
}
</script>

<style scoped>
</style>

