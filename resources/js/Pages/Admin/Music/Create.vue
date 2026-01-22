<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    genres: Array,
});

const form = useForm({
    track_name: '',
    artist_name: '',
    bpm: '',
    key: '',
    remix_type: '',
    genre_id: '',
    visible_from: '',
    visible_until: '',
    download_limit: 2,
    is_pro_only: false,
    file: null,
    preview_file: null,
    download_url: '',
});

const submit = () => {
    form.post(route('admin.music.store'));
};
</script>

<template>
    <Head title="Upload Song" />

    <AdminLayout>
        <template #header>
            Upload New Song
        </template>

        <div class="max-w-2xl mx-auto bg-gray-800 p-8 rounded-lg shadow border border-gray-700">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-400">Main Audio File (Full Track - MP3/WAV)</label>
                    <input 
                        type="file" 
                        @input="form.file = $event.target.files[0]"
                        class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-primary file:text-white hover:file:bg-violet-600"
                    />
                    <div v-if="form.errors.file" class="text-red-500 text-xs mt-1">{{ form.errors.file }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400">Preview Audio (Optional - Lower Quality/Clip)</label>
                    <input 
                        type="file" 
                        @input="form.preview_file = $event.target.files[0]"
                        class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-600 file:text-white hover:file:bg-gray-500"
                    />
                    <div v-if="form.errors.preview_file" class="text-red-500 text-xs mt-1">{{ form.errors.preview_file }}</div>
                    <div class="text-xs text-gray-500 mt-1">If provided, this file plays in the browser. If not, the main file plays.</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400">Alternative Download URL (Optional)</label>
                    <input v-model="form.download_url" type="url" placeholder="https://drive.google.com/..." class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                    <div class="text-xs text-gray-500 mt-1">Direct link to default MP3/WAV if file upload is skipped/too large.</div>
                    <div v-if="form.errors.download_url" class="text-red-500 text-xs mt-1">{{ form.errors.download_url }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Track Name</label>
                        <input v-model="form.track_name" type="text" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                        <div v-if="form.errors.track_name" class="text-red-500 text-xs mt-1">{{ form.errors.track_name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400">Artist Name</label>
                        <input v-model="form.artist_name" type="text" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                        <div v-if="form.errors.artist_name" class="text-red-500 text-xs mt-1">{{ form.errors.artist_name }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">BPM</label>
                        <input v-model="form.bpm" type="number" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                        <div v-if="form.errors.bpm" class="text-red-500 text-xs mt-1">{{ form.errors.bpm }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400">Key</label>
                        <input v-model="form.key" type="text" placeholder="e.g. 5A" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400">Genre</label>
                        <select v-model="form.genre_id" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50">
                            <option value="" disabled>Select Genre</option>
                            <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.name }}</option>
                        </select>
                        <div v-if="form.errors.genre_id" class="text-red-500 text-xs mt-1">{{ form.errors.genre_id }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-700 pt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Visibility Start (Optional)</label>
                        <input v-model="form.visible_from" type="datetime-local" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                        <div class="text-xs text-gray-500 mt-1">Leave empty to show immediately.</div>
                        <div v-if="form.errors.visible_from" class="text-red-500 text-xs mt-1">{{ form.errors.visible_from }}</div>
                    </div>

                    <div>
                         <label class="block text-sm font-medium text-gray-400">Visibility End (Optional)</label>
                        <input v-model="form.visible_until" type="datetime-local" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                         <div class="text-xs text-gray-500 mt-1">Leave empty to show indefinitely.</div>
                         <div v-if="form.errors.visible_until" class="text-red-500 text-xs mt-1">{{ form.errors.visible_until }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400">Download Limit (Per User)</label>
                    <input v-model="form.download_limit" type="number" min="1" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md text-white shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                    <div class="text-xs text-gray-500 mt-1">How many times a user can download this track. Default is 2.</div>
                    <div v-if="form.errors.download_limit" class="text-red-500 text-xs mt-1">{{ form.errors.download_limit }}</div>
                </div>

                <div class="flex items-center pt-4">
                    <input v-model="form.is_pro_only" type="checkbox" id="is_pro" class="rounded bg-gray-900 border-gray-700 text-brand-primary shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50" />
                    <label for="is_pro" class="ml-2 block text-sm text-gray-400">RESTRICT TO PRO USERS</label>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-700">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white font-bold rounded shadow hover:opacity-90 transition disabled:opacity-50"
                    >
                        Upload Song
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
