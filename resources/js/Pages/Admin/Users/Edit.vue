<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    roles: props.user.roles.map(r => r.name),
    username: props.user.profile?.username || '',
    banned_until: props.user.banned_until ? props.user.banned_until.substring(0, 16) : '', // Format for datetime-local
    ban_reason: props.user.ban_reason || '',
});

const setBan = (days) => {
    const date = new Date();
    date.setDate(date.getDate() + days);
    // Format to YYYY-MM-DDTHH:mm for datetime-local check
    const offset = date.getTimezoneOffset() * 60000;
    const localISOTime = (new Date(date - offset)).toISOString().slice(0, 16);
    form.banned_until = localISOTime;
};

const clearBan = () => {
    form.banned_until = '';
    form.ban_reason = '';
};

import { object, string } from 'yup';

const editUserSchema = object({
    username: string().required('Username is required'),
});

const submit = async () => {
    form.clearErrors();

    try {
        await editUserSchema.validate(form.data(), { abortEarly: false });
        form.put(route('admin.users.update', props.user.id));
    } catch (err) {
        if (err.inner) {
            err.inner.forEach((validationError) => {
                form.setError(validationError.path, validationError.message);
            });
        }
    }
};
</script>

<template>
    <Head :title="`Edit ${user.name}`" />

    <AdminLayout>
        <template #header>
            Edit User: {{ user.name }}
        </template>

        <div class="max-w-2xl mx-auto bg-gray-800 p-8 rounded-lg shadow border border-gray-700">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Username (Admin Only) -->
                <div>
                     <label class="block text-sm font-medium text-gray-400">Username (DJ Name)</label>
                     <input 
                        type="text" 
                        v-model="form.username" 
                        class="mt-1 block w-full rounded bg-gray-900 border-gray-700 text-white shadow-sm focus:border-brand-primary focus:ring-brand-primary sm:text-sm"
                    />
                    <div v-if="form.errors.username" class="text-red-500 text-xs mt-1">{{ form.errors.username }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400">Name</label>
                    <div class="mt-1 p-2 bg-gray-900 border border-gray-700 rounded text-gray-300">{{ user.name }}</div>
                </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-400">Email</label>
                    <div class="mt-1 p-2 bg-gray-900 border border-gray-700 rounded text-gray-300">{{ user.email }}</div>
                </div>

                <!-- Roles -->
                <div>
                    <label class="block text-sm font-bold text-white mb-2">Assign Roles</label>
                    <div class="space-y-2">
                        <div v-for="role in roles" :key="role.id" class="flex items-center">
                            <input 
                                type="checkbox" 
                                :value="role.name" 
                                v-model="form.roles"
                                class="rounded bg-gray-900 border-gray-700 text-brand-primary shadow-sm focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-50"
                            />
                            <label class="ml-2 text-gray-300">{{ role.name }}</label>
                        </div>
                    </div>
                </div>

                <!-- Ban Management -->
                <div class="pt-6 border-t border-gray-700">
                    <h3 class="text-lg font-bold text-red-500 mb-4">Account Suspension</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                             <label class="block text-sm font-medium text-gray-400">Ban Until</label>
                             <input 
                                type="datetime-local" 
                                v-model="form.banned_until" 
                                class="mt-1 block w-full rounded bg-gray-900 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                            />
                             <div class="flex gap-2 mt-2 text-xs">
                                <button type="button" @click="setBan(1)" class="text-brand-accent hover:underline">1 Day</button>
                                <button type="button" @click="setBan(7)" class="text-brand-accent hover:underline">7 Days</button>
                                <button type="button" @click="setBan(36500)" class="text-brand-accent hover:underline">Permanent</button>
                                <button type="button" @click="clearBan" class="text-gray-500 hover:text-white hover:underline ml-auto">Lift Ban</button>
                            </div>
                        </div>
                         <div>
                             <label class="block text-sm font-medium text-gray-400">Ban Reason</label>
                             <input 
                                type="text" 
                                v-model="form.ban_reason" 
                                placeholder="Violation of terms..."
                                class="mt-1 block w-full rounded bg-gray-900 border-gray-700 text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                            />
                        </div>
                    </div>
                    <p v-if="form.banned_until" class="mt-2 text-sm text-red-400"> 
                        User is banned until {{ new Date(form.banned_until).toLocaleString() }}
                    </p>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-700">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white font-bold rounded shadow hover:opacity-90 transition disabled:opacity-50"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
