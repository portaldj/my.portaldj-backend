<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    equipments: Array,
});
</script>

<template>
    <Head title="AI Assistant" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">AI Assistant</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="equipments.length === 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400 mb-4">You don't have any equipment with AI support yet.</p>
                    <Link :href="route('profile.edit')" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                        Add equipment to your profile
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="item in equipments" :key="item.id" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300">
                                    {{ item.brand?.name }}
                                </span>
                                <span class="text-xs text-gray-500">{{ item.equipment_model?.type?.name }}</span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ item.equipment_model?.name }}
                            </h3>
                            
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-6">
                                {{ __("Chat with this device's AI to learn features, troubleshoot issues, or get creative tips.") }}
                            </p>
                            
                            <Link 
                                :href="route('assistant.chat', item.equipment_model.id)" 
                                class="inline-flex w-full justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                {{ __('Start Chat') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
