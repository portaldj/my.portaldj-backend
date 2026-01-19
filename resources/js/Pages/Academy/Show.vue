<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    course: Object,
});

const currentChapter = ref(props.course.chapters[0] || null);

const selectChapter = (chapter) => {
    currentChapter.value = chapter;
};
</script>

<template>
    <Head :title="course.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">
                    <Link :href="route('academy')" class="text-gray-400 hover:text-white transition">&larr; Academy</Link>
                    <span class="mx-2 text-gray-600">/</span>
                    {{ course.title }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Main Content (Video Player & Description) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-black rounded-lg overflow-hidden shadow-lg border border-gray-800 aspect-video flex items-center justify-center relative group">
                            <!-- Placeholder Video Player -->
                            <div v-if="currentChapter?.video_url" class="w-full h-full bg-gray-900 flex items-center justify-center">
                                <span class="text-gray-500">Video Player Loading...</span>
                                <iframe 
                                    v-if="currentChapter.video_url.includes('youtube')"
                                    :src="currentChapter.video_url.replace('watch?v=', 'embed/')" 
                                    class="w-full h-full" 
                                    frameborder="0" 
                                    allowfullscreen
                                ></iframe>
                                <div v-else class="text-center p-10">
                                    <p class="text-brand-accent mb-2">Video Source: {{ currentChapter.video_url }}</p>
                                    <p class="text-xs text-gray-500">(Real player integration requires generic HTML5 video or specific provider SDK)</p>
                                </div>
                            </div>
                            <div v-else class="text-gray-500">Select a chapter to start learning.</div>
                        </div>

                        <div class="bg-brand-surface p-6 rounded-lg shadow border border-gray-700">
                            <h3 class="text-2xl font-bold text-white mb-2">{{ currentChapter?.title || course.title }}</h3>
                            <p class="text-gray-300">{{ currentChapter?.content || course.description }}</p>
                        </div>
                    </div>

                    <!-- Sidebar (Chapter List) -->
                    <div class="space-y-6">
                        <div class="bg-brand-surface rounded-lg shadow border border-gray-700 overflow-hidden">
                            <div class="p-4 bg-gray-800 border-b border-gray-700">
                                <h3 class="font-bold text-gray-200">Course Content</h3>
                            </div>
                            <ul class="divide-y divide-gray-700">
                                <li 
                                    v-for="chapter in course.chapters" 
                                    :key="chapter.id" 
                                    @click="selectChapter(chapter)"
                                    class="p-4 cursor-pointer transition hover:bg-gray-800 flex items-center justify-between"
                                    :class="{'bg-brand-gray/50 border-l-4 border-brand-accent': currentChapter?.id === chapter.id}"
                                >
                                    <div>
                                        <div class="text-sm font-medium text-gray-200">{{ chapter.title }}</div>
                                        <div class="text-xs text-gray-500">Video • 10m</div>
                                    </div>
                                    <div v-if="currentChapter?.id === chapter.id" class="text-brand-accent">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div v-else-if="chapter.exam" class="text-gray-500">
                                        <!-- Icon for exam -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Action Card -->
                        <div v-if="currentChapter?.exam" class="bg-gradient-to-br from-brand-primary to-brand-secondary p-6 rounded-lg shadow text-white">
                            <h4 class="font-bold text-lg mb-2">Ready to test your knowledge?</h4>
                            <p class="text-sm opacity-90 mb-4">Take the exam for this chapter to earn points.</p>
                            <Link 
                                :href="route('academy.exam', currentChapter.exam.id)"
                                class="block w-full text-center bg-white text-brand-primary font-bold py-2 rounded shadow hover:bg-gray-100 transition"
                            >
                                Take Exam
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
