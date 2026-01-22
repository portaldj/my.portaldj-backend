<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    courses: Array,
});
</script>

<template>
    <Head title="Academy" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                {{ __('Academy') }}
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
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="font-bold text-xl mb-2 text-white">{{ course.title }}</h3>
                            <p class="text-gray-400 text-sm mb-4 flex-1">{{ course.description }}</p>
                            
                            <!-- Progress Bar -->
                            <div v-if="course.progress > 0" class="w-full bg-gray-700 rounded-full h-1.5 mb-4">
                                <div class="bg-brand-accent h-1.5 rounded-full" :style="{ width: course.progress + '%' }"></div>
                            </div>

                            <div class="flex justify-between items-center mt-auto">
                                <div class="flex items-center space-x-2">
                                     <span class="text-xs font-semibold px-2 py-1 bg-gray-800 rounded text-brand-secondary">
                                        {{ course.chapters_count }} {{ __('Chapters') }}
                                    </span>
                                    <span v-if="course.is_pro" class="text-xs font-bold px-2 py-1 bg-brand-primary text-white rounded uppercase">
                                        PRO
                                    </span>
                                    <span v-else class="text-xs font-bold px-2 py-1 bg-green-600 text-white rounded uppercase">
                                        {{ __('FREE') }}
                                    </span>
                                </div>
                               
                                <Link :href="route('academy.show', course.id)" class="text-brand-accent hover:underline font-bold flex items-center">
                                    <span v-if="course.progress > 0">{{ __('Continue') }}</span>
                                    <span v-else>{{ __('Start Course') }}</span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="courses.length === 0" class="text-center text-gray-500 py-10">
                    {{ __('No courses available yet.') }}
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
