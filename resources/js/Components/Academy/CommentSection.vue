<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

const props = defineProps({
    comments: {
        type: Array,
        default: () => []
    },
    chapterId: {
        type: Number,
        required: true
    }
});

const form = useForm({
    content: ''
});

const submitComment = () => {
    form.post(route('academy.chapters.comments.store', props.chapterId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        }
    });
};
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-xl font-bold text-white">{{ __('Comments') }} ({{ comments.length }})</h3>

        <!-- Comment Form -->
        <form @submit.prevent="submitComment" class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="mb-4">
                <label for="comment" class="sr-only">{{ __('Add a comment') }}</label>
                <textarea
                    id="comment"
                    v-model="form.content"
                    rows="3"
                    class="w-full bg-gray-900 text-white border-gray-700 rounded-lg focus:ring-brand-primary focus:border-brand-primary placeholder-gray-500"
                    :placeholder="__('Share your thoughts about this chapter...')"
                    required
                ></textarea>
                <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</p>
            </div>
            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-brand-primary hover:bg-violet-600 text-white font-bold py-2 px-4 rounded transition disabled:opacity-50"
                >
                    {{ form.processing ? __('Posting...') : __('Post Comment') }}
                </button>
            </div>
        </form>

        <!-- Comments List -->
        <div class="space-y-4">
            <div v-if="comments.length === 0" class="text-center text-gray-500 py-6">
                {{ __('No comments yet. Be the first to share your thoughts!') }}
            </div>

            <div v-for="comment in comments" :key="comment.id" class="bg-gray-800/50 p-4 rounded-lg border border-gray-700/50">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-brand-primary flex items-center justify-center text-white font-bold">
                            {{ comment.user.name.charAt(0) }}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-baseline">
                            <h4 class="text-sm font-bold text-gray-200">{{ comment.user.name }}</h4>
                            <span class="text-xs text-gray-500">{{ dayjs(comment.created_at).fromNow() }}</span>
                        </div>
                        <p class="mt-1 text-gray-300 text-sm whitespace-pre-wrap">{{ comment.content }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
