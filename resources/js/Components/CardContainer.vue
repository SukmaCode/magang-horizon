<template>
  <div
    :class="[
      ' rounded-xl',
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
