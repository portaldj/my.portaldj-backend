<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Or AdminLayout if exists
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    message: '',
    user_id: '', // Optional
});

const submit = () => {
    form.post(route('admin.notifications.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head :title="__('Send Notification')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Send Global Notification') }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
