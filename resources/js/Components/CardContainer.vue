<template>
  <div
    :class="[
      ' rounded-md',
      'transition-all duration-300 ease-out',
      hoverable ? 'hover:shadow-md hover:-translate-y-0.5' : '',
      padding,
      bgColor,
      shadow
    ]"
  >
    <slot />
  </div>
</template>

<script setup>
defineProps({
  hoverable: { type: Boolean, default: false },
  padding: { type: String, default: 'p-6' },
  bgColor: {type: String, default: 'bg-white'},
  shadow: {type: String, default: 'shadow-md'}
});
</script>
