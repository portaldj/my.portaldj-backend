<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    course: Object,
});

// Auto-select first incomplete chapter or fallback to first
const firstIncomplete = props.course.chapters.find(c => !c.is_completed);
const currentChapter = ref(firstIncomplete || props.course.chapters[0] || null);
const iframeLoaded = ref(false);

const selectChapter = (chapter) => {
    currentChapter.value = chapter;
    iframeLoaded.value = false;
};

const markComplete = () => {
    if (!currentChapter.value) return;
    router.post(route('academy.chapters.complete', currentChapter.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Logic to move to next? 
            // Props will update, is_completed will update.
            // Maybe find next chapter and select it?
            
            // For now just keep current valid.
        }
    });
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
                        <div v-if="currentChapter?.video_url" class="bg-black rounded-lg overflow-hidden shadow-lg border border-gray-800 aspect-video flex items-center justify-center relative group">
                            <!-- Placeholder Video Player -->
                            <div class="w-full h-full bg-gray-900 flex items-center justify-center">
                                <span v-if="!iframeLoaded" class="absolute text-gray-500">Video Player Loading...</span>
                                <iframe 
                                    v-if="currentChapter.video_url.includes('youtube')"
                                    :src="currentChapter.video_url.replace('watch?v=', 'embed/')" 
                                    class="w-full h-full relative z-10" 
                                    frameborder="0" 
                                    allowfullscreen
                                    @load="iframeLoaded = true"
                                ></iframe>
                                <div v-else class="text-center p-10 relative z-10">
                                    <p class="text-brand-accent mb-2">Video Source: {{ currentChapter.video_url }}</p>
                                    <p class="text-xs text-gray-500">(Real player integration requires generic HTML5 video or specific provider SDK)</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-brand-surface p-6 rounded-lg shadow border border-gray-700">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-2xl font-bold text-white">{{ currentChapter?.title || course.title }}</h3>
                                <button 
                                    v-if="currentChapter && !currentChapter.is_completed" 
                                    @click="markComplete" 
                                    class="text-xs bg-brand-primary hover:bg-violet-600 text-white px-3 py-1 rounded transition"
                                >
                                    Mark as Complete
                                </button>
                                <span v-else-if="currentChapter?.is_completed" class="text-xs bg-green-600 text-white px-3 py-1 rounded flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Completed
                                </span>
                            </div>
                            <p class="text-gray-300">{{ currentChapter?.content || course.description }}</p>
                        </div>
                    </div>

                    <!-- Sidebar (Chapter List) -->
                    <div class="space-y-6">
                        <div class="bg-brand-surface rounded-lg shadow border border-gray-700 overflow-hidden">
                            <div class="p-4 bg-gray-800 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-gray-200">Course Content</h3>
                                <span class="text-xs text-gray-400">
                                    {{ course.chapters.filter(c => c.is_completed).length }}/{{ course.chapters.length }} Completed
                                </span>
                            </div>
                            <ul class="divide-y divide-gray-700 max-h-[600px] overflow-y-auto">
                                <li 
                                    v-for="chapter in course.chapters" 
                                    :key="chapter.id" 
                                    @click="selectChapter(chapter)"
                                    class="p-4 cursor-pointer transition hover:bg-gray-800 flex items-center justify-between"
                                    :class="{'bg-brand-gray/50 border-l-4 border-brand-accent': currentChapter?.id === chapter.id}"
                                >
                                    <div class="flex items-center space-x-3">
                                        <!-- Status Icon -->
                                        <div v-if="chapter.is_completed" class="text-green-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div v-else class="text-gray-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        
                                        <div>
                                            <div class="text-sm font-medium text-gray-200" :class="{'line-through text-gray-500': chapter.is_completed && currentChapter?.id !== chapter.id}">
                                                {{ chapter.title }}
                                            </div>
                                            <!-- <div class="text-xs text-gray-500">Video</div> -->
                                        </div>
                                    </div>
                                    
                                    <div v-if="currentChapter?.id === chapter.id" class="text-brand-accent">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
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
