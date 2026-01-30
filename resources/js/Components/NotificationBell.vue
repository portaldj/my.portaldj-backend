<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import axios from 'axios';

const notifications = ref([]);
const unreadCount = ref(0);
let pollingInterval = null;

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.unread'));
        notifications.value = response.data.unread;
        unreadCount.value = response.data.count;
    } catch (error) {
        console.error('Failed to fetch notifications', error);
    }
};

// ... existing code ...

onMounted(() => {
    fetchNotifications();
    
    // Poll every 60 seconds
    pollingInterval = setInterval(fetchNotifications, 60000);
});

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval);
});
const markAsRead = async (id) => {
    try {
        await axios.post(route('notifications.read', id));
        fetchNotifications(); // Refresh list
    } catch (error) {
        console.error('Failed to mark as read', error);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post(route('notifications.readAll'));
        fetchNotifications();
    } catch (error) {
        console.error('Failed to mark all as read', error);
    }
};

const handleNotificationClick = (notification) => {
    if (!notification.read_at) {
        axios.post(route('notifications.read', notification.id));
    }
    
    if (notification.data.link && notification.data.link !== '#') {
        router.visit(notification.data.link);
    }
};
</script>

<template>
    <div class="relative ms-3">
        <Dropdown align="right" width="80">
            <template #trigger>
                <button class="relative inline-flex items-center p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    
                    <span v-if="unreadCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">
                        {{ unreadCount > 99 ? '99+' : unreadCount }}
                    </span>
                </button>
            </template>

            <template #content>
                <div class="w-80 max-h-96 overflow-y-auto">
                    <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Notifications') }}</span>
                        <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                            {{ __('Mark all as read') }}
                        </button>
                    </div>

                    <div v-if="notifications.length === 0" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400 text-sm">
                        {{ __('No new notifications') }}
                    </div>

                    <div v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div 
                            v-for="notification in notifications" 
                            :key="notification.id" 
                            @click="handleNotificationClick(notification)"
                            class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out cursor-pointer"
                        >
                            <div class="flex items-start">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-800 dark:text-gray-200">
                                        {{ notification.data.message }}
                                    </p>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                                        {{ new Date(notification.created_at).toLocaleDateString() }}
                                    </span>
                                </div>
                                <button @click.stop="markAsRead(notification.id)" class="ml-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400" :title="__('Mark as read')">
                                    &times;
                                </button>
                            </div>
                        </div>
                    </div>

                    <Link :href="route('notifications.index')" class="block px-4 py-2 text-sm text-center text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 border-t border-gray-200 dark:border-gray-700">
                        {{ __('View all notifications') }}
                    </Link>
                </div>
            </template>
        </Dropdown>
    </div>
</template>
