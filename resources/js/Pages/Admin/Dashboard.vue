<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

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
                <Link :href="route('admin.music.create')" class="flex items-center justify-center space-x-2 py-3 px-4 bg-gray-700 hover:bg-gray-600 rounded-lg text-white transition">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    <span>Upload Music</span>
                </Link>
                <Link :href="route('admin.academy.create')" class="flex items-center justify-center space-x-2 py-3 px-4 bg-gray-700 hover:bg-gray-600 rounded-lg text-white transition">
                    <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Create Course</span>
                </Link>
                 <Link :href="route('admin.users.create')" class="flex items-center justify-center space-x-2 py-3 px-4 bg-gray-700 hover:bg-gray-600 rounded-lg text-white transition">
                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Create User</span>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
