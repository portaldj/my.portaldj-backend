<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    auth: Object,
});
</script>

<template>
    <Head :title="__('Terms of Use')" />

    <div class="min-h-screen bg-gray-100 dark:bg-brand-dark text-gray-900 dark:text-gray-100">
        <!-- Navigation -->
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

        <!-- Content -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-brand-surface overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-10">
                    <h1 class="text-3xl font-bold mb-6 text-brand-primary">{{ __('Terms of Use') }}</h1>
                    <p class="text-sm text-gray-500 mb-8">{{ __('Last updated') }}: {{ new Date().getFullYear() }}</p>

                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        <p class="font-bold text-lg mb-4">{{ __('terms.effective_date') }}</p>

                        <h2 class="text-2xl font-bold mt-8 mb-4 text-brand-primary">{{ __('terms.intro_title') }}</h2>
                        <p>{{ __('terms.intro_text') }}</p>

                        <h2 class="text-2xl font-bold mt-8 mb-4 text-brand-primary">{{ __('terms.section_1_title') }}</h2>
                        <p>{{ __('terms.section_1_text') }}</p>
                        <p class="mt-2 font-semibold">{{ __('terms.section_1_warning') }}</p>

                        <h3 class="text-xl font-bold mt-6 mb-3">{{ __('terms.section_1_1_title') }}</h3>
                        <p>{{ __('terms.section_1_1_text') }}</p>
                        <ul class="list-disc pl-5 mt-2 space-y-2">
                            <li><strong>{{ __('terms.section_1_1_profile') }}:</strong> {{ __('terms.section_1_1_profile_desc') }}</li>
                            <li><strong>{{ __('terms.section_1_1_pool') }}:</strong> {{ __('terms.section_1_1_pool_desc') }}</li>
                            <li><strong>{{ __('terms.section_1_1_restriction') }}:</strong> {{ __('terms.section_1_1_restriction_desc') }}</li>
                            <li><strong>{{ __('terms.section_1_1_verification') }}:</strong> {{ __('terms.section_1_1_verification_desc') }}</li>
                            <li><strong>{{ __('terms.section_1_1_ai') }}:</strong> {{ __('terms.section_1_1_ai_desc') }}</li>
                        </ul>

                        <h3 class="text-xl font-bold mt-6 mb-3">{{ __('terms.section_1_2_title') }}</h3>
                        <ul class="list-disc pl-5 mt-2 space-y-2">
                            <li><strong>{{ __('terms.section_1_2_members') }}:</strong> {{ __('terms.section_1_2_members_desc') }}</li>
                            <li><strong>{{ __('terms.section_1_2_visitors') }}:</strong> {{ __('terms.section_1_2_visitors_desc') }}</li>
                        </ul>

                        <h2 class="text-2xl font-bold mt-8 mb-4 text-brand-primary">{{ __('terms.section_2_title') }}</h2>
                        
                        <h3 class="text-xl font-bold mt-6 mb-3">{{ __('terms.section_2_1_title') }}</h3>
                        <p>{{ __('terms.section_2_1_text') }}</p>
                        <p class="mt-2 text-red-600 font-bold dark:text-red-400">{{ __('terms.section_2_1_warning') }}</p>
                        <p class="mt-2 text-sm text-gray-500">{{ __('terms.section_2_1_detection') }}</p>

                        <h3 class="text-xl font-bold mt-6 mb-3">{{ __('terms.section_2_2_title') }}</h3>
                        <p>{{ __('terms.section_2_2_intro') }}</p>
                        <ul class="list-disc pl-5 mt-2 space-y-2">
                            <li><strong>{{ __('terms.section_2_2_payments') }}:</strong> {{ __('terms.section_2_2_payments_desc') }}</li>
                            <li><strong>{{ __('terms.section_2_2_renewal') }}:</strong> {{ __('terms.section_2_2_renewal_desc') }}</li>
                            <li><strong>{{ __('terms.section_2_2_refunds') }}:</strong> {{ __('terms.section_2_2_refunds_desc') }}</li>
                        </ul>

                        <h2 class="text-2xl font-bold mt-8 mb-4 text-brand-primary">{{ __('terms.section_3_title') }}</h2>

                        <h3 class="text-xl font-bold mt-6 mb-3">{{ __('terms.section_3_1_title') }}</h3>
                        <p>{{ __('terms.section_3_1_text') }}</p>
                        <p class="mt-2"><strong>{{ __('terms.section_3_1_veracity') }}:</strong> {{ __('terms.section_3_1_veracity_desc') }}</p>

                        <h3 class="text-xl font-bold mt-6 mb-3">{{ __('terms.section_3_2_title') }}</h3>
                        <p>{{ __('terms.section_3_2_text') }}</p>

                        <h4 class="text-lg font-bold mt-4 mb-2">{{ __('terms.section_3_2_1_title') }}</h4>
                        <p>{{ __('terms.section_3_2_1_text') }}</p>
                        <ul class="list-disc pl-5 mt-2 space-y-2">
                            <li><strong>{{ __('terms.section_3_2_1_commercial') }}:</strong> {{ __('terms.section_3_2_1_commercial_desc') }}</li>
                            <li><strong>{{ __('terms.section_3_2_1_property') }}:</strong> {{ __('terms.section_3_2_1_property_desc') }}</li>
                        </ul>

                        <h4 class="text-lg font-bold mt-4 mb-2">{{ __('terms.section_3_2_2_title') }}</h4>
                        <p>{{ __('terms.section_3_2_2_text') }}</p>
                        <p class="mt-2"><strong>{{ __('terms.section_3_2_2_liability') }}:</strong> {{ __('terms.section_3_2_2_liability_desc') }}</p>
                        <p class="mt-2 text-red-600 font-bold dark:text-red-400"><strong>{{ __('terms.section_3_2_2_resale') }}:</strong> {{ __('terms.section_3_2_2_resale_desc') }}</p>

                        <h3 class="text-xl font-bold mt-6 mb-3">{{ __('terms.section_3_3_title') }}</h3>
                        <p>{{ __('terms.section_3_3_text') }}</p>
                        <ul class="list-disc pl-5 mt-2 space-y-2">
                            <li><strong>{{ __('terms.section_3_3_nature') }}:</strong> {{ __('terms.section_3_3_nature_desc') }}</li>
                            <li><strong>{{ __('terms.section_3_3_liability') }}:</strong> {{ __('terms.section_3_3_liability_desc') }}</li>
                        </ul>

                        <h2 class="text-2xl font-bold mt-8 mb-4 text-brand-primary">{{ __('terms.section_4_title') }}</h2>
                        <p class="uppercase font-bold">{{ __('terms.section_4_disclaimer') }}</p>
                        <p class="mt-2">{{ __('terms.section_4_text') }}</p>

                        <h3 class="text-xl font-bold mt-6 mb-3">{{ __('terms.section_4_1_title') }}</h3>
                        <p>{{ __('terms.section_4_1_intro') }}</p>
                        <ul class="list-disc pl-5 mt-2 space-y-2">
                            <li>{{ __('terms.section_4_1_p1') }}</li>
                            <li>{{ __('terms.section_4_1_p2') }}</li>
                        </ul>

                        <h3 class="text-xl font-bold mt-6 mb-3">{{ __('terms.section_4_2_title') }}</h3>
                        <p>{{ __('terms.section_4_2_intro') }}</p>
                        <ul class="list-disc pl-5 mt-2 space-y-2">
                            <li>{{ __('terms.section_4_2_p1') }}</li>
                            <li>{{ __('terms.section_4_2_p2') }}</li>
                        </ul>

                        <h2 class="text-2xl font-bold mt-8 mb-4 text-brand-primary">{{ __('terms.section_5_title') }}</h2>
                        
                        <h3 class="text-xl font-bold mt-6 mb-3 text-green-600 dark:text-green-400">{{ __('terms.section_5_1_title') }}</h3>
                        <ul class="list-disc pl-5 mt-2 space-y-2">
                            <li>{{ __('terms.section_5_1_p1') }}</li>
                            <li>{{ __('terms.section_5_1_p2') }}</li>
                            <li>{{ __('terms.section_5_1_p3') }}</li>
                        </ul>

                        <h3 class="text-xl font-bold mt-6 mb-3 text-red-600 dark:text-red-400">{{ __('terms.section_5_2_title') }}</h3>
                        <ul class="list-disc pl-5 mt-2 space-y-2">
                            <li><strong>{{ __('terms.section_5_2_piracy') }}:</strong> {{ __('terms.section_5_2_piracy_desc') }}</li>
                            <li><strong>{{ __('terms.section_5_2_scraping') }}:</strong> {{ __('terms.section_5_2_scraping_desc') }}</li>
                            <li><strong>{{ __('terms.section_5_2_reverse') }}:</strong> {{ __('terms.section_5_2_reverse_desc') }}</li>
                            <li><strong>{{ __('terms.section_5_2_offensive') }}:</strong> {{ __('terms.section_5_2_offensive_desc') }}</li>
                        </ul>

                        <h2 class="text-2xl font-bold mt-8 mb-4 text-brand-primary">{{ __('terms.section_6_title') }}</h2>
                        <p>{{ __('terms.section_6_text') }}</p>
                        <p class="mt-2">{{ __('terms.section_6_prohibition') }}</p>

                        <h2 class="text-2xl font-bold mt-8 mb-4 text-brand-primary">{{ __('terms.section_7_title') }}</h2>
                        <p>{{ __('terms.section_7_text') }} <a href="mailto:pro@portaldj.cl" class="text-brand-primary hover:underline">pro@portaldj.cl</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
