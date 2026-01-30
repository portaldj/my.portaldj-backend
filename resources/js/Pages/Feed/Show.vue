<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostItem from '@/Components/PostItem.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    post: Object
});
</script>

<template>
    <Head :title="`${post.user.name}'s Post`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 mb-2">
                 <Link :href="route('dashboard')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">&larr; {{ __('Back to Feed') }}</Link>
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <PostItem :post="post" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
