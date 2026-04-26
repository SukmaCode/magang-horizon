<template>
  <FormSection title="Data Mahasiswa" description="Lengkapi profil mahasiswa Anda">
    <InputField
      id="reg-nama"
      v-model="form.nama_lengkap"
      label="Nama Lengkap"
      placeholder="Galang Sukmagama"
      :error="form.errors.nama_lengkap"
      required
    />
    <div class="grid grid-cols-2 gap-3">
      <InputField
        id="reg-nim"
        v-model="form.nim"
        label="NIM"
        placeholder="2024001"
        :error="form.errors.nim"
        required
      />
      <InputField
        id="reg-prodi"
        v-model="form.prodi"
        label="Program Studi"
        placeholder="Teknik Informatika"
        :error="form.errors.prodi"
      />
    </div>
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
