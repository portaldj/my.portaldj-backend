<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicCalendar from './Partials/PublicCalendar.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    user: Object,
    isOwnProfile: Boolean,
    events: Array,
    upcomingEvents: Array,
});

const page = usePage();
const locale = computed(() => {
    const l = page.props.locale || 'en';
    return l.toLowerCase().startsWith('es') ? 'es' : 'en';
});

const showCalendarModal = ref(false);

const profile = props.user.profile || {};
const contactInfo = {
    email: props.user.email,
    phone: profile.phone,
    email_public: !!profile.is_email_public,
    phone_public: !!profile.is_phone_public,
};
const socialIcons = {
    instagram: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z',
    soundcloud: 'M23.22 10.559c-.375-3.328-3.085-5.918-6.422-6.142-3.328-.225-6.52 1.68-7.96 4.747-.384-.047-.775-.07-1.168-.07-3.9 0-7.062 2.924-7.062 6.53 0 3.6 3.16 6.53 7.062 6.53h15.534c3.12 0 5.656-2.345 5.656-5.232 0-2.887-2.535-5.232-5.64-5.232v-1.13zm-10.428 2.016c.386 0 .7.29.7.646v5.82c0 .356-.314.646-.7.646-.387 0-.702-.29-.702-.646v-5.82c0-.356.315-.646.702-.646zm-2.106 1.096c.386 0 .7.29.7.647v4.723c0 .356-.314.646-.7.646-.387 0-.702-.29-.702-.646v-4.723c0-.356.315-.647.702-.647zm4.212-3.66c.386 0 .7.29.7.645v5.82c0 .357-.314.647-.7.647-.386 0-.702-.29-.702-.647v-5.82c0-.356.316-.645.702-.645zm2.106 1.549c.386 0 .7.29.7.646v5.82c0 .357-.314.647-.7.647-.387 0-.702-.29-.702-.647v-5.82c0-.356.315-.646.702-.646zm11.996 4.965h-2.106v-3.71h2.106c1.942 0 3.522 1.46 3.522 3.257 0 1.795-1.58 3.255-3.522 3.255v-2.802z',
    website: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z',
    default: 'M12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2Z'
}

const formatDate = (dateString, options = null) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString(locale.value, options || {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatTime = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleTimeString(locale.value, {
        hour: '2-digit',
        minute: '2-digit'
    });
}
const formatEventDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString(locale.value, { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Modal State
const showEventDetailsModal = ref(false);
const selectedEvent = ref(null);

function openEventDetails(event) {
    // Sanitize URL similar to PublicCalendar
    let safeUrl = null;
    if (event.url && typeof event.url === 'string') {
        const trimmed = event.url.trim();
        if (trimmed !== '' && trimmed.toLowerCase() !== 'null') {
            safeUrl = trimmed;
        }
    }

    selectedEvent.value = {
        title: event.title,
        description: event.description,
        start: event.start,
        end: event.end,
        url: safeUrl,
    };
    showEventDetailsModal.value = true;
}

function closeEventDetails() {
    showEventDetailsModal.value = false;
    selectedEvent.value = null;
}

// JSON-LD Structured Data for SEO
const jsonLd = computed(() => {
    const data = {
        "@context": "https://schema.org",
        "@type": "Person",
        "name": props.user.name,
        "image": profile.profile_image_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(props.user.name)}`,
        "description": profile.biography || `DJ Profile of ${props.user.name}`,
        "url": window.location.href, // This might be client-side only. For SSR, rely on props.ziggy or similar if available, or just canonical.
    };
    
    if (profile.job_title) data.jobTitle = profile.job_title;
    if (profile.city) data.address = { "@type": "PostalAddress", "addressLocality": profile.city.name, "addressCountry": profile.city.country.name };
    if (props.user.email && profile.is_email_public) data.email = props.user.email;
    
    return data;
});
</script>

<template>
    <Head>
        <title>{{ user.name }} - {{ __('DJ Profile') }}</title>
        <meta name="description" :content="profile.biography ? profile.biography.substring(0, 160) : (__('Check out :name on Portal DJ.', { name: user.name }))" />
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="profile" />
        <meta property="og:title" :content="user.name + ' - DJ Profile'" />
        <meta property="og:description" :content="profile.biography ? profile.biography.substring(0, 160) : (__('Check out :name on Portal DJ.', { name: user.name }))" />
        <meta property="og:image" :content="profile.profile_image_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name)" />
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="user.name + ' - DJ Profile'" />
        <meta name="twitter:description" :content="profile.biography ? profile.biography.substring(0, 160) : (__('Check out :name on Portal DJ.', { name: user.name }))" />
        <meta name="twitter:image" :content="profile.profile_image_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name)" />
        
        <!-- JSON-LD Structured Data -->
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(jsonLd) }}
        </component>
    </Head>

    <AuthenticatedLayout>
        <!-- Header / Banner -->
        <div class="h-48 bg-gradient-to-r from-brand-primary to-brand-secondary"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12">
            <div class="relative flex flex-col items-center md:items-start md:flex-row md:space-x-8">
                <!-- Profile Image -->
                <div class="relative group">
                    <img 
                        :src="profile.medium_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&color=7F9CF5&background=EBF4FF'" 
                        :alt="__('Profile picture of :name', { name: user.name })" 
                        class="h-40 w-40 rounded-full border-4 border-gray-900 shadow-xl object-cover bg-gray-800"
                    />
                    <div v-if="user.roles.includes('Verified')" class="absolute bottom-2 right-2 bg-blue-500 text-white p-1 rounded-full border-2 border-gray-900" :title="__('Verified DJ')">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="mt-4 md:mt-24 text-center md:text-left flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ profile.username ? '@' + profile.username : user.name }}</h1>
                    <p v-if="profile.username" class="text-lg text-gray-600 dark:text-gray-400 font-medium">{{ user.name }}</p>
                    <div class="flex flex-col md:flex-row items-center md:items-start text-gray-600 dark:text-gray-400 mt-2 space-y-1 md:space-y-0 md:space-x-4 text-sm">
                        <span v-if="profile.dj_type" class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-brand-primary dark:text-brand-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                            {{ profile.dj_type.name }}
                        </span>
                        <span v-if="profile.city" class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ profile.city.name }}, {{ profile.city.country.name }}
                        </span>
                    </div>
                    
                    <div class="mt-4 flex space-x-3 justify-center md:justify-start">
                        <a v-for="social in user.social_networks" :key="social.id" :href="social.url.startsWith('http') ? social.url : (social.platform.base_url ? social.platform.base_url + social.url : 'https://' + social.url)" target="_blank" class="text-gray-400 hover:text-brand-primary dark:hover:text-white transition">
                            <div v-if="social.platform && social.platform.icon" v-html="social.platform.icon" class="w-5 h-5 [&>svg]:w-full [&>svg]:h-full [&>svg]:fill-current"></div>
                            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path :d="socialIcons.default" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Actions -->
                <!-- Actions -->
                <div class="mt-6 md:mt-24 flex space-x-2" v-if="isOwnProfile">
                    <Link :href="route('profile.edit')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded font-bold text-sm transition">
                        {{ __('Edit Profile') }}
                    </Link>
                     <Link :href="route('profile.activity')" class="px-4 py-2 bg-brand-primary hover:bg-brand-secondary text-white rounded font-bold text-sm transition">
                        {{ __('Your Activity') }}
                    </Link>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-12">
                <!-- Left Column: Bio & Info -->
                <div class="space-y-6">
                    <!-- About -->
                    <div class="bg-white dark:bg-brand-surface p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-brand-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ __('About') }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ profile.biography || __('No biography provided.') }}</p>
                    </div>

                    <!-- Details -->
                    <div class="bg-white dark:bg-brand-surface p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                         <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-brand-accent p-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ __('Details') }}
                        </h2>
                        <ul class="space-y-3 text-sm">
                            <li v-if="profile.is_email_public && user.email" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                                <span class="text-gray-500">{{ __('Email') }}</span>
                                <a :href="'mailto:' + user.email" class="text-gray-900 dark:text-white font-medium hover:underline">{{ user.email }}</a>
                            </li>
                             <li v-if="profile.is_phone_public && profile.phone" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                                <span class="text-gray-500">{{ __('Phone') }}</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ profile.phone }}</span>
                            </li>
                            <li v-if="profile.country" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                                <span class="text-gray-500">{{ __('Country') }}</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ profile.country.name }}</span>
                            </li>
                             <li v-if="profile.city" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                                <span class="text-gray-500">{{ __('City') }}</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ profile.city.name }}</span>
                            </li>
                             <li v-if="profile.dj_type" class="flex justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                                <span class="text-gray-500">{{ __('DJ Type') }}</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ profile.dj_type.name }}</span>
                            </li>
                            <!-- Genres -->
                            <li v-if="user.genres && user.genres.length > 0" class="pt-2">
                                <span class="block text-gray-500 mb-2">{{ __('Genres') }}</span>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="genre in user.genres" :key="genre.id" class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-xs text-brand-primary dark:text-brand-accent font-semibold">
                                        {{ genre.name }}
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>

                     <!-- DJ Gear / Setup -->
                    <div class="bg-white dark:bg-brand-surface p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700" v-if="user.equipment && user.equipment.length > 0">
                        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            {{ __('Setup / Gear') }}
                        </h2>
                        <div class="space-y-4">
                             <!-- Equipment List -->
                            <div v-for="item in user.equipment" :key="item.id" class="flex items-start space-x-3 pb-3 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0">
                                 <div class="flex-shrink-0 w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600">
                                     <img v-if="item.image_url" :src="item.image_url" class="w-full h-full object-cover">
                                     <svg v-else class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                                 </div>
                                 <div>
                                     <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                         {{ item.equipment_model ? item.equipment_model.name : item.model_name }}
                                     </h4>
                                      <p class="text-xs text-brand-primary dark:text-brand-accent">
                                          {{ item.type ? item.type.name : (item.equipment_model?.type ? item.equipment_model.type.name : '') }}
                                      </p>
                                     <p class="text-xs text-gray-500">
                                         <span v-if="item.brand" class="mr-1">{{ item.brand.name }}</span>
                                         <span v-else-if="item.equipment_model?.brand">{{ item.equipment_model.brand.name }}</span>
                                     </p>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Agenda & Experience -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 inline-block">{{ __('Experience') }}</h2>
                        
                        <div v-if="user.experiences && user.experiences.length > 0" class="space-y-6 relative border-l-2 border-gray-200 dark:border-gray-700 ml-3 pl-8 pb-4">
                            <div v-for="exp in user.experiences" :key="exp.id" class="relative">
                                <span class="absolute -left-10 top-1 bg-brand-primary h-4 w-4 rounded-full border-4 border-gray-100 dark:border-gray-900"></span>
                                <div class="bg-white dark:bg-brand-surface p-5 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 transition hover:border-brand-primary dark:hover:border-brand-primary">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ exp.title }}
                                    </h4>
                                    <p class="text-md text-gray-700 dark:text-gray-300 mb-1">
                                        {{ exp.place }}
                                    </p>
                                    <p class="text-brand-secondary text-sm font-semibold mb-2">
                                        {{ formatDate(exp.start_date) }} - {{ exp.is_current ? __('Currently working here') : formatDate(exp.end_date) }}
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm whitespace-pre-line">{{ exp.description }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-gray-500 italic bg-gray-50 dark:bg-brand-surface/50 p-6 rounded-lg text-center border border-dashed border-gray-300 dark:border-gray-700">
                            {{ __('No experience listed yet.') }}
                        </div>
                    </div>

                    <!-- Agenda List & Modal -->
                    <div class="bg-white dark:bg-brand-surface p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-2">
                             <h2 class="text-2xl font-bold text-gray-900 dark:text-white inline-block">{{ __('Agenda') }}</h2>
                             <button @click="showCalendarModal = true" class="text-sm font-semibold text-brand-primary hover:text-brand-secondary underline">
                                 {{ __('View Full Agenda') }}
                             </button>
                        </div>

                         <div v-if="upcomingEvents && upcomingEvents.length > 0" class="space-y-4">
                            <div v-for="event in upcomingEvents" :key="event.id" @click="openEventDetails(event)" class="cursor-pointer flex flex-col sm:flex-row bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-100 dark:border-gray-700 transition hover:border-brand-primary dark:hover:border-brand-primary">
                                <!-- Date Badge -->
                                <div class="flex-shrink-0 flex items-center mb-3 sm:mb-0 sm:mr-4">
                                    <div class="flex flex-col items-center justify-center bg-white dark:bg-brand-surface border border-gray-200 dark:border-gray-600 rounded-lg w-16 h-16 shadow-sm">
                                        <span class="text-xs font-bold text-red-500 uppercase">{{ new Date(event.start).toLocaleString(locale, { month: 'short' }) }}</span>
                                        <span class="text-xl font-extrabold text-gray-900 dark:text-white">{{ new Date(event.start).getDate() }}</span>
                                    </div>
                                </div>
                                <!-- Event Info -->
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ event.title }}</h4>
                                    <p class="text-sm text-brand-secondary font-medium mb-1">
                                        {{ new Date(event.start).toLocaleTimeString(locale, { hour: '2-digit', minute: '2-digit' }) }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ event.description }}</p>
                                </div>
                                <!-- Action -->
                                <div class="mt-3 sm:mt-0 sm:ml-4 flex items-center">
                                    <button class="text-sm font-medium text-brand-primary border border-brand-primary px-3 py-1.5 rounded-full hover:bg-brand-primary hover:text-white transition">
                                        {{ __('More Info') }}
                                    </button>
                                </div>
                            </div>
                         </div>

                         <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400 italic bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                             {{ __('No upcoming events scheduled.') }}
                         </div>


                         <div class="mt-4 text-center sm:text-right">
                             <SecondaryButton @click="showCalendarModal = true" class="w-full sm:w-auto justify-center">
                                 {{ __('View Full Agenda') }}
                             </SecondaryButton>
                         </div>
                    </div>

                    <!-- Full Calendar Modal -->
                    <Modal :show="showCalendarModal" @close="showCalendarModal = false">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Full Agenda') }}</h2>
                                <button @click="showCalendarModal = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <PublicCalendar v-if="showCalendarModal" :initialEvents="events" :username="profile.username" :contactInfo="contactInfo" />
                             <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="showCalendarModal = false">
                                    {{ __('Close') }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </Modal>

                    <!-- Event Details Modal (Single Event) -->
                    <Modal :show="showEventDetailsModal" @close="closeEventDetails">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ selectedEvent?.title }}</h2>
                            <div class="text-sm text-gray-500 mb-4">
                                {{ formatEventDate(selectedEvent?.start) }} 
                                <span v-if="selectedEvent?.end"> - {{ formatEventDate(selectedEvent?.end) }}</span>
                            </div>
                            
                            <p v-if="selectedEvent?.description" class="text-gray-700 dark:text-gray-300 mb-4 whitespace-pre-wrap">
                                {{ selectedEvent.description }}
                            </p>

                            <div v-if="selectedEvent?.url" class="mt-4">
                                <a :href="selectedEvent.url" target="_blank" class="inline-flex items-center px-4 py-2 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-secondary transition">
                                    {{ __('More Info / Tickets') }}
                                </a>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closeEventDetails">{{ __('Close') }}</SecondaryButton>
                            </div>
                        </div>
                    </Modal>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
