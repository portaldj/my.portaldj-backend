<script setup>
import { ref, computed } from 'vue';
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
</script>

<template>
    <div class="min-h-screen bg-brand-dark text-gray-100 font-sans">
        <!-- Top Navigation -->
        <nav class="border-b border-gray-800 bg-brand-gray/80 backdrop-blur-md sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('dashboard')">
                                <span class="text-2xl font-bold bg-gradient-to-r from-brand-secondary to-brand-accent bg-clip-text text-transparent">
                                    PORTAL DJ
                                </span>
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <PremiumNavLink :href="route('dashboard')" :active="route().current('dashboard') || route().current('feed.*')">
                                {{ __('Global Feed') }}
                            </PremiumNavLink>
                            <PremiumNavLink :href="route('pool')" :active="route().current('pool')">
                                {{ __('Music Pool') }}
                            </PremiumNavLink>
                            <PremiumNavLink :href="route('academy') || route('academy.*')" :active="route().current('academy') || route().current('academy.*')">
                                {{ __('Academy') }}
                            </PremiumNavLink>
                            <NavLink :href="route('assistant.index')" :active="route().current('assistant.*')">
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
                                        class="inline-flex items-center rounded-md border border-transparent bg-brand-surface px-3 py-2 text-sm font-medium leading-4 text-gray-300 transition duration-150 ease-in-out hover:text-white focus:outline-none"
                                    >
                                        {{ user.name }}
                                        <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="bg-brand-surface border border-gray-700 rounded-md">
                                        <DropdownLink :href="route('profile.show', user.profile?.username || user.id)" class="text-gray-300 hover:bg-brand-gray hover:text-brand-accent">
                                            {{ __('View Profile') }}
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.edit')" class="text-gray-300 hover:bg-brand-gray hover:text-brand-accent">
                                            {{ __('Edit Profile') }}
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="text-gray-300 hover:bg-brand-gray hover:text-brand-secondary">
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
                            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-800 hover:text-gray-200 focus:bg-gray-800 focus:text-gray-200 focus:outline-none"
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
            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-brand-surface">
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        {{ __('Dashboard') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('pool')" :active="route().current('pool')">
                        {{ __('Music Pool') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('academy')" :active="route().current('academy') || route().current('academy.*')">
                        {{ __('Academy') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('assistant.index')" :active="route().current('assistant.*')">
                    {{ __('AI Assistant') }}
                </ResponsiveNavLink>
                <ResponsiveNavLink v-if="$page.props.auth.user && !$page.props.auth.user.is_pro" :href="route('subscription.index')" :active="route().current('subscription.*')" class="text-yellow-600 font-bold bg-yellow-50">
                    {{ __('Upgrade to PRO') }}
                </ResponsiveNavLink>
                     <ResponsiveNavLink v-if="isAdmin" :href="route('admin.dashboard')" :active="route().current('admin.*')" class="text-red-400">
                        {{ __('Admin Panel') }}
                    </ResponsiveNavLink>
                </div>

                <div class="border-t border-gray-700 pb-1 pt-4" v-if="user">
                    <div class="px-4">
                        <div class="text-base font-medium text-gray-200">{{ user.name }}</div>
                        <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')"> {{ __('Profile') }} </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button"> {{ __('Log Out') }} </ResponsiveNavLink>
                    </div>
                </div>
                 <div class="border-t border-gray-700 pb-1 pt-4 p-4 space-y-2" v-else>
                     <ResponsiveNavLink :href="route('login')"> {{ __('Log in') }} </ResponsiveNavLink>
                     <ResponsiveNavLink :href="route('register')"> {{ __('Register') }} </ResponsiveNavLink>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-brand-surface shadow border-b border-gray-800" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <!-- Flash Messages -->
            <div v-if="$page.props.flash.success" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">{{ __('Success!') }} </strong>
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>
            </div>
            <div v-if="$page.props.flash.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">{{ __('Error!') }} </strong>
                    <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                </div>
            </div>
            <div v-if="$page.props.flash.message" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">{{ __('Info:') }} </strong>
                    <span class="block sm:inline">{{ $page.props.flash.message }}</span>
                </div>
            </div>

            <slot />
        </main>
    </div>
</template>
