<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    isOwnProfile: Boolean,
});

const profile = props.user.profile || {};
const socialIcons = {
    instagram: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z',
    soundcloud: 'M23.22 10.559c-.375-3.328-3.085-5.918-6.422-6.142-3.328-.225-6.52 1.68-7.96 4.747-.384-.047-.775-.07-1.168-.07-3.9 0-7.062 2.924-7.062 6.53 0 3.6 3.16 6.53 7.062 6.53h15.534c3.12 0 5.656-2.345 5.656-5.232 0-2.887-2.535-5.232-5.64-5.232v-1.13zm-10.428 2.016c.386 0 .7.29.7.646v5.82c0 .356-.314.646-.7.646-.387 0-.702-.29-.702-.646v-5.82c0-.356.315-.646.702-.646zm-2.106 1.096c.386 0 .7.29.7.647v4.723c0 .356-.314.646-.7.646-.387 0-.702-.29-.702-.646v-4.723c0-.356.315-.647.702-.647zm4.212-3.66c.386 0 .7.29.7.645v5.82c0 .357-.314.647-.7.647-.386 0-.702-.29-.702-.647v-5.82c0-.356.316-.645.702-.645zm2.106 1.549c.386 0 .7.29.7.646v5.82c0 .357-.314.647-.7.647-.387 0-.702-.29-.702-.647v-5.82c0-.356.315-.646.702-.646zm11.996 4.965h-2.106v-3.71h2.106c1.942 0 3.522 1.46 3.522 3.257 0 1.795-1.58 3.255-3.522 3.255v-2.802z',
    website: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z',
    default: 'M12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2Z'
}

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <Head>
        <title>{{ user.name }} - {{ __('DJ Profile') }}</title>
        <meta name="description" :content="profile.biography ? profile.biography.substring(0, 160) : 'Check out ' + user.name + ' on Portal DJ.'" />
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="profile" />
        <meta property="og:title" :content="user.name + ' - DJ Profile'" />
        <meta property="og:description" :content="profile.biography ? profile.biography.substring(0, 160) : 'Check out ' + user.name + ' on Portal DJ.'" />
        <meta property="og:image" :content="profile.profile_image_path ? '/storage/' + profile.profile_image_path : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name)" />
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="user.name + ' - DJ Profile'" />
        <meta name="twitter:description" :content="profile.biography ? profile.biography.substring(0, 160) : 'Check out ' + user.name + ' on Portal DJ.'" />
        <meta name="twitter:image" :content="profile.profile_image_path ? '/storage/' + profile.profile_image_path : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name)" />
    </Head>

    <AuthenticatedLayout>
        <!-- Header / Banner -->
        <div class="h-48 bg-gradient-to-r from-brand-primary to-brand-secondary"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12">
            <div class="relative flex flex-col items-center md:items-start md:flex-row md:space-x-8">
                <!-- Profile Image -->
                <div class="relative group">
                    <img 
                        :src="profile.profile_image_path ? '/storage/' + profile.profile_image_path : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&color=7F9CF5&background=EBF4FF'" 
                        alt="Profile" 
                        class="h-40 w-40 rounded-full border-4 border-gray-900 shadow-xl object-cover bg-gray-800"
                    />
                    <div v-if="user.roles.includes('Verified')" class="absolute bottom-2 right-2 bg-blue-500 text-white p-1 rounded-full border-2 border-gray-900" title="Verified DJ">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="mt-4 md:mt-24 text-center md:text-left flex-1">
                    <h1 class="text-3xl font-bold text-white">{{ profile.username ? '@' + profile.username : user.name }}</h1>
                    <p v-if="profile.username" class="text-lg text-gray-400 font-medium">{{ user.name }}</p>
                    <div class="flex flex-col md:flex-row items-center md:items-start text-gray-400 mt-2 space-y-1 md:space-y-0 md:space-x-4 text-sm">
                        <span v-if="profile.dj_type" class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-brand-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                            {{ profile.dj_type.name }}
                        </span>
                        <span v-if="profile.city" class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ profile.city.name }}, {{ profile.city.country.name }}
                        </span>
                    </div>
                    
                    <div class="mt-4 flex space-x-3 justify-center md:justify-start">
                        <a v-for="social in user.social_networks" :key="social.id" :href="social.url.startsWith('http') ? social.url : (social.platform.base_url ? social.platform.base_url + social.url : 'https://' + social.url)" target="_blank" class="text-gray-400 hover:text-white transition">
                            <div v-if="social.platform && social.platform.icon" v-html="social.platform.icon" class="w-5 h-5 [&>svg]:w-full [&>svg]:h-full [&>svg]:fill-current"></div>
                            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path :d="socialIcons.default" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 md:mt-24" v-if="isOwnProfile">
                    <Link :href="route('profile.edit')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded font-bold text-sm transition">
                        {{ __('Edit Profile') }}
                    </Link>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-12">
                <!-- Left Column: Bio & Info -->
                <div class="space-y-6">
                    <!-- Biography -->
                    <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                        <h3 class="text-lg font-bold text-white mb-4">{{ __('About') }}</h3>
                        <p class="text-gray-400 text-sm whitespace-pre-line leading-relaxed">
                            {{ profile.biography || __('No biography provided yet.') }}
                        </p>
                    </div>

                    <!-- Details -->
                    <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                         <h3 class="text-lg font-bold text-white mb-4">{{ __('Details') }}</h3>
                         <div class="space-y-4">
                            <div>
                                <span class="block text-xs text-gray-500 uppercase font-bold">{{ __('Genres') }}</span>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span v-for="genre in user.genres" :key="genre.id" class="px-2 py-1 bg-gray-900 rounded text-xs text-brand-primary">
                                        {{ genre.name }}
                                    </span>
                                    <span v-if="(!user.genres || user.genres.length === 0)" class="text-gray-500 text-sm">-</span>
                                </div>
                            </div>
                         </div>
                    </div>
                    
                    <!-- Equipment -->
                    <div class="bg-gray-800 p-6 rounded-lg border border-gray-700" v-if="user.equipment && user.equipment.length > 0">
                        <h3 class="text-lg font-bold text-white mb-4">{{ __('Setup / Gear') }}</h3>
                        <div class="space-y-3">
                            <div v-for="item in user.equipment" :key="item.id" class="flex flex-col border-b border-gray-700 last:border-0 pb-3 last:pb-0">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-gray-200 font-medium">
                                        {{ item.brand?.name || item.equipment_model?.brand?.name }} 
                                        {{ item.model_name || item.equipment_model?.name }}
                                        <span v-if="item.equipment_model?.is_verified" class="ml-1 text-[10px] bg-blue-100/10 text-blue-300 px-1 rounded border border-blue-900">{{ __('Verified') }}</span>
                                    </span>
                                </div>
                                <span class="text-xs text-brand-accent mt-1">{{ item.type?.name || item.equipment_model?.type?.name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Experience -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                        <h3 class="text-lg font-bold text-white mb-6">{{ __('Experience') }}</h3>
                        
                        <div v-if="user.experiences && user.experiences.length > 0" class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                             <div v-for="exp in user.experiences" :key="exp.id" class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                <!-- Icon -->
                                <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-slate-300 group-[.is-active]:bg-emerald-500 text-slate-500 group-[.is-active]:text-emerald-50 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                    <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="12" height="10">
                                        <path fill-rule="nonzero" d="M10.422 1.257 4.655 7.025 2.553 4.923A.916.916 0 0 0 1.257 6.22l2.75 2.75a.916.916 0 0 0 1.296 0l6.415-6.416a.916.916 0 0 0-1.296-1.296Z" />
                                    </svg>
                                </div>
                                <!-- Content -->
                                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-gray-900 p-4 rounded border border-slate-700 shadow">
                                    <div class="flex items-center justify-between space-x-2 mb-1">
                                        <div class="font-bold text-slate-200">{{ exp.title }}</div>
                                        <time class="font-caveat font-medium text-brand-accent">{{ formatDate(exp.start_date) }} - {{ exp.end_date ? formatDate(exp.end_date) : __('Present') }}</time>
                                    </div>
                                    <div class="text-slate-400 text-sm mb-2">{{ exp.place }}</div>
                                    <div class="text-slate-500 text-sm">{{ exp.description }}</div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-gray-500 text-center py-4">
                            {{ __('No experience listed.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
