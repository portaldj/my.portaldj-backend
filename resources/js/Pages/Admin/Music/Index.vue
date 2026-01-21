<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    songs: Object,
});

const form = useForm({});

const deleteSong = (id) => {
    if (confirm('Are you sure you want to delete this song?')) {
        form.delete(route('admin.music.destroy', id));
    }
};
</script>

<template>
    <Head title="Music Manager" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <span>Music Manager</span>
                <Link :href="route('admin.music.create')" class="px-4 py-2 bg-brand-primary text-white rounded text-sm font-bold hover:bg-violet-600">
                    + Upload Song
                </Link>
            </div>
        </template>

        <div class="bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Track</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Artist</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Genre</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-300">
                    <tr v-for="song in songs.data" :key="song.id">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-white">{{ song.track_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ song.artist_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ song.bpm }} BPM • {{ song.key || '-' }}
                            <span v-if="song.is_pro_only" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-brand-secondary text-white">PRO</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ song.genre?.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <Link :href="route('admin.music.edit', song.id)" class="text-brand-primary hover:text-indigo-400 mr-2">Edit</Link>
                            <button @click="deleteSong(song.id)" class="text-red-400 hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="songs.data.length === 0">
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            No songs found. Upload one to get started.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination (Simple Implementation) -->
        <div class="mt-4 flex justify-between" v-if="songs.links.length > 3">
            <Link 
                v-for="(link, k) in songs.links" 
                :key="k" 
                :href="link.url || '#'" 
                v-html="link.label"
                class="px-3 py-1 border rounded text-sm"
                :class="link.active ? 'bg-brand-primary border-brand-primary text-white' : 'border-gray-700 text-gray-400'" 
            />
        </div>
    </AdminLayout>
</template>
