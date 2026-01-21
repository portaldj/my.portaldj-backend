<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Payment Successful" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Payment Successful
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="rounded-full bg-green-100 p-4">
                            <svg class="h-16 w-16 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Welcome to PRO!</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                        Your subscription has been successfully activated. You now have full access to all premium features.
                        A receipt has been sent to your email address.
                    </p>

                    <div class="flex justify-center space-x-4">
                        <Link :href="route('dashboard')" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-6 py-2 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Back to Dashboard
                        </Link>
                        <Link :href="route('academy')" class="bg-brand-primary text-white px-6 py-2 rounded-md hover:bg-brand-secondary transition">
                            Go to Academy
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
