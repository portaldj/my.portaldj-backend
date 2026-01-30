<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    announcements: Object
});
</script>

<template>
    <Head :title="__('Announcements')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Announcements') }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        
                        <div v-if="announcements.data.length === 0" class="text-center py-10 text-gray-500">
                            {{ __('No announcements yet.') }}
                        </div>

                        <div v-else class="space-y-6">
                            <div v-for="announcement in announcements.data" :key="announcement.id" class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0 last:pb-0">
                                <h3 class="text-lg font-bold text-brand-primary dark:text-brand-accent">
                                    {{ new Date(announcement.created_at).toLocaleDateString() }}
                                </h3>
                                <p class="mt-2 text-gray-800 dark:text-gray-200 whitespace-pre-wrap">
                                    {{ announcement.message }}
                                </p>
                            </div>
                        </div>

                         <!-- Pagination -->
                         <div v-if="announcements.links && announcements.links.length > 3" class="mt-6 flex justify-center space-x-2">
                             <template v-for="(link, key) in announcements.links">
                                <div v-if="link.url === null" :key="key" class="px-4 py-2 text-gray-500 border rounded" v-html="link.label" />
                                <Link v-else :key="`link-${key}`" :href="link.url" class="px-4 py-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-700 dark:border-gray-600" :class="{ 'bg-brand-primary text-white border-brand-primary': link.active }" v-html="link.label" />
                             </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
