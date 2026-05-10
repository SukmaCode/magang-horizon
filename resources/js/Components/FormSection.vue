<template>
  <div class="space-y-5">
    <div v-if="title || description" class="space-y-1">
      <h3 v-if="title" class="text-base font-jakartaSemiBold text-text-primary">{{ title }}</h3>
      <p v-if="description" class="text-sm font-jakarta text-text-secondary">{{ description }}</p>
    </div>
    <div class="space-y-4">
      <slot />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: '' },
  description: { type: String, default: '' },
});
</script>
