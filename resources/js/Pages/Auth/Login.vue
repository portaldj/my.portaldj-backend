<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

import { object, string } from 'yup';

const loginSchema = object({
    email: string().required('Email is required').email('Must be a valid email'),
    password: string().required('Password is required'),
});

const submit = async () => {
    form.clearErrors();

    try {
        await loginSchema.validate(form.data(), { abortEarly: false });
        form.post(route('login'), {
            onFinish: () => form.reset('password'),
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
        <Head :title="__('Log in')" />

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <Link href="/" class="flex justify-center">
                 <ApplicationLogo class="h-20 w-20 fill-current text-brand-primary" />
            </Link>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ __('Te damos la bienvenida a tu perfil profesional DJ!') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                {{ __('Conecta, colabora y crece con la comunidad.') }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" :value="__('Email')" />
                        <div class="mt-1">
                            <TextInput
                                id="email"
                                type="email"
                                class="block w-full"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" :value="__('Password')" />
                        <div class="mt-1">
                            <TextInput
                                id="password"
                                type="password"
                                class="block w-full"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                        </label>

                        <div class="text-sm">
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="font-medium text-brand-primary hover:text-brand-secondary"
                            >
                                {{ __('Forgot your password?') }}
                            </Link>
                        </div>
                    </div>

                    <div class="text-xs text-center text-gray-500 dark:text-gray-400">
                         {{ __('Al hacer clic en «Login» para iniciar sesión, aceptas las Condiciones de uso, la Política de privacidad y la Política de cookies.') }}
                    </div>

                    <div>
                        <PrimaryButton
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-primary hover:bg-brand-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ __('Log in') }}
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
                                {{ __('¿No tienes cuenta?') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link
                            :href="route('register')"
                            class="w-full flex justify-center py-2 px-4 border border-brand-primary rounded-md shadow-sm text-sm font-medium text-brand-primary bg-transparent hover:bg-brand-primary/10 transition"
                        >
                            {{ __('Únete ahora') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600 dark:text-gray-400">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
                 {{ __('¿Por qué unirte a Portal DJ?') }}
            </h3>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-brand-primary text-white mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ __('Perfil Profesional') }}</h4>
                    <p class="mt-2 text-sm">{{ __('Muestra tu experiencia, equipo y disponibilidad.') }}</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-brand-primary text-white mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ __('Music Pool & Academy') }}</h4>
                    <p class="mt-2 text-sm">{{ __('Accede a música exclusiva y cursos avanzados.') }}</p>
                </div>
                 <div class="flex flex-col items-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-brand-primary text-white mb-4">
                         <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <!-- Helper / Assistant Icon -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ __('Asistente IA') }}</h4>
                    <p class="mt-2 text-sm">{{ __('Resuelve dudas técnicas sobre tu equipo al instante.') }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
