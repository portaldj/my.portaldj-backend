<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    notifications: Object,
});

const markAsRead = async (id) => {
    // Logic to mark as read if needed, or just let backend handle it on view
};
</script>

<template>
    <Head :title="__('Notifications')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Notifications') }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="notifications.data.length === 0" class="text-center py-8 text-gray-500">
                            {{ __('No notifications found.') }}
                        </div>

                        <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="notification in notifications.data" :key="notification.id" class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <!-- Icon based on type -->
                                        <div v-if="notification.data.type === 'like'" class="h-8 w-8 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center text-red-600 dark:text-red-300">
                                            ❤️
                                        </div>
                                        <div v-else-if="notification.data.type === 'comment'" class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300">
                                            💬
                                        </div>
                                        <div v-else class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300">
                                            🔔
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ notification.data.message }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ new Date(notification.created_at).toLocaleString() }}
                                        </p>
                                    </div>

                                    <div>
                                        <Link 
                                            v-if="notification.data.link && notification.data.link !== '#'"
                                            :href="notification.data.link" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-300 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            {{ __('View') }}
                                        </Link>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <!-- Pagination -->
                        <div class="mt-6" v-if="notifications.links.length > 3">
                            <div class="flex flex-wrap -mb-1">
                                <template v-for="(link, key) in notifications.links" :key="key">
                                    <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                                    <Link v-else class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-blue-700 text-white': link.active }" :href="link.url" v-html="link.label" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
