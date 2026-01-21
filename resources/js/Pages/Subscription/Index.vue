<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    plans: Object,
    isPro: Boolean,
    activeSubscription: Object,
});

const monthly = props.plans.monthly;
const yearly = props.plans.yearly;

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(amount);
};

const subscribe = (planId) => {
    router.post(route('subscription.subscribe'), { plan_id: planId });
};

const expiryDate = computed(() => {
    if (props.activeSubscription?.expires_at) {
        return new Date(props.activeSubscription.expires_at).toLocaleDateString();
    }
    return null;
});

const user = usePage().props.auth.user;
const canStartTrial = computed(() => {
    if (!user.last_trial_at) return true;
    const lastTrialDate = new Date(user.last_trial_at);
    const threeMonthsAgo = new Date();
    threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
    return lastTrialDate < threeMonthsAgo;
});

const activateTrial = () => {
    if (confirm('Start your 7-Day Free Trial?')) {
        router.post(route('subscription.activateTrial'));
    }
};
</script>

<template>
    <Head title="Go PRO" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Upgrade to PRO
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Active Subscription Status -->
                <div v-if="isPro && activeSubscription" class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">You are a PRO Member!</strong>
                    <span class="block sm:inline"> Your subscription is active until {{ expiryDate }}.</span>
                    <p class="mt-2 text-sm">To renew or change your plan, please wait until your current subscription expires.</p>
                </div>

                <div v-else class="text-center mb-10">
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                        Simple, transparent pricing
                    </h3>
                    <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">
                        Unlock full access to the Academy, Music Pool, and AI Assistant.
                    </p>
                </div>

                <div v-if="!isPro" class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <!-- Free Trial Card -->
                     <div class="col-span-1 md:col-span-2 bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl shadow-xl border border-gray-700 p-6 flex flex-col md:flex-row items-center justify-between">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-xl font-bold text-white">7-Day Free Trial</h3>
                            <p class="text-gray-300">Try all PRO features for free. No credit card required.</p>
                            <p v-if="!canStartTrial" class="text-xs text-red-400 mt-1">
                                (Available once every 3 months. Next available: {{ new Date(new Date(user.last_trial_at).setMonth(new Date(user.last_trial_at).getMonth() + 3)).toLocaleDateString() }})
                            </p>
                        </div>
                        <button 
                            @click="activateTrial" 
                            :disabled="!canStartTrial"
                            class="bg-white text-gray-900 font-bold py-2 px-6 rounded-full hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ canStartTrial ? 'Start Free Trial' : 'Trial Unavailable' }}
                        </button>
                    </div>

                    <!-- Monthly Plan -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col">
                        <div class="p-8 flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ monthly.name }}</h3>
                            <p class="mt-4 flex items-baseline text-gray-900 dark:text-white">
                                <span class="text-5xl font-extrabold tracking-tight">{{ formatCurrency(monthly.amount) }}</span>
                                <span class="ml-1 text-xl font-semibold text-gray-500">/month</span>
                            </p>
                            <p class="mt-6 text-gray-500 dark:text-gray-400">Perfect for getting started.</p>

                            <ul role="list" class="mt-6 space-y-4">
                                <li class="flex">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">Full Academy Access</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">Unlimited Music Pool</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">AI Assistant</span>
                                </li>
                            </ul>
                        </div>
                        <div class="p-8 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                            <button @click="subscribe('monthly')" class="w-full bg-brand-primary hover:bg-brand-secondary text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105">
                                Subscribe Monthly
                            </button>
                        </div>
                    </div>

                    <!-- Yearly Plan -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border-2 border-brand-accent flex flex-col relative">
                        <div class="absolute top-0 right-0 bg-brand-accent text-white text-xs font-bold px-3 py-1 rounded-bl-lg uppercase tracking-wide">
                            Best Value
                        </div>
                        <div class="p-8 flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ yearly.name }}</h3>
                            <p class="mt-4 flex items-baseline text-gray-900 dark:text-white">
                                <span class="text-5xl font-extrabold tracking-tight">{{ formatCurrency(yearly.amount) }}</span>
                                <span class="ml-1 text-xl font-semibold text-gray-500">/year</span>
                            </p>
                            <p class="mt-6 text-gray-500 dark:text-gray-400">
                                Save {{ yearly.savings_pct }}% compared to monthly.
                            </p>

                            <ul role="list" class="mt-6 space-y-4">
                                <li class="flex">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">Full Academy Access</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">Unlimited Music Pool</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">AI Assistant</span>
                                </li>
                            </ul>
                        </div>
                        <div class="p-8 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                            <button @click="subscribe('yearly')" class="w-full bg-gradient-to-r from-brand-accent to-purple-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105 shadow-lg">
                                Subscribe Yearly
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
