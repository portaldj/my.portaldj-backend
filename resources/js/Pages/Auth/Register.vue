<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import { computed } from 'vue';
import { object, string, number, ref as yupRef } from 'yup';

const props = defineProps({
    countries: Array,
    djTypes: Array,
});

const form = useForm({
    name: '',
    username: '',
    phone: '',
    dj_type_id: '',
    country_id: '',
    city_id: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const availableCities = computed(() => {
    if (!form.country_id) return [];
    const country = props.countries.find(c => c.id === form.country_id);
    return country ? country.cities : [];
});

const registerSchema = object({
    name: string().required('Name is required'),
    username: string().required('Username is required'),
    phone: string().required('Phone is required'),
    dj_type_id: number().required('DJ Type is required').typeError('DJ Type is required'),
    country_id: number().required('Country is required').typeError('Country is required'),
    city_id: number().required('City is required').typeError('City is required'),
    email: string().required('Email is required').email('Must be a valid email'),
    password: string().required('Password is required').min(8, 'Password must be at least 8 characters'),
    password_confirmation: string()
        .oneOf([yupRef('password'), null], 'Passwords must match')
        .required('Confirm Password is required'),
});

const submit = async () => {
    form.clearErrors();

    try {
        await registerSchema.validate(form.data(), { abortEarly: false });
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
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
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <Head :title="__('Register')" />

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <Link href="/" class="flex justify-center">
                 <ApplicationLogo class="h-20 w-20 fill-current text-brand-primary" />
            </Link>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ __('Saca el máximo partido a tu vida profesional como DJ.') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                {{ __('Únete a la mejor comunidad de DJs de Latinoamérica.') }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <InputLabel for="name" :value="__('Full Name')" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <!-- Username -->
                    <div>
                        <InputLabel for="username" :value="__('Username (DJ Name)')" />
                        <TextInput
                            id="username"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.username"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <InputLabel for="phone" :value="__('Phone')" />
                        <TextInput
                            id="phone"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.phone"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <!-- DJ Type -->
                    <div>
                        <InputLabel for="dj_type_id" :value="__('DJ Type')" />
                        <select
                            id="dj_type_id"
                            v-model="form.dj_type_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            required
                        >
                            <option value="" disabled>{{ __('Select Type') }}</option>
                            <option v-for="type in djTypes" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.dj_type_id" />
                    </div>

                    <!-- Country & City -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="country_id" :value="__('Country')" />
                            <select
                                id="country_id"
                                v-model="form.country_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                required
                            >
                                <option value="" disabled>{{ __('Select Country') }}</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">
                                    {{ country.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.country_id" />
                        </div>
                        <div>
                            <InputLabel for="city_id" :value="__('City')" />
                            <select
                                id="city_id"
                                v-model="form.city_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                required
                                :disabled="!form.country_id"
                            >
                                <option value="" disabled>{{ __('Select City') }}</option>
                                <option 
                                    v-for="city in availableCities" 
                                    :key="city.id" 
                                    :value="city.id"
                                >
                                    {{ city.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.city_id" />
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" :value="__('Email')" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Password -->
                    <div>
                        <InputLabel for="password" :value="__('Password')" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <InputLabel for="password_confirmation" :value="__('Confirm Password')" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="text-xs text-center text-gray-500 dark:text-gray-400 mt-4">
                         {{ __('Al hacer clic en «Registrarse», aceptas las Condiciones de uso, la Política de privacidad y la Política de cookies de Portal DJ.') }}
                    </div>

                    <div class="mt-4">
                        <PrimaryButton
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-primary hover:bg-brand-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ __('Register') }}
                        </PrimaryButton>
                    </div>
                </form>

                <div class="mt-6">
                     <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">
                                {{ __('¿Ya eres usuario?') }}
                            </span>
                        </div>
                    </div>
                
                    <div class="mt-6">
                        <Link
                            :href="route('login')"
                            class="w-full flex justify-center py-2 px-4 border border-brand-primary rounded-md shadow-sm text-sm font-medium text-brand-primary bg-transparent hover:bg-brand-primary/10 transition"
                        >
                            {{ __('Iniciar sesión') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600 dark:text-gray-400">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
                 {{ __('Beneficios de unirse a Portal DJ') }}
            </h3>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-brand-primary text-white mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ __('Perfil Profesional') }}</h4>
                    <p class="mt-2 text-sm">{{ __('Crea tu identidad digital y conecta con otros profesionales.') }}</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-brand-primary text-white mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ __('Music Pool') }}</h4>
                    <p class="mt-2 text-sm">{{ __('Descarga versiones exclusivas y mantente actualizado.') }}</p>
                </div>
                 <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-brand-primary text-white mb-4">
                         <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ __('Asistente IA') }}</h4>
                    <p class="mt-2 text-sm">{{ __('Obtén soporte técnico inteligente para tu equipo DJ.') }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
