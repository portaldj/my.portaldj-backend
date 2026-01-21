<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;

const navItems = [
    { name: 'Dashboard', route: 'admin.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Music Manager', route: 'admin.music.index', icon: 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3' },
    { name: 'Equipment', route: 'admin.equipment.index', icon: 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4' },
    { name: 'Academy', route: 'admin.academy.index', icon: 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z' },
    { name: 'Users', route: 'admin.users.index', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
    { name: 'Social Platforms', route: 'admin.social-platforms.index', icon: 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1' },
    { name: 'Taxonomy', route: 'admin.taxonomy.index', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
    { name: 'Global Settings', route: 'admin.settings.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];
</script>

<template>
    <div class="min-h-screen bg-gray-900 text-gray-100 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 border-r border-gray-700 hidden md:flex flex-col">
            <div class="h-16 flex items-center justify-center border-b border-gray-700">
                <Link :href="route('dashboard')" class="text-xl font-bold text-brand-primary">
                    PORTAL DJ <span class="text-xs text-gray-400">ADMIN</span>
                </Link>
            </div>
            
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <Link 
                    v-for="item in navItems" 
                    :key="item.name" 
                    :href="route(item.route)" 
                    class="flex items-center px-4 py-3 rounded-lg transition-colors group"
                    :class="route().current(item.route) ? 'bg-brand-primary text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white'"
                >
                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    {{ item.name }}
                </Link>
            </nav>

            <div class="p-4 border-t border-gray-700">
                 <Link :href="route('profile.edit')" class="flex items-center space-x-3 text-sm text-gray-400 hover:text-white transition">
                    <div class="h-8 w-8 rounded-full bg-gray-600 flex items-center justify-center">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ user.name }}</p>
                        <p class="text-xs">View Profile</p>
                    </div>
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Mobile Header -->
            <header class="md:hidden bg-gray-800 border-b border-gray-700 h-16 flex items-center justify-between px-4">
                <Link :href="route('dashboard')" class="text-lg font-bold text-brand-primary">
                    PORTAL DJ
                </Link>
                <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-gray-400 hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </header>

            <!-- Mobile Menu -->
            <div v-show="showingNavigationDropdown" class="md:hidden bg-gray-800 border-b border-gray-700">
                <nav class="p-2 space-y-1">
                    <Link 
                        v-for="item in navItems" 
                        :key="item.name" 
                        :href="route(item.route)" 
                        class="block px-3 py-2 rounded-md text-base font-medium"
                         :class="route().current(item.route) ? 'bg-brand-primary text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white'"
                    >
                        {{ item.name }}
                    </Link>
                </nav>
            </div>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                 <header v-if="$slots.header" class="mb-8">
                    <h1 class="text-2xl font-bold text-white">
                        <slot name="header" />
                    </h1>
                </header>
                <slot />
            </main>
        </div>
    </div>
</template>
