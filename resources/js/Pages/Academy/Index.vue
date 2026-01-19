<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    courses: Array,
});
</script>

<template>
    <Head title="Academy" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Academy
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="course in courses" :key="course.id" class="bg-brand-surface overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 transition hover:border-brand-accent">
                        <div class="h-40 bg-gray-800 flex items-center justify-center">
                            <!-- Placeholder for thumbnail -->
                            <span class="text-gray-500">Thumbnail</span>
                        </div>
                        <div class="p-6">
                            <h3 class="font-bold text-xl mb-2 text-white">{{ course.title }}</h3>
                            <p class="text-gray-400 text-sm mb-4">{{ course.description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-semibold px-2 py-1 bg-gray-800 rounded text-brand-secondary">
                                    {{ course.chapters_count }} Chapters
                                </span>
                                <button class="text-brand-accent hover:underline">Start Course &rarr;</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="courses.length === 0" class="text-center text-gray-500 py-10">
                    No courses available yet.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
