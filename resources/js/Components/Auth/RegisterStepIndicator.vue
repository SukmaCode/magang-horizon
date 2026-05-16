<template>
    <div class="px-8 pb-4">
        <div class="flex items-center justify-center gap-3">
            <template v-for="(step, idx) in steps" :key="idx">
                <!-- Step circle -->
                <button
                    type="button"
                    @click="$emit('goToStep', idx)"
                    :disabled="!canGoToStep(idx)"
                    :class="[
                        'relative flex items-center justify-center w-9 h-9 rounded-sm text-xs font-jakartaSemiBold transition-all duration-300',
                        idx === currentStep
                            ? 'animated-light-effect bg-primary text-white shadow-lg shadow-primary/30 scale-125'
                            : idx < currentStep
                              ? 'bg-success text-white '
                              : 'bg-gray-100 text-text-secondary',
                        canGoToStep(idx) ? 'cursor-pointer' : 'cursor-default',
                    ]"
                >
                    <svg
                        v-if="idx < currentStep"
                        class="w-4 h-4"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span v-else>{{ idx + 1 }}</span>
                </button>
                <!-- Connector line -->
                <div
                    v-if="idx < steps.length - 1"
                    :class="[
                        'h-0.5 w-12 rounded-full transition-all duration-500',
                        idx < currentStep ? 'bg-success' : 'bg-gray-200',
                    ]"
                ></div>
            </template>
        </div>
        <div class="mt-3 text-center">
            <p
                class="text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider"
            >
                {{ steps[currentStep] }}
            </p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    steps: {
        type: Array,
        required: true,
    },
    currentStep: {
        type: Number,
        required: true,
    },
    canGoToStep: {
        type: Function,
        required: true,
    },
});

defineEmits(["goToStep"]);
</script>
