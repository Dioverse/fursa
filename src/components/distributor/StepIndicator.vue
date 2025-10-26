<!-- Step Indicator -->
<template>
    <!-- Wrapper -->
    <div class="relative w-full mb-8">
        <!-- Base track -->
        <div class="absolute left-0 right-0 top-5 h-1 bg-gray-300"></div>
        <!-- Progress track -->
        <div
            class="absolute top-5 h-1 bg-green-500 transition-all duration-300"
            :style="{ width: progressWidth, left: '0' }"
        ></div>

        <!-- Steps grid -->
        <div
            class="relative grid gap-0"
            :style="{ gridTemplateColumns: `repeat(${steps.length}, minmax(0, 1fr))` }"
        >
            <div
                v-for="step in steps"
                :key="step.number"
                class="flex flex-col items-center text-center"
            >
                <div
                    class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-all duration-300 shadow-sm"
                    :class="getStepClass(step.number)"
                    :aria-current="step.number === currentStep ? 'step' : undefined"
                >
                    <font-awesome-icon v-if="step.number < currentStep" icon="check" class="text-white" />
                    <span v-else>{{ step.number }}</span>
                </div>
                <p
                    class="text-xs mt-2 truncate max-w-[8rem]"
                    :class="step.number <= currentStep ? 'text-gray-800' : 'text-gray-400'"
                    :title="step.label"
                >
                    {{ step.label }}
                </p>
            </div>
        </div>
    </div>
  
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    currentStep: {
        type: Number,
        required: true
    },
    steps: {
        type: Array,
        default: () => [
            { number: 1, label: 'Business Info' },
            { number: 2, label: 'Contact Person' },
            { number: 3, label: 'Distribution' },
            { number: 4, label: 'Product Focus' },
            { number: 5, label: 'Banking & KYC' },
            { number: 6, label: 'Review & Submit' }
        ]
    }
})

const getStepClass = (stepNumber) => {
    if (stepNumber < props.currentStep) {
        return 'bg-green-500 text-white'
    } else if (stepNumber === props.currentStep) {
        return 'bg-primary text-white'
    } else {
        return 'bg-gray-300 text-gray-600'
    }
}

// Compute progress line width based on current step
const progressWidth = computed(() => {
    const total = props.steps?.length ?? 0
    if (total <= 1) return '0%'
    const completedSegments = Math.max(0, Math.min(total - 1, props.currentStep - 1))
    const pct = (completedSegments / (total - 1)) * 100
    return `${pct}%`
})
</script>