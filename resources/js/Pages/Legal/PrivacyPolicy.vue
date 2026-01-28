<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <Head :title="__('Privacy Policy')" />

    <div class="min-h-screen bg-gray-100 dark:bg-brand-dark text-gray-900 dark:text-gray-100">
        <nav class="bg-white dark:bg-brand-surface border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link href="/">
                                <ApplicationLogo class="block h-9 w-auto" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-brand-surface overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-10">
                    <h1 class="text-3xl font-bold mb-6 text-brand-primary">{{ __('Privacy Policy') }}</h1>
                    <p class="text-sm text-gray-500 mb-8">{{ __('Last updated') }}: {{ new Date().getFullYear() }}</p>

                    <div class="prose dark:prose-invert max-w-none space-y-6">
                        <p class="text-lg">
                            {{ __('legal_privacy_intro') }}
                        </p>
                        
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-lg mb-2">{{ __('Collection of Data') }}</h3>
                            <p>{{ __('legal_privacy_collection') }}</p>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-lg mb-2">{{ __('Usage of Data') }}</h3>
                            <p>{{ __('legal_privacy_usage') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
