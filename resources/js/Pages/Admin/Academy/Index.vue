<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    courses: Object,
});

import { router } from '@inertiajs/vue3';

const toggleCourse = (course) => {
    router.post(route('admin.academy.toggle', course.id));
};

const togglePro = (course) => {
    router.post(route('admin.academy.togglePro', course.id));
};

const deleteCourse = (course) => {
    if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
        router.delete(route('admin.academy.destroy', course.id));
    }
};
</script>

<template>
    <Head title="Academy Manager" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <span>Academy Manager</span>
                <Link :href="route('admin.academy.create')" class="px-4 py-2 bg-brand-primary text-white rounded text-sm font-bold hover:bg-violet-600">
                    + Create Course
                </Link>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="course in courses.data" :key="course.id" class="bg-gray-800 rounded-lg shadow border border-gray-700 overflow-hidden flex flex-col">
                <div class="h-40 bg-gray-700 bg-cover bg-center relative" :style="course.thumbnail_path ? `background-image: url('/storage/${course.thumbnail_path}')` : ''">
                    <div v-if="!course.thumbnail_path" class="w-full h-full flex items-center justify-center text-gray-500">
                        No Thumbnail
                    </div>
                    <div class="absolute top-2 right-2">
                         <span v-if="course.is_active" class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded shadow">ACTIVE</span>
                         <span v-else class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded shadow">INACTIVE</span>
                    </div>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-xl font-bold text-white mb-2">{{ course.title }}</h3>
                    <p class="text-gray-400 text-sm mb-4 line-clamp-3">{{ course.description }}</p>
                    <div class="mt-auto flex justify-between items-center gap-2">
                        <Link :href="route('admin.academy.show', course.id)" class="px-3 py-1 bg-gray-700 text-white text-sm rounded hover:bg-gray-600 transition">
                            Chapters
                        </Link>
                        <Link :href="route('admin.academy.edit', course.id)" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-500 transition">
                            Edit
                        </Link>
                        
                         <div class="flex gap-2">
                             <button @click="togglePro(course)" class="text-sm px-2 py-1 rounded transition border border-gray-600" :class="course.is_pro ? 'text-yellow-400 bg-gray-700' : 'text-gray-400 hover:text-white'">
                                {{ course.is_pro ? 'PRO Only' : 'Free' }}
                             </button>
                             <button @click="toggleCourse(course)" class="text-sm px-2 py-1 rounded transition" :class="course.is_active ? 'text-yellow-400 hover:text-yellow-300' : 'text-green-400 hover:text-green-300'">
                                {{ course.is_active ? 'Deactivate' : 'Activate' }}
                             </button>
                             <button @click="deleteCourse(course)" class="text-sm text-red-400 hover:text-red-300 px-2 py-1">
                                Delete
                             </button>
                        </div>
                    </div>
                </div>
            </div>
            
             <div v-if="courses.data.length === 0" class="col-span-full text-center py-10 text-gray-500 bg-gray-800 rounded-lg border border-gray-700">
                No courses found. Create one to get started.
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-between" v-if="courses.links.length > 3">
             <Link 
                v-for="(link, k) in courses.links" 
                :key="k" 
                :href="link.url || '#'" 
                v-html="link.label"
                class="px-3 py-1 border rounded text-sm"
                 :class="link.active ? 'bg-brand-primary border-brand-primary text-white' : 'border-gray-700 text-gray-400'" 
            />
        </div>
    </AdminLayout>
</template>
