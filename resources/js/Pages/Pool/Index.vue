<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    songs: Object,
});

const currentSong = ref(null);
const isPlaying = ref(false);
const audioPlayer = ref(null);

const togglePlay = (song) => {
    if (currentSong.value?.id === song.id) {
        if (isPlaying.value) {
            audioPlayer.value.pause();
        } else {
            audioPlayer.value.play();
        }
    } else {
        currentSong.value = song;
        // Audio element will auto-play due to autoplay attribute when src changes
    }
};

const downloadSong = (song) => {
    // Optimistic UI update
    song.user_downloads_count++;
    
    window.location.href = route('pool.download', song.id);
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString();
};
</script>

<template>
    <Head title="Music Pool" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Music Pool
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-brand-surface shadow-sm sm:rounded-lg border border-gray-700">
                    <div class="p-6 text-gray-100">
                        <div v-if="songs.data.length === 0" class="text-center py-10 text-gray-500">
                            No songs found in the pool.
                        </div>
                        <ul v-else class="divide-y divide-gray-700">
                            <li v-for="song in songs.data" :key="song.id" class="py-4 flex justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    <button 
                                        @click="togglePlay(song)"
                                        class="h-10 w-10 rounded-full bg-brand-surface border border-brand-primary flex items-center justify-center text-brand-primary hover:bg-brand-primary hover:text-white transition"
                                    >
                                        <svg v-if="currentSong?.id === song.id && isPlaying" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 01-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 001 1h2a1 1 0 001-1V8a1 1 0 00-1-1h-2z" clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div>
                                        <h3 class="font-bold text-lg text-brand-accent">{{ song.track_name }}</h3>
                                        <p class="text-sm text-gray-400">{{ song.artist_name }} • {{ song.bpm }} BPM</p>
                                        <p v-if="song.visible_until" class="text-xs text-red-400 mt-1">
                                            Available until: {{ formatDate(song.visible_until) }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center">
                                    <template v-if="song.is_pro_only && !$page.props.auth.user.is_pro">
                                        <a 
                                            :href="route('subscription.index')" 
                                            class="px-4 py-2 bg-brand-secondary rounded hover:bg-pink-600 transition text-white text-sm font-bold flex items-center"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                            Subscribe
                                        </a>
                                    </template>
                                    <template v-else>
                                        <div class="flex items-center space-x-2">
                                            <div class="text-xs text-gray-500 mr-2 flex flex-col items-end">
                                               <span>Downloads: {{ song.user_downloads_count }} / {{ song.download_limit }}</span>
                                               <span v-if="song.user_downloads_count >= song.download_limit" class="text-red-500 font-bold">Limit Reached</span>
                                            </div>

                                            <a 
                                                v-if="song.download_url"
                                                :href="song.download_url" 
                                                target="_blank"
                                                class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-600 transition text-white text-sm font-bold border border-gray-600"
                                                title="Alternative Download"
                                            >
                                                Alt DL
                                            </a>
                                            <button 
                                                @click="downloadSong(song)"
                                                :disabled="song.user_downloads_count >= song.download_limit"
                                                class="px-4 py-2 bg-brand-primary rounded transition text-white text-sm font-bold disabled:opacity-50 disabled:cursor-not-allowed hover:bg-violet-500"
                                            >
                                                Download
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </li>
                        </ul>
                        
                        <!-- Fixed Player Bar -->
                        <div v-if="currentSong" class="fixed bottom-0 left-0 w-full bg-brand-surface border-t border-gray-700 p-4 flex items-center justify-between z-50 shadow-lg">
                            <div class="flex items-center space-x-4">
                                <div class="text-sm">
                                    <div class="font-bold text-white">{{ currentSong.track_name }}</div>
                                    <div class="text-gray-400">{{ currentSong.artist_name }}</div>
                                </div>
                            </div>
                            <!-- Prefer preview file, fallback to main file -->
                            <audio 
                                ref="audioPlayer" 
                                :src="currentSong.preview_file_path ? `/storage/${currentSong.preview_file_path}` : (currentSong.file_path ? `/storage/${currentSong.file_path}` : '#')" 
                                controls 
                                autoplay 
                                class="w-1/2"
                                @play="isPlaying = true"
                                @pause="isPlaying = false"
                                @ended="isPlaying = false"
                            ></audio>
                            <button @click="currentSong = null; isPlaying = false" class="text-gray-400 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
