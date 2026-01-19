<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    thumbnail: null,
});

const submit = () => {
    form.post(route('admin.academy.store'));
};
</script>

<template>
    <Head title="Create Course" />

    <AdminLayout>
        <template #header>
            Create New Course
        </template>

        <div class="max-w-2xl mx-auto bg-gray-800 p-8 rounded-lg shadow border border-gray-700">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-400">Thumbnail Image</label>
                    <input 
                        type="file" 
                        @input="form.thumbnail = $event.target.files[0]"
                        class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-primary file:text-white hover:file:bg-violet-600"
                    />
                    <div v-if="form.errors.thumbnail" class="text-red-500 text-xs mt-1">{{ form.errors.thumbnail }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400">Course Title</label>
                    <input v-model="form.title" type="text" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                    <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                </div>

                 <div>
                    <label class="block text-sm font-medium text-gray-400">Description</label>
                    <textarea v-model="form.description" rows="4" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50"></textarea>
                    <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-700">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white font-bold rounded shadow hover:opacity-90 transition disabled:opacity-50"
                    >
                        Create Course
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
