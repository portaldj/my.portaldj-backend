<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
});

const form = useForm({
    title: '',
    video_url: '',
    content: '',
    order: props.course.chapters.length + 1,
});

const submit = () => {
    form.post(route('admin.academy.chapters.store', props.course), {
        onSuccess: () => {
             form.reset();
             form.order = props.course.chapters.length + 1;
        }
    });
};

const deleteChapter = (id) => {
    if (confirm('Delete this chapter?')) {
        useForm({}).delete(route('admin.chapters.destroy', id));
    }
}
</script>

<template>
    <Head :title="`Manage: ${course.title}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-2">
                <Link :href="route('admin.academy.index')" class="text-gray-400 hover:text-white">&larr; Courses</Link>
                <span class="text-gray-600">/</span>
                <span>{{ course.title }}</span>
                <Link :href="route('admin.academy.edit', course.id)" class="ml-4 text-xs bg-blue-600 text-white px-2 py-1 rounded">Edit Details</Link>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chapter List -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-xl font-bold text-white">Chapters</h3>
                <div v-if="course.chapters.length === 0" class="p-6 bg-gray-800 rounded border border-gray-700 text-gray-500">
                    No chapters yet. Add one properly.
                </div>
                <div v-for="chapter in course.chapters" :key="chapter.id" class="p-4 bg-gray-800 rounded border border-gray-700 flex justify-between items-center">
                    <div>
                        <div class="font-bold text-white">{{ chapter.order }}. {{ chapter.title }}</div>
                        <div class="text-sm text-gray-500 truncate w-96">{{ chapter.video_url || 'No Video' }}</div>
                    </div>
                    <button @click="deleteChapter(chapter.id)" class="text-red-400 text-sm hover:underline">Delete</button>
                </div>
            </div>

            <!-- Add Chapter Form -->
            <div class="bg-gray-800 p-6 rounded shadow border border-gray-700 h-fit">
                <h3 class="text-lg font-bold text-white mb-4">Add Chapter</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase">Title</label>
                        <input v-model="form.title" type="text" class="w-full bg-gray-900 border-gray-700 rounded text-white text-sm" />
                         <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                    </div>
                     <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase">Order</label>
                        <input v-model="form.order" type="number" class="w-full bg-gray-900 border-gray-700 rounded text-white text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase">Video URL</label>
                        <input v-model="form.video_url" type="url" placeholder="https://youtube.com/..." class="w-full bg-gray-900 border-gray-700 rounded text-white text-sm" />
                        <div v-if="form.errors.video_url" class="text-red-500 text-xs mt-1">{{ form.errors.video_url }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase">Content / Notes</label>
                        <textarea v-model="form.content" rows="3" class="w-full bg-gray-900 border-gray-700 rounded text-white text-sm"></textarea>
                        <div v-if="form.errors.content" class="text-red-500 text-xs mt-1">{{ form.errors.content }}</div>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full py-2 bg-brand-primary text-white rounded font-bold hover:bg-violet-600">
                        Add Chapter
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
