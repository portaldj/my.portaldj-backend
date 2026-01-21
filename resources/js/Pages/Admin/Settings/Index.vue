<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    pool_is_pro: props.settings.pool_is_pro === '1',
    academy_is_pro: props.settings.academy_is_pro === '1',
    assistant_is_pro: props.settings.assistant_is_pro === '1',
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => alert('Settings saved!'),
    });
};
</script>

<template>
    <Head title="Global Settings" />

    <AdminLayout>
        <template #header>
            Global Settings
        </template>

        <div class="bg-gray-800 rounded-lg shadow border border-gray-700 p-6 max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-white">PRO Features Access Control</h3>
                    <p class="text-sm text-gray-400">Decide which sections require a PRO subscription globally.</p>
                </div>

                <div class="flex items-center space-x-3">
                    <input 
                        id="pool_is_pro" 
                        type="checkbox" 
                        v-model="form.pool_is_pro"
                        class="rounded border-gray-600 bg-gray-700 text-brand-primary focus:ring-brand-primary"
                    >
                    <label for="pool_is_pro" class="text-gray-200">
                        Make <strong>Music Pool</strong> PRO Only
                    </label>
                </div>

                <div class="flex items-center space-x-3">
                    <input 
                        id="academy_is_pro" 
                        type="checkbox" 
                        v-model="form.academy_is_pro"
                        class="rounded border-gray-600 bg-gray-700 text-brand-primary focus:ring-brand-primary"
                    >
                    <label for="academy_is_pro" class="text-gray-200">
                        Make <strong>Entire Academy</strong> PRO Only
                    </label>
                </div>

                <div class="flex items-center space-x-3">
                    <input 
                        id="assistant_is_pro" 
                        type="checkbox" 
                        v-model="form.assistant_is_pro"
                        class="rounded border-gray-600 bg-gray-700 text-brand-primary focus:ring-brand-primary"
                    >
                    <label for="assistant_is_pro" class="text-gray-200">
                        Make <strong>AI Assistant</strong> PRO Only
                    </label>
                </div>

                <div class="pt-4 border-t border-gray-700">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-4 py-2 bg-brand-primary text-white font-bold rounded hover:bg-brand-secondary transition disabled:opacity-50"
                    >
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
