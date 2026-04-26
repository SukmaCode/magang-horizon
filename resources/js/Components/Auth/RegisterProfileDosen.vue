<template>
  <FormSection title="Data Dosen" description="Lengkapi profil dosen Anda">
    <InputField
      id="reg-nama-dosen"
      v-model="form.nama_dosen"
      label="Nama Lengkap"
      placeholder="Dr. Siti Aminah"
      :error="form.errors.nama_dosen"
      required
    />
    <InputField
      id="reg-nip"
      v-model="form.nip"
      label="NIP"
      placeholder="198501012020011001"
      :error="form.errors.nip"
      required
    />
  </FormSection>
</template>

<script setup>
import FormSection from '@/Components/FormSection.vue';
import InputField from '@/Components/InputField.vue';

defineProps({
  form: {
    type: Object,
    required: true,
  },
});
</script>
