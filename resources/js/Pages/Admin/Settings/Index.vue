<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
});

// Map settings to form
const form = useForm({
    settings: {
        module_pool: props.settings.module_pool ?? '1',
        module_academy: props.settings.module_academy ?? '1',
        module_agenda: props.settings.module_agenda ?? '1',
        module_assistant: props.settings.module_assistant ?? '1',
        
        // PRO Flags
        pool_is_pro: props.settings.pool_is_pro ?? '0',
        academy_is_pro: props.settings.academy_is_pro ?? '0',
        assistant_is_pro: props.settings.assistant_is_pro ?? '0',
    }
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Toast handled by layout
        }
    });
};

const modules = [
    { 
        key: 'module_pool', 
        label: 'Music Pool', 
        description: 'Enable access to the Music Pool and downloading features.',
        proKey: 'pool_is_pro',
        proLabel: 'Require PRO Subscription',
        hasPro: true 
    },
    { 
        key: 'module_academy', 
        label: 'Academy', 
        description: 'Enable access to Courses, Exams, and Learning materials.',
        proKey: 'academy_is_pro',
        proLabel: 'Require PRO Subscription',
        hasPro: true
    },
    { 
        key: 'module_agenda', 
        label: 'Agenda / Calendar', 
        description: 'Enable the Event Calendar and Booking features.',
        hasPro: false 
    },
    { 
        key: 'module_assistant', 
        label: 'AI Assistant', 
        description: 'Enable the AI Chat and Equipment Assistant. (PRO Restriction applies to usage)',
        proKey: 'assistant_is_pro',
        proLabel: 'Require PRO for Access',
        hasPro: true 
    },
];
</script>

<template>
    <Head title="Global Settings" />

    <AdminLayout>
        <template #header>
            Global Settings
        </template>

        <div class="space-y-6">
            <!-- Module Access Control -->
            <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
                <h3 class="text-lg font-medium text-white mb-4">Module Access Control</h3>
                <p class="text-gray-400 mb-6 text-sm">
                    Toggle visibility and access to major application modules. Disabling a module will hide it from the navigation and block access to its routes for all users (except Admins).
                </p>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div v-for="mod in modules" :key="mod.key" class="bg-gray-700/50 p-4 rounded-lg border border-gray-700">
                            <!-- Main Module Toggle -->
                            <div class="flex items-start justify-between">
                                <div class="flex-1 mr-4">
                                    <label :for="mod.key" class="text-base font-medium text-gray-200 block mb-1">
                                        {{ mod.label }}
                                    </label>
                                    <p class="text-sm text-gray-400">{{ mod.description }}</p>
                                </div>
                                
                                <!-- Toggle Switch -->
                                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <input 
                                        type="checkbox" 
                                        :name="mod.key" 
                                        :id="mod.key" 
                                        v-model="form.settings[mod.key]"
                                        true-value="1"
                                        false-value="0"
                                        class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-transform duration-200 ease-in-out transform checked:translate-x-full checked:border-brand-primary"
                                    />
                                    <label :for="mod.key" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-600 cursor-pointer transition-colors duration-200 ease-in-out" :class="{'bg-brand-primary/50': form.settings[mod.key] == '1'}"></label>
                                </div>
                            </div>

                            <!-- PRO Toggle (Nested) -->
                            <div v-if="mod.hasPro && form.settings[mod.key] == '1'" class="mt-4 pt-4 border-t border-gray-600 pl-4 flex items-center justify-between">
                                 <div>
                                    <label :for="mod.proKey" class="text-sm font-medium text-yellow-500 block mb-1">
                                        {{ mod.proLabel }}
                                    </label>
                                    <p class="text-xs text-gray-500">Only accessible to PRO users.</p>
                                </div>
                                
                                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <input 
                                        type="checkbox" 
                                        :name="mod.proKey" 
                                        :id="mod.proKey" 
                                        v-model="form.settings[mod.proKey]"
                                        true-value="1"
                                        false-value="0"
                                        class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-transform duration-200 ease-in-out transform checked:translate-x-full checked:border-yellow-500"
                                    />
                                    <label :for="mod.proKey" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-600 cursor-pointer transition-colors duration-200 ease-in-out" :class="{'bg-yellow-500/50': form.settings[mod.proKey] == '1'}"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-700">
                         <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <p v-if="form.recentlySuccessful" class="text-sm text-green-400 mr-4 self-center">Saved.</p>
                        </transition>

                        <PrimaryButton :disabled="form.processing">
                            Save Changes
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Custom Toggle Styles if not using a library */
.toggle-checkbox:checked {
    right: 0;
    border-color: #6875F5;
}
.toggle-checkbox:checked + .toggle-label {
    background-color: #6875F5;
}

/* Tailwind-friendly toggle override */
input:checked ~ .toggle-label {
    @apply bg-brand-primary;
}
</style>
