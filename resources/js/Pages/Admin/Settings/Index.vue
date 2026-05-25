<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';

const props = defineProps({
    settings: Object,
});

const activeTab = ref('general');

const tabs = computed(() => {
    return Object.keys(props.settings).map(key => {
        let label = key;
        if (key === 'general') label = 'Umum';
        if (key === 'internship') label = 'Aturan Magang';
        if (key === 'system') label = 'Sistem & Notifikasi';
        return { key, label };
    });
});

// Prepare form data
const initialFormSettings = [];
for (const group in props.settings) {
    props.settings[group].forEach(setting => {
        initialFormSettings.push({
            id: setting.id,
            key: setting.key,
            value: setting.value,
            type: setting.type,
            group: setting.group,
            label: setting.label,
        });
    });
}

const form = useForm({
    settings: initialFormSettings
});

const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const submit = () => {
    form.post('/admin/pengaturan/update', {
        preserveScroll: true,
        onSuccess: (page) => {
            if (page.props.flash?.success) {
                toastMessage.value = page.props.flash.success;
                toastType.value = 'success';
                showToast.value = true;
                setTimeout(() => showToast.value = false, 3000);
            }
        },
        onError: () => {
            toastMessage.value = 'Gagal menyimpan pengaturan. Periksa kembali form Anda.';
            toastType.value = 'error';
            showToast.value = true;
            setTimeout(() => showToast.value = false, 3000);
        }
    });
};

const getSettingsByGroup = (groupKey) => {
    return form.settings.filter(s => s.group === groupKey);
};
</script>

<template>
    <Head title="Pengaturan Aplikasi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengaturan Aplikasi</h2>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col md:flex-row">
                    <!-- Sidebar Tabs -->
                    <div class="w-full md:w-1/4 bg-gray-50 border-r border-gray-200">
                        <nav class="flex flex-row md:flex-col items-center md:items-start md:p-2 p-2 md:space-y-2">
                            <button 
                                v-for="tab in tabs" 
                                :key="tab.key"
                                @click="activeTab = tab.key"
                                :class="[
                                    'w-full text-left px-4 py-2 text-sm rounded-md transition-colors font-jakartaSemiBold',
                                    activeTab === tab.key 
                                        ? 'bg-primary/10 text-primary' 
                                        : 'text-gray-600 hover:bg-primary/10'
                                ]"
                            >
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>

                    <!-- Content -->
                    <div class="w-full md:w-3/4 p-6">
                        <form @submit.prevent="submit">
                            <div v-for="tab in tabs" :key="tab.key" v-show="activeTab === tab.key" class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2">
                                    Pengaturan {{ tab.label }}
                                </h3>

                                <div v-for="(setting, index) in getSettingsByGroup(tab.key)" :key="setting.id" class="flex flex-col space-y-1">
                                    <label :for="'setting_' + setting.id" class="text-sm font-jakartaSemiBold">
                                        {{ setting.label || setting.key }}
                                    </label>

                                    <!-- Render Input Based on Type -->
                                    <div v-if="setting.type === 'text'">
                                        <input 
                                            :id="'setting_' + setting.id"
                                            v-model="setting.value"
                                            type="text" 
                                            class="mt-1 px-4 py-2 block w-full border-b font-jakarta border-gray-300 sm:text-sm focus:outline-none focus:ring-0 focus:border-b-2 focus:border-primary"
                                        />
                                    </div>
                                    
                                    <div v-else-if="setting.type === 'number'">
                                        <input 
                                            :id="'setting_' + setting.id"
                                            v-model="setting.value"
                                            type="number" 
                                            class="mt-1 px-4 py-2 block w-full border-b font-jakarta border-gray-300 sm:text-sm focus:outline-none focus:ring-0 focus:border-b-2 focus:border-primary"
                                        />
                                    </div>

                                    <div v-else-if="setting.type === 'image'" class="mt-2">
                                        <div class="flex items-center gap-4">
                                            <div v-if="typeof setting.value === 'string' && setting.value" class="w-16 h-16 shrink-0 border border-gray-200 rounded-md overflow-hidden bg-gray-50 flex items-center justify-center">
                                                <img :src="setting.value" class="max-w-full max-h-full object-contain" />
                                            </div>
                                            <div class="flex-1">
                                                <input 
                                                    :id="'setting_' + setting.id"
                                                    type="file" 
                                                    accept="image/*"
                                                    @change="e => { if (e.target.files[0]) setting.value = e.target.files[0]; }"
                                                    class="block w-full text-sm font-jakarta text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-jakartaSemiBold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"
                                                />
                                                <p class="mt-1 font-jakarta text-xs text-gray-500">Upload gambar baru (PNG/JPG) untuk mengganti.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else-if="setting.type === 'boolean'" class="flex items-center mt-2">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                v-model="setting.value" 
                                                :true-value="'1'"
                                                :false-value="'0'"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-sm font-jakartaSemiBold">
                                                {{ setting.value === '1' ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </label>
                                    </div>

                                    <div v-else-if="setting.type === 'textarea'">
                                        <textarea 
                                            :id="'setting_' + setting.id"
                                            v-model="setting.value"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                        ></textarea>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="mt-8 pt-5 flex justify-end">
                                <ButtonPrimary type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Simpan Pengaturan
                                </ButtonPrimary>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Toast -->
        <Transition 
            enter-active-class="transition ease-out duration-300" 
            enter-from-class="translate-y-4 opacity-0" 
            enter-to-class="translate-y-0 opacity-100" 
            leave-active-class="transition ease-in duration-200" 
            leave-from-class="translate-y-0 opacity-100" 
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="showToast" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50', toastType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white']">
                {{ toastMessage }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>
