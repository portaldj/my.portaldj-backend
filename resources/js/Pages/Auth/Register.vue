<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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

import { computed } from 'vue';

const availableCities = computed(() => {
    if (!form.country_id) return [];
    const country = props.countries.find(c => c.id === form.country_id);
    return country ? country.cities : [];
});

import { object, string, number, ref as yupRef } from 'yup';

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
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
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
            <div class="mt-4">
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
            <div class="mt-4">
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
            <div class="mt-4">
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
            <div class="mt-4 grid grid-cols-2 gap-4">
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
            <div class="mt-4">
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
            <div class="mt-4">
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
            <div class="mt-4">
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

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    {{ __('Already registered?') }}
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ __('Register') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
