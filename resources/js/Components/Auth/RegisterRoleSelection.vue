<template>
  <FormSection title="Pilih Peran Anda" description="Setiap peran memiliki akses dan fitur berbeda">
    <div class="grid grid-cols-2 gap-3">
      <button
        v-for="role in roleOptions"
        :key="role.value"
        type="button"
        @click="$emit('update:modelValue', role.value)"
        :class="[
          'relative flex flex-col items-center gap-2.5 p-5 rounded-xl border text-center transition-all duration-200',
          modelValue === role.value
            ? 'border-primary bg-primary/5 shadow-sm'
            : 'border-gray-100 bg-white hover:border-gray-200 hover:shadow-sm',
        ]"
      >
        <!-- Selection dot -->
        <div
          :class="[
            'absolute top-3 right-3 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200',
            modelValue === role.value
              ? 'border-primary bg-primary'
              : 'border-gray-200',
          ]"
        >
          <svg v-if="modelValue === role.value" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
        </div>
        <!-- Icon -->
        <div
          :class="[
            'flex items-center justify-center w-11 h-11 rounded-xl transition-colors duration-200',
            modelValue === role.value ? 'bg-primary/10 text-primary' : 'bg-gray-50 text-text-secondary',
          ]"
        >
          <component :is="role.icon" class="w-5 h-5" />
        </div>
        <div>
          <p
            :class="[
              'text-sm font-jakartaSemiBold transition-colors',
              modelValue === role.value ? 'text-primary' : 'text-text-primary',
            ]"
          >
            {{ role.label }}
          </p>
          <p class="text-[11px] text-text-secondary mt-0.5">{{ role.desc }}</p>
        </div>
      </button>
    </div>

    <ButtonPrimary
      type="button"
      @click="$emit('next')"
      :disabled="!modelValue"
      full-width
      class="mt-2"
    >
      Lanjutkan
    </ButtonPrimary>
  </FormSection>
</template>

<script setup>
import { h } from 'vue';
import FormSection from '@/Components/FormSection.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';

defineProps({
  modelValue: {
    type: String,
    required: true,
  },
});

defineEmits(['update:modelValue', 'next']);

// ─── Icons (inline SVG render functions) ────────────────────
const StudentIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-5 h-5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 14l9-5-9-5-9 5 9 5z' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 14l6.16-3.422A12.083 12.083 0 0112 21a12.083 12.083 0 01-6.16-10.422L12 14z' }),
  ]),
};
const IndustryIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-5 h-5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' }),
  ]),
};
const SupervisorIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-5 h-5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' }),
  ]),
};
const ProdiIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-5 h-5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' }),
  ]),
};

// ─── Role Options ───────────────────────────────────────────
const roleOptions = [
  { value: 'mahasiswa', label: 'Mahasiswa', desc: 'Peserta magang', icon: StudentIcon },
  { value: 'supervisor_industri', label: 'Industri', desc: 'Perusahaan mitra', icon: IndustryIcon },
  { value: 'dosen_pembimbing', label: 'Dosen Pembimbing', desc: 'Pembimbing kampus', icon: SupervisorIcon },
  { value: 'dosen_prodi', label: 'Dosen Prodi', desc: 'Koordinator prodi', icon: ProdiIcon },
];
</script>
