<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ActivityList from './Partials/ActivityList.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    activities: Object,
});
</script>

<template>
    <Head :title="__('Your Activity')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                {{ __('Your Activity') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <ActivityList :activities="activities.data" :pagination="activities" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
