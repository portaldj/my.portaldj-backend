<script setup>
import { Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

defineProps({
    activities: {
        type: Array, // Expected: Paginator data
        default: () => []
    },
    pagination: {
        type: Object,
        default: () => ({})
    }
});

const getActivityLink = (activity) => {
    if (activity.type === 'post') {
        return route('feed.show', activity.id);
    } else if (activity.type === 'comment') {
        return route('feed.show', activity.commentable_id); // Go to post
    }
    return '#';
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Your Activity') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('A summary of your recent posts and comments.') }}
            </p>
        </header>

        <div class="mt-6 space-y-4">
            <div v-if="activities.length === 0" class="text-gray-500 italic">
                {{ __('No recent activity.') }}
            </div>

            <div v-else class="grid grid-cols-1 gap-4">
                <Link 
                    v-for="activity in activities" 
                    :key="`${activity.type}-${activity.id}`"
                    :href="getActivityLink(activity)"
                    class="block bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-brand-primary dark:hover:border-brand-primary transition group"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span 
                                    class="text-xs font-bold uppercase px-2 py-0.5 rounded"
                                    :class="activity.type === 'post' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200'"
                                >
                                    {{ activity.type === 'post' ? __('Post') : __('Comment') }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ dayjs(activity.created_at).fromNow() }}
                                </span>
                            </div>
                            
                            <p class="text-gray-800 dark:text-gray-200 text-sm line-clamp-2">
                                <span v-if="activity.type === 'comment' && activity.commentable" class="font-bold text-gray-500 mr-1">
                                    {{ __('On post:') }}
                                </span>
                                {{ activity.content }}
                            </p>
                            
                            <div v-if="activity.type === 'comment' && activity.commentable" class="mt-2 text-xs text-gray-500 bg-gray-100 dark:bg-gray-800 p-2 rounded">
                                <span class="block font-medium mb-1">{{ __('Original Post:') }}</span>
                                <p class="italic line-clamp-1">"{{ activity.commentable.content }}"</p>
                            </div>
                        </div>
                        
                        <div class="text-brand-primary opacity-0 group-hover:opacity-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </Link>
            </div>
            
            <!-- Simple Pagination -->
            <div v-if="pagination.last_page > 1" class="flex justify-center mt-4 space-x-2">
                <Link 
                    v-if="pagination.prev_page_url" 
                    :href="pagination.prev_page_url" 
                    only="activity"
                    preserve-scroll
                    class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm hover:bg-gray-300 dark:hover:bg-gray-600"
                >
                    &larr; {{ __('Previous') }}
                </Link>
                 <Link 
                    v-if="pagination.next_page_url" 
                    :href="pagination.next_page_url" 
                    only="activity"
                    preserve-scroll
                    class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm hover:bg-gray-300 dark:hover:bg-gray-600"
                >
                    {{ __('Next') }} &rarr;
                </Link>
            </div>
        </div>
    </section>
</template>
