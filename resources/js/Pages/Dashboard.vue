<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import MultiSelect from '@/Components/MultiSelect.vue';

const props = defineProps({
    posts: Object,
    clubs: Array,
    cities: Array,
    djs: Array,
});

const user = usePage().props.auth.user;

const postForm = useForm({
    content: '',
    image: null,
    tags: [],
});

const submitPost = () => {
    postForm.post(route('feed.store'), {
        forceFormData: true,
        onSuccess: () => postForm.reset(),
    });
};

const allTagOptions = computed(() => {
    const options = [];
    if (props.clubs) options.push(...props.clubs.map(c => ({ id: c.id, name: c.name, type: 'club' })));
    if (props.cities) options.push(...props.cities.map(c => ({ id: c.id, name: c.name, type: 'city' })));
    if (props.djs) options.push(...props.djs.map(d => ({ id: d.id, name: d.name, type: 'user' })));
    return options.sort((a, b) => a.name.localeCompare(b.name));
});

const activeCommentInput = ref({});
const commentForms = ref({}); 

const toggleLike = (post) => {
    router.post(route('feed.like', post.id), {}, { preserveScroll: true });
};

const toggleCommentInput = (post) => {
    activeCommentInput.value[post.id] = !activeCommentInput.value[post.id];
};

const submitComment = (post) => {
    const content = commentForms.value[post.id];
    if (!content) return;

    router.post(route('feed.comment', post.id), { content }, {
        preserveScroll: true,
        onSuccess: () => {
            commentForms.value[post.id] = '';
            activeCommentInput.value[post.id] = false; 
        }
    });
};
</script>

<template>
    <Head title="Feed" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Global Feed
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Create Post Card -->
                <div class="bg-brand-surface p-6 rounded-lg shadow-lg border border-gray-700">
                    <div class="flex space-x-4">
                        <div class="flex-shrink-0">
                            <!-- Avatar Placeholder -->
                            <div class="h-10 w-10 rounded-full bg-brand-primary flex items-center justify-center text-white font-bold">
                                {{ user.name.charAt(0) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <form @submit.prevent="submitPost">
                                <textarea
                                    v-model="postForm.content"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg text-gray-200 focus:ring-brand-accent focus:border-brand-accent resize-none"
                                    rows="3"
                                    placeholder="What's spinning?"
                                    required
                                ></textarea>

                                <!-- Media & Tagging Tools -->
                                <div class="mt-3 space-y-3">
                                    <div class="flex items-center gap-2">
                                        <!-- Image Upload -->
                                        <label class="cursor-pointer text-gray-400 hover:text-brand-accent p-2 rounded hover:bg-gray-800 transition relative group">
                                            <input type="file" @input="postForm.image = $event.target.files[0]" class="hidden" accept="image/*">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span v-if="postForm.image" class="absolute -top-1 -right-1 h-2 w-2 bg-green-500 rounded-full"></span>
                                        </label>
                                        
                                        <div class="flex-1">
                                            <MultiSelect
                                                v-model="postForm.tags"
                                                :items="allTagOptions"
                                                placeholder="Tag Clubs, Cities, or DJs..."
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 flex justify-end">
                                    <button 
                                        type="submit" 
                                        :disabled="postForm.processing"
                                        class="px-4 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white font-semibold rounded-md shadow hover:opacity-90 transition disabled:opacity-50"
                                    >
                                        Post
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Posts List -->
                <div v-for="post in posts.data" :key="post.id" class="bg-brand-surface p-6 rounded-lg shadow-lg border border-gray-700">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-gray-300">
                            {{ post.user.name.charAt(0) }}
                        </div>
                        <div>
                            <Link :href="route('profile.show', post.user.profile?.username || 'unknown')" class="text-white font-bold hover:underline">
                                {{ post.user.name }}
                            </Link>
                            <div class="text-gray-500 text-xs">{{ new Date(post.created_at).toLocaleString() }}</div>
                        </div>
                    </div>
                    
                    <p class="text-gray-200 text-lg mb-4">
                        {{ post.content }}
                    </p>

                    <!-- Attached Image -->
                    <div v-if="post.image_path" class="mb-4 rounded-lg overflow-hidden border border-gray-700">
                        <img :src="'/storage/' + post.image_path" class="w-full h-auto object-cover max-h-96">
                    </div>

                    <!-- Tags display -->
                    <div v-if="post.tags && post.tags.length > 0" class="mb-4 flex flex-wrap gap-2 text-sm">
                        <div v-for="tag in post.tags" :key="tag.id + tag.type" class="flex items-center text-brand-accent bg-brand-surface-light px-2 py-1 rounded border border-gray-700">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="font-bold">{{ tag.name }}</span>
                            <span class="ml-1 text-xs opacity-70 border border-gray-600 rounded px-1">{{ tag.type }}</span>
                        </div>
                    </div>

                    <!-- Legacy Tag Fallback (for old posts if migration failed or needed) -->
                    <div v-else-if="post.tag_name" class="mb-4 text-sm text-brand-accent flex items-center">
                         <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="ml-1 font-bold">{{ post.tag_name }}</span>
                    </div>

                    <div class="flex items-center space-x-6 border-t border-gray-700 pt-4 text-gray-400 text-sm">
                        <button 
                            @click="toggleLike(post)" 
                            class="flex items-center space-x-1 transition"
                            :class="post.is_liked ? 'text-brand-accent' : 'hover:text-brand-accent'"
                        >
                            <svg class="w-5 h-5" :fill="post.is_liked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span>{{ post.likes_count }} Likes</span>
                        </button>
                        <button 
                            @click="toggleCommentInput(post)"
                            class="flex items-center space-x-1 hover:text-brand-accent transition"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>{{ post.comments_count }} Comments</span>
                        </button>
                    </div>

                    <!-- Comment Section -->
                    <div v-if="activeCommentInput[post.id] || (post.comments && post.comments.length > 0)" class="mt-4 pt-4 border-t border-gray-800 space-y-4">
                        <!-- Input -->
                        <div v-if="activeCommentInput[post.id]" class="flex space-x-3">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-brand-primary flex items-center justify-center text-white text-xs font-bold">
                                {{ user.name.charAt(0) }}
                            </div>
                            <div class="flex-1">
                                <form @submit.prevent="submitComment(post)">
                                    <input 
                                        v-model="commentForms[post.id]" 
                                        type="text" 
                                        class="w-full bg-gray-900 border border-gray-700 rounded-full px-4 py-2 text-sm text-gray-200 focus:ring-brand-accent focus:border-brand-accent"
                                        placeholder="Write a comment..."
                                        required
                                    >
                                </form>
                            </div>
                        </div>

                        <!-- List -->
                        <div v-for="comment in post.comments" :key="comment.id" class="flex space-x-3">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs text-gray-300">
                                {{ comment.user.name.charAt(0) }}
                            </div>
                            <div class="flex-1 bg-gray-800 rounded-lg px-4 py-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-bold text-white">{{ comment.user.name }}</span>
                                    <span class="text-xs text-gray-500">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                                </div>
                                <p class="text-sm text-gray-300 mt-1">{{ comment.content }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="posts.data.length === 0" class="text-center text-gray-500">
                    No posts yet. Be the first to share!
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
