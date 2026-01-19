<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    roles: Array,
});

const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
    roles: ['User'], // Default role
});

import { object, string, ref as yupRef } from 'yup';

const createUserSchema = object({
    name: string().required('Name is required'),
    email: string().required('Email is required').email('Must be a valid email'),
    username: string().required('Username is required'),
    password: string().required('Password is required').min(8, 'Password must be at least 8 characters'),
    password_confirmation: string()
        .oneOf([yupRef('password'), null], 'Passwords must match')
        .required('Confirm Password is required'),
});

const submit = async () => {
    form.clearErrors();

    try {
        await createUserSchema.validate(form.data(), { abortEarly: false });
        form.post(route('admin.users.store'));
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
    <Head title="Create User" />

    <AdminLayout>
        <template #header>
            Create New User
        </template>

        <div class="max-w-2xl mx-auto bg-gray-800 p-8 rounded-lg shadow border border-gray-700">
            <form @submit.prevent="submit" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-400">Name</label>
                    <input 
                        type="text" 
                        v-model="form.name" 
                        required
                        class="mt-1 block w-full rounded bg-gray-900 border-gray-700 text-white shadow-sm focus:border-brand-primary focus:ring-brand-primary sm:text-sm"
                    />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400">Email</label>
                    <input 
                        type="email" 
                        v-model="form.email" 
                        required
                        class="mt-1 block w-full rounded bg-gray-900 border-gray-700 text-white shadow-sm focus:border-brand-primary focus:ring-brand-primary sm:text-sm"
                    />
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400">Username (DJ Name)</label>
                    <input 
                        type="text" 
                        v-model="form.username" 
                        required
                        class="mt-1 block w-full rounded bg-gray-900 border-gray-700 text-white shadow-sm focus:border-brand-primary focus:ring-brand-primary sm:text-sm"
                    />
                    <div v-if="form.errors.username" class="text-red-500 text-xs mt-1">{{ form.errors.username }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Password</label>
                        <input 
                            type="password" 
                            v-model="form.password" 
                            required
                            class="mt-1 block w-full rounded bg-gray-900 border-gray-700 text-white shadow-sm focus:border-brand-primary focus:ring-brand-primary sm:text-sm"
                        />
                        <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Confirm Password</label>
                        <input 
                            type="password" 
                            v-model="form.password_confirmation" 
                            required
                            class="mt-1 block w-full rounded bg-gray-900 border-gray-700 text-white shadow-sm focus:border-brand-primary focus:ring-brand-primary sm:text-sm"
                        />
                    </div>
                </div>

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

                <div class="flex justify-end gap-4 pt-6 border-t border-gray-700">
                    <Link :href="route('admin.users.index')" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600 transition">Cancel</Link>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white font-bold rounded shadow hover:opacity-90 transition disabled:opacity-50"
                    >
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
