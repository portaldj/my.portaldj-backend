<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>
            Dashboard
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
                <h3 class="text-gray-400 text-sm font-medium">Total Users</h3>
                <p class="text-3xl font-bold text-white mt-1">{{ stats?.users_count || 0 }}</p>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
                <h3 class="text-gray-400 text-sm font-medium">Songs in Pool</h3>
                <p class="text-3xl font-bold text-white mt-1">{{ stats?.songs_count || 0 }}</p>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
                <h3 class="text-gray-400 text-sm font-medium">Academy Courses</h3>
                <p class="text-3xl font-bold text-white mt-1">{{ stats?.courses_count || 0 }}</p>
            </div>
        </div>

        <div class="mt-8 bg-gray-800 rounded-lg shadow border border-gray-700 p-6">
            <h3 class="text-lg font-bold text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <button class="flex items-center justify-center space-x-2 py-3 px-4 bg-gray-700 hover:bg-gray-600 rounded-lg text-white transition">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add New Song</span>
                </button>
                 <button class="flex items-center justify-center space-x-2 py-3 px-4 bg-gray-700 hover:bg-gray-600 rounded-lg text-white transition">
                    <span>Create Course</span>
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
