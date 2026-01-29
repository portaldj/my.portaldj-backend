<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    events: Array,
});

import { usePage } from '@inertiajs/vue3';
const page = usePage();
const currentLocale = computed(() => {
    const l = page.props.locale || 'en';
    return l.toLowerCase().startsWith('es') ? 'es' : 'en';
});

// Watch for changes in events prop (e.g. after create/update)
import { watch } from 'vue';
watch(() => props.events, (newEvents) => {
    // With computed options, we don't modify calendarOptions.value directly.
    // Instead, we just let the computed property re-evaluate if we depend on props.events.
    // BUT FullCalendar options object needs 'events' array.
    // If we make calendarOptions computed, it will return a NEW object every time props.events change.
    // FullCalendar-Vue handles this by detecting new options.
});

// We need a separate ref for events to mutate it if needed (e.g. optimistic updates)?
// Or just derive it.
// Let's use computed for the whole options object.

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
    initialView: 'dayGridMonth',
    locales: [esLocale],
    locale: currentLocale.value, // dynamically switches 'es' <-> 'en'
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    editable: true,
    selectable: true,
    dayMaxEvents: true,
    events: props.events.map(e => ({
        id: e.id,
        title: e.title,
        start: e.start,
        end: e.end,
        extendedProps: {
            description: e.description,
            url: e.url,
            is_public: e.is_public,
        },
        className: e.is_public ? 'bg-brand-primary border-brand-primary text-white' : 'bg-gray-500 border-gray-500 text-white',
    })),
    select: handleDateSelect,
    eventClick: handleEventClick,
    eventDrop: handleEventDrop,
    eventResize: handleEventResize,
}));

// Modal State
const showEventModal = ref(false);
const isEditing = ref(false);
const form = useForm({
    id: null,
    title: '',
    description: '',
    start: '',
    end: '',
    url: '',
    is_public: true,
    promote: false,
    press_release: '',
});

function handleDateSelect(selectInfo) {
    isEditing.value = false;
    form.reset();

    // Use formatting helper to ensure YYYY-MM-DDTHH:mm format for datetime-local input
    // FullCalendar provides Date objects in .start and .end
    form.start = formatDateForInput(selectInfo.start);
    
    // For end date, sometimes it is exclusive or not provided.
    // If allDay (Month view), we might want to default to start + 1 hour or just leave end blank?
    // Let's use the provided end if available (selection across days or time slots)
    if (selectInfo.end) {
        // If it's an all-day selection (e.g. Month view click), the end date is the start of the next day.
        // We probably don't want that as the default "End" time for a single day event.
        if (selectInfo.allDay && (selectInfo.end - selectInfo.start === 86400000)) {
           // Single day click in Month view
           // Set end to 1 hour after start (assuming start is 00:00 or current time?)
           // Month view click start is 00:00 local.
           // Let's default to 12:00 PM for month view clicks? Or just keep it simple.
           
           // Better UX: If All Day, selectInfo.start is 00:00.
           // Let's set start to 12:00 PM and End to 13:00 PM just to have a time.
           const startDate = new Date(selectInfo.start);
           startDate.setHours(12);
           form.start = formatDateForInput(startDate);
           
           const endDate = new Date(startDate);
           endDate.setHours(13);
           form.end = formatDateForInput(endDate);
        } else {
            form.end = formatDateForInput(selectInfo.end);
        }
    } else {
        form.end = '';
    }

    showEventModal.value = true;
}

function handleEventClick(clickInfo) {
    isEditing.value = true;
    const event = clickInfo.event;
    form.id = event.id;
    form.title = event.title;
    form.description = event.extendedProps.description;
    form.url = event.extendedProps.url;
    form.is_public = !!event.extendedProps.is_public;
    // We don't load promotion status back into the form for editing events yet, 
    // as the requirement implies this is triggered on creation or new request.
    // For simplicity, we reset these.
    form.promote = false;
    form.press_release = '';
    
    // Format dates for datetime-local input (YYYY-MM-DDTHH:mm)
    // FullCalendar dates are Date objects.
    form.start = formatDateForInput(event.start);
    form.end = event.end ? formatDateForInput(event.end) : '';

    showEventModal.value = true;
}

function formatDateForInput(date) {
    if (!date) return '';
    // Adjust for timezone offset to keep local time in input
    const d = new Date(date);
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    return d.toISOString().slice(0, 16);
}

function handleEventDrop(dropInfo) {
    // Update event date on backend
    const event = dropInfo.event;
    
    router.put(route('calendar.update', event.id), {
        title: event.title, // Keep existing title
        start: event.start.toISOString(),
        end: event.end ? event.end.toISOString() : null,
        // We need to send other required fields if validation requires them?
        // Our controller checks for everything. So we should probably fetch full event or use a specific move endpoint.
        // But simpler: just use the standard update with all props from event object.
        description: event.extendedProps.description,
        url: event.extendedProps.url,
        is_public: event.extendedProps.is_public,
    }, {
        preserveScroll: true,
        onSuccess: () => {
           // Toast?
        },
        onError: () => {
            dropInfo.revert();
        }
    });
}

function handleEventResize(resizeInfo) {
    handleEventDrop(resizeInfo); // Same logic
}

function submit() {
    if (isEditing.value) {
        form.put(route('calendar.update', form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('calendar.store'), {
            onSuccess: () => closeModal(),
        });
    }
}

function deleteEvent() {
    if (!confirm(__('Are you sure you want to delete this event?'))) return;
    form.delete(route('calendar.destroy', form.id), {
        onSuccess: () => closeModal(),
    });
}

function closeModal() {
    showEventModal.value = false;
    form.reset();
}
</script>

<template>
    <Head title="Agenda" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                {{ __('Agenda') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-brand-surface overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <FullCalendar :options="calendarOptions" class="calendar-theme" />
                </div>
            </div>
        </div>

        <!-- Event Modal -->
        <Modal :show="showEventModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ isEditing ? __('Edit Event') : __('New Event') }}
                </h2>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="title" :value="__('Event Title')" />
                        <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" required autofocus placeholder="e.g. DJ Set at Club X" />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="start" :value="__('Start')" />
                            <TextInput id="start" v-model="form.start" type="datetime-local" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.start" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="end" :value="__('End')" />
                            <TextInput id="end" v-model="form.end" type="datetime-local" class="mt-1 block w-full" />
                            <InputError :message="form.errors.end" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="description" :value="__('Description')" />
                        <textarea id="description" v-model="form.description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="3"></textarea>
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="url" :value="__('Event / Ticket Link')" />
                        <TextInput id="url" v-model="form.url" type="url" class="mt-1 block w-full" placeholder="https://" />
                        <InputError :message="form.errors.url" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <Checkbox id="is_public" v-model:checked="form.is_public" />
                        <InputLabel for="is_public" :value="__('Visible on Public Profile')" class="ml-2" />
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                        <div class="flex items-center mb-2">
                             <Checkbox id="promote" v-model:checked="form.promote" />
                             <InputLabel for="promote" :value="__('Promote this date on Portal DJ Blog?')" class="ml-2 font-bold text-brand-primary" />
                        </div>
                        <p class="text-xs text-gray-500 mb-3 ml-6">{{ __('If selected, our team will review your press note and publish it on the blog if approved.') }}</p>
                        
                        <div v-if="form.promote" class="ml-6 space-y-2">
                             <InputLabel for="press_release" :value="__('Press Release / Note')" />
                             <textarea 
                                id="press_release" 
                                v-model="form.press_release" 
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                rows="4"
                                :placeholder="__('Describe your event for the blog post...')"
                            ></textarea>
                            <InputError :message="form.errors.press_release" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                         <DangerButton v-if="isEditing" type="button" @click="deleteEvent">
                            {{ __('Delete') }}
                        </DangerButton>
                        <SecondaryButton @click="closeModal">
                            {{ __('Cancel') }}
                        </SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ isEditing ? __('Update') : __('Create') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

<style>
/* Override FullCalendar variables for Dark Mode compatibility if needed */
.fc {
    --fc-page-bg-color: transparent;
    --fc-neutral-bg-color: rgba(255,255,255,0.1);
    --fc-list-event-hover-bg-color: rgba(255,255,255,0.1);
    --fc-theme-standard-border-color: #e5e7eb;
}
.dark .fc {
    --fc-theme-standard-border-color: #374151;
    --fc-page-bg-color: transparent;
    --fc-neutral-bg-color: #1f2937;
    --fc-button-text-color: #fff;
    --fc-button-bg-color: #4f46e5;
    --fc-button-border-color: #4f46e5;
    --fc-button-hover-bg-color: #4338ca;
    --fc-button-hover-border-color: #4338ca;
    --fc-button-active-bg-color: #3730a3;
    --fc-button-active-border-color: #3730a3;
    
    color: #e5e7eb;
}
.dark .fc-daygrid-day-number, .dark .fc-col-header-cell-cushion {
    color: #e5e7eb;
}
</style>
