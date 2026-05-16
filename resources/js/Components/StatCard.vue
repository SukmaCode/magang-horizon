<template>
    <CardContainer padding="p-6">
        <div class="flex items-center gap-4">
            <div :class="wrapperClass">
                <component :is="icon" class="w-6 h-6" />
            </div>
            <div>
                <h3
                    class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider"
                >
                    {{ title }}
                </h3>
                <p class="text-2xl font-jakartaSemiBold text-text-primary">
                    {{ value }}
                </p>
            </div>
        </div>
    </CardContainer>
</template>

<script setup>
import { computed } from 'vue';
import CardContainer from '@/Components/CardContainer.vue';

defineProps({
    title: String,
    value: Number,
    icon: Object,
    color: String,
});

const wrapperClass = computed(
    () =>
        `w-12 h-12 rounded-xl bg-${color}/10 flex items-center justify-center text-${color}`,
);
</script>
