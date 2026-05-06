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
        placeholder="411275520..."
        :error="form.errors.nim"
        required
      />
      <SelectField
        id="reg-prodi"
        v-model="form.prodi"
        label="Program Studi"
        placeholder="Pilih Program Studi"
        :options="prodiOptions"
        :error="form.errors.prodi"
        required
      />
    </div>
  </FormSection>
</template>

<script setup>
import FormSection from '@/Components/FormSection.vue';
import InputField from '@/Components/InputField.vue';
import SelectField from '@/Components/SelectField.vue';

const prodiOptions = [
  // FICT
  { value: 'S1 Informatika', label: 'S1 Informatika' },
  { value: 'S1 Sistem Informasi', label: 'S1 Sistem Informasi' },
  { value: 'S1 Teknologi Informasi', label: 'S1 Teknologi Informasi' },
  // FMB
  { value: 'S1 Akuntansi', label: 'S1 Akuntansi' },
  { value: 'S1 Manajemen', label: 'S1 Manajemen' },
  { value: 'S1 Pariwisata', label: 'S1 Pariwisata' },
  // FHS
  { value: 'S1 Keperawatan', label: 'S1 Keperawatan' },
  { value: 'S1 Kebidanan', label: 'S1 Kebidanan' },
  { value: 'Profesi Ners', label: 'Profesi Ners' },
  { value: 'D3 Keperawatan', label: 'D3 Keperawatan' },
  { value: 'D3 Kebidanan', label: 'D3 Kebidanan' },
  // FESS
  { value: 'S1 Pendidikan Bahasa Inggris', label: 'S1 Pendidikan Bahasa Inggris' },
  { value: 'S1 PGSD', label: 'S1 PGSD' },
  { value: 'S1 PGPAUD', label: 'S1 PGPAUD' },
  { value: 'S1 Ilmu Komunikasi', label: 'S1 Ilmu Komunikasi' },
  { value: 'S1 Psikologi', label: 'S1 Psikologi' },
];

defineProps({
  form: {
    type: Object,
    required: true,
  },
});
</script>
