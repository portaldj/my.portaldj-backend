<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    latestDJs: Array,
    filters: Object,
    request: Object,
});

const form = useForm({
    search: props.request.search || '',
    country_id: props.request.country_id || '',
    city_id: props.request.city_id || '',
});

const availableCities = computed(() => {
    if (!form.country_id) return [];
    const country = props.filters.countries.find(c => c.id == form.country_id);
    return country ? country.cities : [];
});
</script>

<template>
    <Head :title="__('Welcome')" />
    <div class="min-h-screen bg-gray-100 dark:bg-brand-dark text-gray-900 dark:text-gray-100 transition-colors duration-300">
        <!-- Top Navigation (Login/Register) -->
        <nav v-if="canLogin" class="absolute top-0 right-0 p-6 z-50 flex justify-end space-x-4">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-200 hover:text-white"
            >
                {{ __('Dashboard') }}
            </Link>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-200 hover:text-white"
                >
                     {{ __('Log in') }}
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="font-semibold text-brand-primary hover:text-brand-secondary"
                >
                     {{ __('Register') }}
                </Link>
            </template>
        </nav>

        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-brand-surface border-b border-gray-800">
             <!-- Background / Overlay -->
             <div class="absolute inset-0 bg-brand-primary/10"></div>
             
             <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 flex flex-col items-center">
                 <div class="text-center w-full">
                     <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-white mb-6">
                         {{ __('Discover the World\'s Best') }} <span class="bg-gradient-to-r from-brand-secondary to-brand-accent bg-clip-text text-transparent">DJs</span>
                     </h1>
                     <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-400 mb-10">
                         {{ __('Connect with top talent') }}
                     </p>

                     <!-- Search Box -->
                     <form @submit.prevent="$inertia.get('/', form, { preserveState: true, replace: true })" class="max-w-4xl mx-auto bg-gray-900/80 backdrop-blur-md p-4 rounded-xl shadow-2xl border border-gray-700 flex flex-col md:flex-row gap-4 w-full">
                         <div class="flex-1">
                             <input 
                                v-model="form.search" 
                                type="text" 
                                :placeholder="__('Search by name')" 
                                class="w-full bg-gray-800 border-gray-700 text-white rounded-lg focus:ring-brand-accent focus:border-brand-accent"
                             >
                         </div>
                         <div class="w-full md:w-48">
                             <select v-model="form.country_id" class="w-full bg-gray-800 border-gray-700 text-white rounded-lg focus:ring-brand-accent focus:border-brand-accent">
                                 <option value="">{{ __('All Countries') }}</option>
                                 <option v-for="country in filters.countries" :key="country.id" :value="country.id">
                                     {{ country.name }}
                                 </option>
                             </select>
                         </div>
                         <div class="w-full md:w-48">
                             <select v-model="form.city_id" class="w-full bg-gray-800 border-gray-700 text-white rounded-lg focus:ring-brand-accent focus:border-brand-accent" :disabled="!form.country_id">
                                 <option value="">{{ __('All Cities') }}</option>
                                 <option v-for="city in availableCities" :key="city.id" :value="city.id">
                                     {{ city.name }}
                                 </option>
                             </select>
                         </div>
                         <button type="submit" class="w-full md:w-auto px-8 py-3 bg-brand-primary hover:bg-brand-secondary text-white font-bold rounded-lg transition shadow-lg shadow-brand-primary/20">
                             {{ __('Search') }}
                         </button>
                     </form>
                 </div>
             </div>
        </div>

        <!-- Latest DJs Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 border-l-4 border-brand-accent pl-4">{{ __('Latest Arrivals') }}</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="dj in latestDJs" :key="dj.id" class="bg-white dark:bg-brand-surface rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 hover:border-brand-primary dark:hover:border-brand-accent transition group shadow-md dark:shadow-none">
                    <Link :href="route('profile.show', dj.profile?.username || 'unknown')" class="block">
                        <div class="h-48 bg-gray-200 dark:bg-gray-800 overflow-hidden relative">
                             <img 
                                :src="dj.profile?.profile_image_path ? '/storage/' + dj.profile.profile_image_path : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(dj.name)" 
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            >
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-gray-900/80 to-transparent h-20"></div>
                            <div class="absolute bottom-4 left-4">
                                <span v-if="dj.profile?.djType" class="px-2 py-1 bg-brand-primary/80 text-white text-xs rounded font-bold uppercase tracking-wider">
                                    {{ dj.profile.djType.name }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1 group-hover:text-brand-primary dark:group-hover:text-brand-accent transition">{{ dj.name }}</h3>
                            <p class="text-sm text-gray-500 mb-4 flex items-center" v-if="dj.profile?.city">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ dj.profile.city.name }}, {{ dj.profile.city.country.name }}
                            </p>
                            <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">
                                {{ dj.profile?.biography || 'No biography yet.' }}
                            </p>
                        </div>
                    </Link>
                </div>
            </div>

            <div v-if="latestDJs.length === 0" class="text-center py-20 text-gray-500">
                {{ __('No DJs found') }}
            </div>
        </div>

        <footer class="bg-gray-900 text-gray-500 py-12 text-center border-t border-gray-800">
             Portal DJ v{{ laravelVersion }} (PHP {{ phpVersion }}) <br>
             &copy; {{ new Date().getFullYear() }} All Rights Reserved.
        </footer>
    </div>
</template>
