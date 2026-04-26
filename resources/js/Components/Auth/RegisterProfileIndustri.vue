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
    <div class="grid grid-cols-3 gap-3">
      <InputField
        id="reg-lat"
        v-model="form.latitude"
        label="Latitude"
        type="number"
        placeholder="-6.2088"
        :error="form.errors.latitude"
      />
      <InputField
        id="reg-lng"
        v-model="form.longitude"
        label="Longitude"
        type="number"
        placeholder="106.845"
        :error="form.errors.longitude"
      />
      <InputField
        id="reg-radius"
        v-model="form.geofence_radius"
        label="Radius (m)"
        type="number"
        placeholder="500"
        :error="form.errors.geofence_radius"
        hint="Meter"
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
