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
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                {{ __('Academy') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="course in courses" :key="course.id" class="bg-white dark:bg-brand-surface overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 transition hover:border-brand-primary dark:hover:border-brand-accent">
                        <div class="h-40 bg-gray-200 dark:bg-gray-800 flex items-center justify-center relative overflow-hidden group">
                           <img v-if="course.thumb_url" :src="course.thumb_url" class="w-full h-full object-cover transition duration-500 group-hover:scale-110" :alt="course.title">
                           <div v-else class="text-gray-400 dark:text-gray-500 flex flex-col items-center">
                                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span class="text-xs">No Thumbnail</span>
                           </div>
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="font-bold text-xl mb-2 text-gray-900 dark:text-white">{{ course.title }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 flex-1">{{ course.description }}</p>
                            
                            <!-- Progress Bar -->
                            <div v-if="course.progress > 0" class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-4">
                                <div class="bg-brand-primary dark:bg-brand-accent h-1.5 rounded-full" :style="{ width: course.progress + '%' }"></div>
                            </div>

                            <div class="flex justify-between items-center mt-auto">
                                <div class="flex items-center space-x-2">
                                     <span class="text-xs font-semibold px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-brand-secondary">
                                        {{ course.chapters_count }} {{ __('Chapters') }}
                                    </span>
                                    <span v-if="course.is_pro" class="text-xs font-bold px-2 py-1 bg-brand-primary text-white rounded uppercase">
                                        PRO
                                    </span>
                                    <span v-else class="text-xs font-bold px-2 py-1 bg-green-600 text-white rounded uppercase">
                                        {{ __('FREE') }}
                                    </span>
                                </div>
                                
                                <Link :href="route('academy.show', course.id)" class="text-brand-primary dark:text-brand-accent hover:underline font-bold flex items-center">
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
