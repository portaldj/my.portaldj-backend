<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import PremiumNavLink from '@/Components/PremiumNavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const roles = computed(() => user.value?.roles || []);

const isAdmin = computed(() => roles.value.includes('Admin'));

import Toast from '@/Components/Toast.vue';

const toasts = ref([]);
let toastId = 0;

const addToast = (message, type = 'info') => {
    const id = toastId++;
    toasts.value.push({ id, message, type });
    // Cleanup if too many? For now let them self-dismiss
};

const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index !== -1) {
        toasts.value.splice(index, 1);
    }
};

// Watch for flash messages from Inertia
// We need to watch the props deeply or just the flash object
watch(() => page.props.flash, (newFlash) => {
    if (newFlash.success) {
        addToast(newFlash.success, 'success');
    }
    if (newFlash.error) {
        addToast(newFlash.error, 'error');
    }
    if (newFlash.message) {
        addToast(newFlash.message, 'info');
    }
}, { deep: true, immediate: true });

onMounted(() => {
    // 1. Handle BF Cache (Back/Forward)
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            window.location.reload();
        }
    });

    // 2. Explicit Session Check
    // If we are looking at a cached page, this request will fail if the session is dead.
    // Only check if we have a user (guest users don't need session verification)
    if (page.props.auth.user) {
        axios.get('/verify-session')
            .catch(error => {
                if (error.response && (error.response.status === 401 || error.response.status === 419)) {
                    window.location.href = '/';
                }
            });
    }

    initTheme();
});

import { useTheme } from '@/Composables/useTheme';
const { currentTheme, setTheme, initTheme } = useTheme();
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-brand-dark text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">
        <!-- Top Navigation -->
        <nav class="border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-brand-gray/80 backdrop-blur-md sticky top-0 z-50 transition-colors duration-300">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('dashboard')">
                                <!-- Light Mode Logo -->
                                <img src="/images/logo.png" alt="Portal DJ" class="block dark:hidden h-10 w-auto object-contain" />
                                <!-- Dark Mode Logo -->
                                <img src="/images/logo-dark.png" alt="Portal DJ" class="hidden dark:block h-10 w-auto object-contain" />
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <PremiumNavLink :href="route('dashboard')" :active="route().current('dashboard') || route().current('feed.*')">
                                {{ __('Global Feed') }}
                            </PremiumNavLink>
                            <PremiumNavLink v-if="$page.props.modules.pool" :href="route('pool')" :active="route().current('pool')">
                                {{ __('Music Pool') }}
                            </PremiumNavLink>
                            <PremiumNavLink v-if="$page.props.modules.academy" :href="route('academy') || route('academy.*')" :active="route().current('academy') || route().current('academy.*')">
                                {{ __('Academy') }}
                            </PremiumNavLink>
                            <NavLink v-if="$page.props.modules.agenda" :href="route('calendar.index')" :active="route().current('calendar.*')">
                                {{ __('Agenda') }}
                            </NavLink>
                            <NavLink v-if="$page.props.modules.assistant" :href="route('assistant.index')" :active="route().current('assistant.*')">
                                {{ __('AI Assistant') }}
                            </NavLink>
                            <NavLink v-if="$page.props.auth.user && !$page.props.auth.user.is_pro" :href="route('subscription.index')" :active="route().current('subscription.*')" class="text-yellow-500 font-bold">
                                {{ __('Upgrade to PRO') }}
                            </NavLink>
                            <PremiumNavLink v-if="isAdmin" :href="route('admin.dashboard')" :active="route().current('admin.*')" class="text-red-400 hover:text-red-300 border-red-500">
                                {{ __('Admin Panel') }}
                            </PremiumNavLink>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center">
                        <!-- Settings Dropdown -->
                        <div class="relative ms-3" v-if="user">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        class="inline-flex items-center rounded-md border border-transparent bg-white dark:bg-brand-surface px-3 py-2 text-sm font-medium leading-4 text-gray-700 dark:text-gray-300 transition duration-150 ease-in-out hover:text-brand-primary dark:hover:text-white focus:outline-none shadow-sm dark:shadow-none"
                                    >
                                        {{ user.name }}
                                        <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="bg-white dark:bg-brand-surface border border-gray-200 dark:border-gray-700 rounded-md shadow-lg">
                                        <!-- Theme Switcher -->
                                        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-bold uppercase">{{ __('Theme') }}</p>
                                            <div class="flex space-x-2 bg-gray-100 dark:bg-brand-dark p-1 rounded-lg">
                                                <button @click="setTheme('light')" :class="{'bg-white dark:bg-brand-gray shadow text-brand-primary': currentTheme === 'light', 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': currentTheme !== 'light'}" class="flex-1 p-1 rounded-md text-xs font-medium transition flex justify-center items-center" title="Light">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </button>
                                                <button @click="setTheme('dark')" :class="{'bg-white dark:bg-brand-gray shadow text-brand-primary': currentTheme === 'dark', 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': currentTheme !== 'dark'}" class="flex-1 p-1 rounded-md text-xs font-medium transition flex justify-center items-center" title="Dark">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                                    </svg>
                                                </button>
                                                <button @click="setTheme('system')" :class="{'bg-white dark:bg-brand-gray shadow text-brand-primary': currentTheme === 'system', 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': currentTheme !== 'system'}" class="flex-1 p-1 rounded-md text-xs font-medium transition flex justify-center items-center" title="System">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <DropdownLink :href="route('profile.show', user.profile?.username || user.id)" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-brand-gray hover:text-brand-primary dark:hover:text-brand-accent">
                                            {{ __('View Profile') }}
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.edit')" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-brand-gray hover:text-brand-primary dark:hover:text-brand-accent">
                                            {{ __('Edit Profile') }}
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-brand-gray hover:text-red-500 dark:hover:text-brand-secondary">
                                            {{ __('Log Out') }}
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                        <div v-else class="flex space-x-4 items-center">
                            <Link :href="route('login')" class="text-gray-300 hover:text-white transition font-medium">Login</Link>
                            <Link :href="route('register')" class="bg-brand-primary hover:bg-brand-secondary text-white px-4 py-2 rounded-md transition text-sm font-bold shadow-lg shadow-brand-primary/20">Register</Link>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 dark:text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 focus:bg-gray-100 dark:focus:bg-gray-800 focus:text-gray-700 dark:focus:text-gray-200 focus:outline-none"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-white dark:bg-brand-surface border-b border-gray-200 dark:border-gray-700">
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        {{ __('Dashboard') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink v-if="$page.props.modules.pool" :href="route('pool')" :active="route().current('pool')">
                        {{ __('Music Pool') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink v-if="$page.props.modules.academy" :href="route('academy')" :active="route().current('academy') || route().current('academy.*')">
                        {{ __('Academy') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink v-if="$page.props.modules.agenda" :href="route('calendar.index')" :active="route().current('calendar.*')">
                        {{ __('Agenda') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink v-if="$page.props.modules.assistant" :href="route('assistant.index')" :active="route().current('assistant.*')">
                        {{ __('AI Assistant') }}
                    </ResponsiveNavLink>
                <ResponsiveNavLink v-if="$page.props.auth.user && !$page.props.auth.user.is_pro" :href="route('subscription.index')" :active="route().current('subscription.*')" class="text-yellow-600 font-bold bg-yellow-50 dark:bg-yellow-900/20">
                    {{ __('Upgrade to PRO') }}
                </ResponsiveNavLink>
                     <ResponsiveNavLink v-if="isAdmin" :href="route('admin.dashboard')" :active="route().current('admin.*')" class="text-red-500 dark:text-red-400">
                        {{ __('Admin Panel') }}
                    </ResponsiveNavLink>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pb-1 pt-4" v-if="user">
                    <div class="px-4">
                        <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ user.name }}</div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')"> {{ __('Profile') }} </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button"> {{ __('Log Out') }} </ResponsiveNavLink>
                    </div>
                </div>
                 <div class="border-t border-gray-200 dark:border-gray-700 pb-1 pt-4 p-4 space-y-2" v-else>
                     <ResponsiveNavLink :href="route('login')"> {{ __('Log in') }} </ResponsiveNavLink>
                     <ResponsiveNavLink :href="route('register')"> {{ __('Register') }} </ResponsiveNavLink>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white dark:bg-brand-surface shadow border-b border-gray-200 dark:border-gray-800" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <!-- Flash Messages -->
            <!-- Flash Messages (Toast Container) -->
            <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-3">
                <TransitionGroup 
                    enter-active-class="transform ease-out duration-300 transition"
                    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <Toast 
                        v-for="toast in toasts" 
                        :key="toast.id" 
                        :message="toast.message" 
                        :type="toast.type" 
                        @close="removeToast(toast.id)"
                    />
                </TransitionGroup>
            </div>

            <slot />
        </main>
    </div>
</template>
