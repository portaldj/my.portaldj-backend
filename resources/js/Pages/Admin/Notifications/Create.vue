<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Or AdminLayout if exists
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    announcements: Object
});

const form = useForm({
    message: '',
    user_id: '', // Optional
});

const submit = () => {
    form.post(route('admin.notifications.store'), {
        onSuccess: () => form.reset(),
    });
};

const resend = (announcement) => {
    if (confirm('Are you sure you want to resend this notification?')) {
        useForm({}).post(route('admin.notifications.resend', announcement.id));
    }
};
</script>

<template>
    <Head :title="__('Send Notification')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Send Global Notification') }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Create Form -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="message" :value="__('Message')" />
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    rows="4"
                                    required
                                ></textarea>
                                <InputError :message="form.errors.message" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="user_id" :value="__('User ID (Optional - Leave empty for all users)')" />
                                <TextInput
                                    id="user_id"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.user_id"
                                />
                                <InputError :message="form.errors.user_id" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">{{ __('Send Notification') }}</PrimaryButton>

                                <transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Sent.') }}</p>
                                </transition>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- History Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Announcements History') }}</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date') }}</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Message') }}</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Target') }}</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="announcement in announcements.data" :key="announcement.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ new Date(announcement.created_at).toLocaleString() }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap max-w-xs truncate" :title="announcement.message">
                                            {{ announcement.message }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span v-if="announcement.target_user" class="text-blue-600 dark:text-blue-400 font-semibold">{{ announcement.target_user.name }}</span>
                                            <span v-else class="text-green-600 dark:text-green-400 font-bold">{{ __('All Users') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="resend(announcement)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                {{ __('Resend') }}
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="announcements.data.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('No announcements yet.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination (Simple) -->
                         <div v-if="announcements.links && announcements.links.length > 3" class="mt-4 flex justify-between">
                            <Link 
                                v-if="announcements.prev_page_url" 
                                :href="announcements.prev_page_url"
                                class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 text-sm"
                            >
                                &laquo; {{ __('Previous') }}
                            </Link>
                            <span v-else></span>

                             <Link 
                                v-if="announcements.next_page_url" 
                                :href="announcements.next_page_url"
                                class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 text-sm"
                            >
                                {{ __('Next') }} &raquo;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
