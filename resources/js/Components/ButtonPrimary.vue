<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'relative inline-flex items-center justify-center gap-2 font-jakartaSemiBold text-sm rounded-sm px-5 py-2.5',
      'transition-all duration-200 ease-out',
      'focus:outline-none focus:ring-2 focus:ring-offset-2',
      'disabled:cursor-not-allowed',
      variantClasses,
      fullWidth ? 'w-full' : '',
    ]"
  >
    <!-- Spinner -->
    <svg
      v-if="loading"
      class="animate-spin w-4.5 h-4.5"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
    </svg>
    <span :class="{ 'opacity-0': loading && !$slots.default }">
      <slot />
    </span>
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' },
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  fullWidth: { type: Boolean, default: false },
});

const variantClasses = computed(() => {
  const variants = {
    primary: [
      'bg-primary text-white shadow-lg shadow-primary/25',
      'hover:bg-primary-hover hover:shadow-xl hover:shadow-primary/30',
      'focus:ring-primary/50',
      'disabled:bg-primary/60 disabled:shadow-none',
      'active:scale-[0.98]',
    ].join(' '),
    secondary: [
      'bg-white text-text-primary border border-gray-200 shadow-sm',
      'hover:bg-surface hover:border-gray-300',
      'focus:ring-gray-300',
      'disabled:bg-gray-100',
      'active:scale-[0.98]',
    ].join(' '),
    ghost: [
      'text-text-secondary',
      'hover:bg-surface hover:text-text-primary',
      'focus:ring-gray-300',
    ].join(' '),
  };
  return variants[props.variant] || variants.primary;
});
</script>
