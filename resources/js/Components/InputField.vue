<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-sm font-medium text-text-primary mb-1.5">
      {{ label }}
      <span v-if="required" class="text-accent">*</span>
    </label>
    <div class="relative">
      <div v-if="icon" class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
        <component :is="icon" class="w-4.5 h-4.5 text-text-secondary" />
      </div>
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="[
          'w-full rounded-xl border bg-white px-4 py-3 text-sm text-text-primary placeholder:text-text-secondary/50',
          'transition-all duration-200 ease-out',
          'focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent',
          'disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-surface',
          icon ? 'pl-11' : '',
          error
            ? 'border-danger ring-2 ring-danger/20'
            : 'border-gray-200 hover:border-gray-300',
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur')"
      />
    </div>
    <p v-if="error" class="mt-1.5 text-xs text-danger flex items-center gap-1">
      <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      {{ error }}
    </p>
    <p v-else-if="hint" class="mt-1.5 text-xs text-text-secondary">{{ hint }}</p>
  </div>
</template>

<script setup>
defineProps({
  id: { type: String, required: true },
  label: { type: String, default: '' },
  type: { type: String, default: 'text' },
  modelValue: { type: [String, Number], default: '' },
  placeholder: { type: String, default: '' },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  icon: { type: [Object, null], default: null },
});

defineEmits(['update:modelValue', 'blur']);
</script>
