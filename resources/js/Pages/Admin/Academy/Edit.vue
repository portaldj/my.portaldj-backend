<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
});

const form = useForm({
    title: props.course.title || '',
    description: props.course.description || '',
    thumbnail: null,
    is_pro: Boolean(props.course.is_pro),
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('admin.academy.update', props.course.id), {
        forceFormData: true,
    });
};

// Override submit to use POST with _method put for file support
const submitWithFile = () => {
    router.post(route('admin.academy.update', props.course.id), {
        _method: 'put',
        ...form.data(),
        thumbnail: form.thumbnail,
    }, {
         forceFormData: true,
    });
};
// Simpler: Just use form requests. Inertia v1.0+ handles files in put? 
// Actually, PHP doesn't read $_FILES on PUT requests easily.
// Standard Laravel/Inertia way: POST with _method: 'PUT'.
</script>

<template>
    <Head title="Edit Course" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-2">
                <span class="text-gray-400">Edit Course:</span>
                <span>{{ course.title }}</span>
            </div>
        </template>

        <div class="max-w-2xl mx-auto bg-gray-800 p-8 rounded-lg shadow border border-gray-700">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-400">Thumbnail Image (Leave empty to keep current)</label>
                    <div v-if="course.thumbnail_path" class="mb-2">
                        <img :src="`/storage/${course.thumbnail_path}`" class="h-20 w-auto rounded border border-gray-700" />
                    </div>
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

                <!-- PRO Toggle -->
                <div class="flex items-center space-x-3 bg-gray-900 p-4 rounded border border-gray-700">
                    <input 
                        id="is_pro" 
                        type="checkbox" 
                        v-model="form.is_pro"
                        class="rounded border-gray-600 bg-gray-700 text-brand-primary focus:ring-brand-primary h-5 w-5"
                    >
                    <div>
                        <label for="is_pro" class="text-white font-bold block">PRO Course</label>
                        <p class="text-xs text-gray-400">If checked, only users with an active subscription can access this course.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-700">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white font-bold rounded shadow hover:opacity-90 transition disabled:opacity-50"
                    >
                        Update Course
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
