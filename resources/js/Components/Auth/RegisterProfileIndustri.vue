<template>
  <FormSection title="Data Perusahaan" description="Lengkapi informasi perusahaan Anda">
    <InputField
      id="reg-perusahaan"
      v-model="form.nama_perusahaan"
      label="Nama Perusahaan"
      placeholder="PT Maju Mapan"
      :error="form.errors.nama_perusahaan"
      required
    />
    <InputField
      id="reg-alamat"
      v-model="form.alamat"
      label="Alamat"
      placeholder="Jl. Sudirman No. 123, Jakarta"
      :error="form.errors.alamat"
    />
    <InputField
      id="reg-kontak"
      v-model="form.kontak_person"
      label="Nama Supervisor"
      placeholder="Budi Santoso"
      :error="form.errors.kontak_person"
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
