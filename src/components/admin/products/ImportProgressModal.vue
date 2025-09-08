<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-strong max-w-md w-full animate-fade-in" @click.stop>
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ isComplete ? 'Import Complete' : 'Importing Products' }}
                </h3>
                <button v-if="!loading && isComplete" @click="closeModal"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <font-awesome-icon icon="times" class="h-5 w-5" />
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <!-- Progress Indicator -->
                <div v-if="!isComplete" class="space-y-4">
                    <!-- Main Progress Bar -->
                    <div>
                        <div class="flex justify-between text-sm font-medium text-gray-700 mb-2">
                            <span>{{ currentStep }}</span>
                            <span>{{ progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full transition-all duration-300 ease-out"
                                :style="{ width: `${progressPercentage}%` }"></div>
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 rounded-full mb-4">
                            <div class="loading-spinner w-8 h-8 border-primary-600"></div>
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ currentMessage }}
                        </p>
                        <p v-if="progress.current > 0 && progress.total > 0" class="text-xs text-gray-500 mt-1">
                            Processing {{ progress.current }} of {{ progress.total }} items
                        </p>
                    </div>

                    <!-- Stage Progress -->
                    <div class="space-y-2">
                        <div v-for="stage in importStages" :key="stage.key"
                            class="flex items-center space-x-3 p-2 rounded-md" :class="getStageClass(stage.key)">
                            <div class="flex-shrink-0">
                                <font-awesome-icon v-if="isStageComplete(stage.key)" icon="check-circle"
                                    class="h-4 w-4 text-green-500" />
                                <div v-else-if="isStageActive(stage.key)"
                                    class="loading-spinner w-4 h-4 border-primary-600"></div>
                                <div v-else class="w-4 h-4 rounded-full bg-gray-300"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">
                                {{ stage.label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Completion State -->
                <div v-else class="text-center space-y-4">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <font-awesome-icon icon="check" class="h-8 w-8 text-green-600" />
                    </div>

                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">
                            Import Completed!
                        </h4>
                        <p class="text-sm text-gray-600">
                            {{ completionMessage }}
                        </p>
                    </div>

                    <!-- Import Summary -->
                    <div v-if="importSummary" class="bg-gray-50 rounded-lg p-4 text-left">
                        <h5 class="text-sm font-medium text-gray-900 mb-3">Import Summary</h5>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-gray-500">Total Processed</div>
                                <div class="font-medium text-gray-900">
                                    {{ importSummary.total || 0 }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">Successfully Imported</div>
                                <div class="font-medium text-green-600">
                                    {{ importSummary.success || 0 }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">Skipped/Updated</div>
                                <div class="font-medium text-yellow-600">
                                    {{ importSummary.skipped || 0 }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">Errors</div>
                                <div class="font-medium text-red-600">
                                    {{ importSummary.errors || 0 }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Details -->
                    <div v-if="importSummary?.error_details?.length > 0" class="text-left">
                        <details class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <summary class="text-sm font-medium text-red-800 cursor-pointer">
                                View {{ importSummary.error_details.length }} Error(s)
                            </summary>
                            <div class="mt-3 space-y-2">
                                <div v-for="(error, index) in importSummary.error_details.slice(0, 5)" :key="index"
                                    class="text-xs text-red-700 bg-white rounded p-2">
                                    <div class="font-medium">Row {{ error.row || index + 1 }}:</div>
                                    <div>{{ error.message || error }}</div>
                                    <div v-if="error.field" class="text-red-500 mt-1">
                                        Field: {{ error.field }}
                                    </div>
                                </div>
                                <div v-if="importSummary.error_details.length > 5" class="text-xs text-red-600 italic">
                                    And {{ importSummary.error_details.length - 5 }} more errors...
                                </div>
                            </div>
                        </details>
                    </div>

                    <!-- Warning Details -->
                    <div v-if="importSummary?.warnings?.length > 0" class="text-left">
                        <details class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <summary class="text-sm font-medium text-yellow-800 cursor-pointer">
                                View {{ importSummary.warnings.length }} Warning(s)
                            </summary>
                            <div class="mt-3 space-y-2">
                                <div v-for="(warning, index) in importSummary.warnings.slice(0, 3)" :key="index"
                                    class="text-xs text-yellow-700 bg-white rounded p-2">
                                    <div class="font-medium">{{ warning.type || 'Warning' }}:</div>
                                    <div>{{ warning.message || warning }}</div>
                                </div>
                            </div>
                        </details>
                    </div>
                    <!-- Close Button -->
                    <div class="mt-4">
                        <button @click="closeModal" class="btn-primary">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    importSummary: Object,
    closeModal: Function
});
</script>
