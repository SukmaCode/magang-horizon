<template>
  <div
    :class="[
      'bg-card rounded-2xl shadow-sm border border-gray-100',
      'transition-all duration-300 ease-out',
      hoverable ? 'hover:shadow-md hover:-translate-y-0.5' : '',
      padding,
    ]"
  >
    <slot />
  </div>
</template>

<script setup>
defineProps({
  hoverable: { type: Boolean, default: false },
  padding: { type: String, default: 'p-6' },
});
</script>
