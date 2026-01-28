<script setup>
import { ref, onMounted, computed } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    username: String,
    initialEvents: {
        type: Array,
        default: () => []
    },
    contactInfo: Object // { email: string, phone: string, email_public: bool, phone_public: bool }
});

import { usePage } from '@inertiajs/vue3';
const page = usePage();
const currentLocale = computed(() => {
    const l = page.props.locale || 'en';
    return l.toLowerCase().startsWith('es') ? 'es' : 'en';
});

const events = ref([...props.initialEvents]);
const fullCalendar = ref(null);

// If we need to fetch events dynamically (optional since we pass initialEvents)
// const fetchEvents = ...

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
    initialView: 'timeGridWeek',
    locales: [esLocale],
    locale: currentLocale.value === 'es' ? esLocale : 'en',
    headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek'
    },
    editable: false,
    selectable: true,
    selectMirror: true, // Show placeholder while selecting
    dayMaxEvents: true,
    events: events.value,
    select: handleDateSelect,
    eventClick: handleEventClick,
    height: 'auto',
}));

// Fix for Modal rendering issue
onMounted(() => {
    // Small delay to ensure modal transition is complete/layout is ready
    setTimeout(() => {
        if (fullCalendar.value) {
            fullCalendar.value.getApi().updateSize();
        }
    }, 100);
});

// Modals
const showEventModal = ref(false);
const showContactModal = ref(false);
const selectedEvent = ref(null);
const selectedDate = ref(null);

function handleDateSelect(selectInfo) {
    selectedDate.value = selectInfo.startStr;
    showContactModal.value = true;
    // Clear selection
    // selectInfo.view.calendar.unselect();
}

function handleEventClick(clickInfo) {
    clickInfo.jsEvent.preventDefault(); // Prevent browser navigation if url is present
    
    const eventUrl = clickInfo.event.extendedProps.url || clickInfo.event.url;
    
    // logic to safely determine if we have a valid url
    let safeUrl = null;
    if (eventUrl && typeof eventUrl === 'string') {
        const trimmed = eventUrl.trim();
        if (trimmed !== '' && trimmed.toLowerCase() !== 'null') {
            safeUrl = trimmed;
        }
    }

    selectedEvent.value = {
        title: clickInfo.event.title,
        description: clickInfo.event.extendedProps.description,
        start: clickInfo.event.start,
        end: clickInfo.event.end,
        url: safeUrl,
    };
    showEventModal.value = true;
}

function closeModals() {
    showEventModal.value = false;
    showContactModal.value = false;
    selectedEvent.value = null;
    selectedDate.value = null;
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleString(currentLocale.value, { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>

<template>
    <div class="calendar-public">
        <FullCalendar ref="fullCalendar" :options="calendarOptions" />

        <!-- Event Details Modal -->
        <Modal :show="showEventModal" @close="closeModals">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ selectedEvent?.title }}</h2>
                <div class="text-sm text-gray-500 mb-4">
                    {{ formatDate(selectedEvent?.start) }} 
                    <span v-if="selectedEvent?.end"> - {{ formatDate(selectedEvent?.end) }}</span>
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
                    <SecondaryButton @click="closeModals">{{ __('Close') }}</SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- Availability / Contact Modal -->
        <Modal :show="showContactModal" @close="closeModals">
            <div class="p-6 text-center">
                <div class="mb-4">
                    <div class="bg-green-100 dark:bg-green-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Available for Booking!') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('This date appears to be available.') }}</p>
                </div>

                <div class="space-y-4 text-left bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-2">{{ __('Contact Info') }}</h3>
                    
                    <div v-if="contactInfo?.email && contactInfo?.email_public" class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        <a :href="'mailto:' + contactInfo.email" class="text-brand-primary hover:underline font-medium break-all">{{ contactInfo.email }}</a>
                    </div>
                    
                    <div v-if="contactInfo?.phone && contactInfo?.phone_public" class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        <a :href="'tel:' + contactInfo.phone" class="text-gray-900 dark:text-white hover:text-brand-primary font-medium">{{ contactInfo.phone }}</a>
                    </div>

                    <div v-if="!contactInfo?.email_public && !contactInfo?.phone_public" class="text-sm text-gray-500 italic">
                        {{ __('This DJ has not shared their contact information publicly.') }}
                    </div>
                </div>

                <div class="mt-6 flex justify-center">
                     <SecondaryButton @click="closeModals">{{ __('Close') }}</SecondaryButton>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
/* Scoped styles for public calendar specifics */
.calendar-public :deep(.fc-header-toolbar) {
    font-size: 0.85em; 
}
.calendar-public :deep(.fc-toolbar-title) {
    font-size: 1.2em;
}

/* Dark Mode Overrides */
:global(.dark) .calendar-public :deep(.fc-toolbar-title) {
    color: #ffffff !important;
}
:global(.dark) .calendar-public :deep(.fc-button) {
    color: #ffffff !important;
    background-color: #374151;
    border-color: #4b5563;
}
:global(.dark) .calendar-public :deep(.fc-button-active) {
    background-color: #1f2937;
    border-color: #374151;
}
:global(.dark) .calendar-public :deep(.fc-daygrid-day-number),
:global(.dark) .calendar-public :deep(.fc-col-header-cell-cushion) {
    color: #e5e7eb;
}
</style>
