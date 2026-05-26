<template>
    <div class="bg-card p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-jakartaSemiBold text-text-primary mb-2">Tanda Tangan Digital</h3>
        <p class="text-sm font-jakarta text-text-secondary mb-4">
            Upload tanda tangan digital Anda. Ini akan digunakan untuk menandatangani dokumen seperti Laporan Logbook.
        </p>

        <div v-if="hasSignature && !isEditing" class="flex flex-col items-start gap-4">
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <span class="text-xs font-jakartaSemiBold text-success flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Tanda tangan sudah diupload
                </span>
                <p class="text-xs font-jakarta text-text-secondary">Anda dapat memperbarui tanda tangan dengan mengklik tombol di bawah.</p>
            </div>
            <button @click="isEditing = true" class="text-sm font-semibold text-primary hover:text-primary-hover transition-colors">
                Perbarui Tanda Tangan
            </button>
        </div>

        <form v-else @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-2">Upload File Tanda Tangan (PNG)</label>
                <input
                    type="file"
                    accept="image/*"
                    @change="handleFileChange"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-colors"
                />
                <p v-if="form.errors.signature" class="text-xs text-danger mt-1">{{ form.errors.signature }}</p>
                <p v-if="error" class="text-xs text-danger mt-1">{{ error }}</p>
            </div>
            
            <div v-if="preview" class="mt-4">
                <p class="text-xs font-semibold text-text-secondary mb-2">Preview:</p>
                <img :src="preview" class="h-20 object-contain bg-white border border-gray-200 p-2 rounded" alt="Signature Preview" />
            </div>

            <div class="flex gap-3">
                <button
                    v-if="hasSignature"
                    type="button"
                    @click="cancelEdit"
                    class="px-4 py-2 text-sm font-medium text-text-secondary border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    :disabled="form.processing || !form.signature"
                    class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Tanda Tangan' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    hasSignature: {
        type: Boolean,
        default: false,
    }
});

const isEditing = ref(false);
const preview = ref(null);
const error = ref(null);

const form = useForm({
    signature: '',
});

function handleFileChange(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        error.value = 'Harap upload file gambar.';
        return;
    }
    
    error.value = null;

    const reader = new FileReader();
    reader.onload = (e) => {
        preview.value = e.target.result;
        form.signature = e.target.result; // base64 string
    };
    reader.readAsDataURL(file);
}

function cancelEdit() {
    isEditing.value = false;
    preview.value = null;
    form.reset();
}

function submit() {
    form.post('/signature', {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
            preview.value = null;
            form.reset();
        },
    });
}
</script>
