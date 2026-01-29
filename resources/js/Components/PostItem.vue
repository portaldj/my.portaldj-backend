<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    post: Object,
});

const emit = defineEmits(['post-deleted']);

const user = usePage().props.auth.user;
const isAdmin = computed(() => user.roles && user.roles.includes('Admin'));

const showCommentInput = ref(false);
const commentContent = ref('');

const canDelete = (item) => {
    return isAdmin.value || item.user.id === user.id;
};

const toggleLike = () => {
    // Optimistic Update
    const wasLiked = props.post.is_liked;
    props.post.is_liked = !wasLiked;
    props.post.likes_count += props.post.is_liked ? 1 : -1;

    axios.post(route('feed.like', props.post.id))
        .catch(() => {
            // Revert on failure
            props.post.is_liked = wasLiked;
            props.post.likes_count += props.post.is_liked ? 1 : -1;
        });
};

const toggleCommentInput = () => {
    showCommentInput.value = !showCommentInput.value;
};

const submitComment = () => {
    if (!commentContent.value) return;

    axios.post(route('feed.comment', props.post.id), { content: commentContent.value })
        .then(response => {
            // Add comment to list (Top)
            if (!props.post.comments) props.post.comments = [];
            props.post.comments.unshift(response.data.comment);
            props.post.comments_count++;
            
            // Clear form
            commentContent.value = '';
            showCommentInput.value = false;
        })
        .catch(error => {
            console.error(error);
            alert('Failed to post comment.');
        });
};

const deletePost = () => {
    if (!confirm('Are you sure you want to delete this post?')) return;
    
    axios.delete(route('feed.destroy', props.post.id))
        .then(() => {
            emit('post-deleted', props.post.id);
        })
        .catch(error => {
            console.error(error);
            alert('Failed to delete post.');
        });
};

const deleteComment = (comment) => {
    if (!confirm('Are you sure you want to delete this comment?')) return;
    
    axios.delete(route('feed.comments.destroy', comment.id))
        .then(() => {
             props.post.comments = props.post.comments.filter(c => c.id !== comment.id);
             props.post.comments_count--;
        })
        .catch(error => {
             console.error(error);
             alert('Failed to delete comment.');
        });
};

const loadMoreComments = () => {
    // Current count or page?
    const currentCount = props.post.comments.length;
    const page = Math.floor(currentCount / 5) + 1;
    
    axios.get(route('feed.comments.index', { post: props.post.id, page: page }))
        .then(response => {
             const newComments = response.data.data;
             // Ensure unique before adding
             const existingIds = new Set(props.post.comments.map(c => c.id));
             newComments.forEach(c => {
                 if (!existingIds.has(c.id)) {
                     props.post.comments.push(c);
                 }
             });
        })
        .catch(err => {
            console.error('Failed to load comments', err);
        });
};
</script>

<template>
    <div class="bg-white dark:bg-brand-surface p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700 relative group">
        <!-- Post Header -->
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-300 overflow-hidden">
                    <img v-if="post.user.profile?.thumb_url" :src="post.user.profile.thumb_url" class="w-full h-full object-cover" alt="Avatar">
                    <span v-else>{{ post.user.name.charAt(0) }}</span>
                </div>
                <div>
                    <Link :href="route('profile.show', post.user.profile?.username || 'unknown')" class="text-gray-900 dark:text-white font-bold hover:underline">
                        {{ post.user.name }}
                    </Link>
                    <Link v-if="post.user.profile?.username" :href="route('profile.show', post.user.profile.username)" class="ml-2 text-brand-primary dark:text-brand-accent text-sm hover:underline">
                        @{{ post.user.profile.username }}
                    </Link>
                    <div class="text-gray-500 text-xs">{{ new Date(post.created_at).toLocaleString() }}</div>
                </div>
            </div>
            
            <!-- Delete Post Button -->
            <button 
                v-if="canDelete(post)" 
                @click="deletePost"
                class="text-gray-400 dark:text-gray-500 hover:text-red-500 transition p-1"
                title="Delete Post"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
        
        <p class="text-gray-800 dark:text-gray-200 text-lg mb-4 whitespace-pre-wrap">{{ post.content }}</p>

        <!-- Attached Image -->
        <div v-if="post.image_path" class="mb-4 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <img :src="post.optimized_url || post.image_url" class="w-full h-auto object-cover max-h-96">
        </div>

        <!-- Tags display -->
        <div v-if="post.tags && post.tags.length > 0" class="mb-4 flex flex-wrap gap-2 text-sm">
            <template v-for="tag in post.tags" :key="tag.id + tag.type">
                <!-- Clickable DJ Tag -->
                <Link 
                    v-if="tag.type === 'user'" 
                    :href="route('profile.show', tag.username || tag.id)"
                    class="flex items-center text-brand-primary dark:text-brand-accent bg-gray-100 dark:bg-brand-surface-light px-2 py-1 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-brand-gray transition"
                >
                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="font-bold">{{ tag.name }}</span>
                    <span class="ml-1 text-xs opacity-70 border border-gray-300 dark:border-gray-600 rounded px-1">DJ</span>
                </Link>

                <!-- Other Tags (Club/City) - Non-clickable or different icon -->
                <div v-else class="flex items-center text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded border border-gray-200 dark:border-gray-700">
                    <svg v-if="tag.type === 'club'" class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <svg v-else-if="tag.type === 'city'" class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="font-bold">{{ tag.name }}</span>
                </div>
            </template>
        </div>

        <!-- Legacy Tag Fallback -->
        <div v-else-if="post.tag_name" class="mb-4 text-sm text-brand-primary dark:text-brand-accent flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="ml-1 font-bold">{{ post.tag_name }}</span>
        </div>

        <div class="flex items-center space-x-6 border-t border-gray-200 dark:border-gray-700 pt-4 text-gray-500 dark:text-gray-400 text-sm">
            <button 
                @click="toggleLike" 
                class="flex items-center space-x-1 transition"
                :class="post.is_liked ? 'text-brand-secondary dark:text-brand-accent' : 'hover:text-brand-secondary dark:hover:text-brand-accent'"
            >
                <svg class="w-5 h-5" :fill="post.is_liked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span>{{ post.likes_count }} Likes</span>
            </button>
            <button 
                @click="toggleCommentInput"
                class="flex items-center space-x-1 hover:text-brand-secondary dark:hover:text-brand-accent transition"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>{{ post.comments_count }} {{ __('Comments') }}</span>
            </button>
        </div>

        <!-- Comment Section -->
        <div v-if="showCommentInput || (post.comments && post.comments.length > 0)" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-800 space-y-4">
            <!-- Input -->
            <div v-if="showCommentInput" class="flex space-x-3">
                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-brand-primary flex items-center justify-center text-white text-xs font-bold">
                    {{ user.name.charAt(0) }}
                </div>
                <div class="flex-1">
                    <form @submit.prevent="submitComment" class="relative">
                        <input 
                            v-model="commentContent" 
                            type="text" 
                            class="w-full bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-full pl-4 pr-10 py-2 text-sm text-gray-900 dark:text-gray-200 focus:ring-brand-accent focus:border-brand-accent transition-colors"
                            :placeholder="__('Write a comment...')"
                            required
                        >
                        <button 
                            type="submit"
                            class="absolute right-1 top-1/2 -translate-y-1/2 p-1.5 text-brand-primary hover:text-brand-accent hover:bg-gray-200 dark:hover:bg-gray-800 rounded-full transition-colors flex items-center justify-center"
                            title="Send Comment"
                        >
                            <svg class="w-4 h-4 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div v-for="comment in post.comments" :key="comment.id" class="flex space-x-3 group/comment">
                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs text-gray-600 dark:text-gray-300 overflow-hidden">
                    <img v-if="comment.user.profile?.thumb_url" :src="comment.user.profile.thumb_url" class="w-full h-full object-cover" alt="Avatar">
                    <span v-else>{{ comment.user.name.charAt(0) }}</span>
                </div>
                <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-lg px-4 py-2 relative">
                    <div class="flex justify-between items-center">
                        <Link 
                            :href="route('profile.show', comment.user.profile?.username || comment.user.id)" 
                            class="text-sm font-bold text-gray-900 dark:text-white hover:text-brand-primary dark:hover:text-brand-accent hover:underline"
                        >
                            {{ comment.user.name }}
                        </Link>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                            <!-- Delete Comment Button -->
                            <button 
                                v-if="canDelete(comment)" 
                                @click="deleteComment(comment)"
                                class="text-gray-400 dark:text-gray-600 hover:text-red-500 transition opacity-0 group-hover/comment:opacity-100"
                                title="Delete Comment"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ comment.content }}</p>
                </div>
            </div>

            <!-- Load More Comments Button -->
            <div v-if="post.comments_count > post.comments.length" class="text-center pt-2">
                    <button @click="loadMoreComments" class="text-sm text-brand-primary dark:text-brand-accent hover:underline">
                        {{ __('Load more comments') }}
                    </button>
            </div>
        </div>
    </div>
</template>
