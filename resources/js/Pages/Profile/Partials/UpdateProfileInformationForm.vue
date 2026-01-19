<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';


const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    countries: Array,
    djTypes: Array,
    socialPlatforms: Array,
    clubs: Array, // Passed from controller
    user: Object,
});

const user = props.user || usePage().props.auth.user;

import AutocompleteInput from '@/Components/AutocompleteInput.vue';

const form = useForm({
    name: user.name,
    email: user.email,
    username: user.profile?.username || '',
    phone: user.profile?.phone || '',
    biography: user.profile?.biography || '',
    country_id: user.profile?.country_id || '',
    city_id: user.profile?.city_id || '',
    dj_type_id: user.profile?.dj_type_id || '',
    profile_image: null,
    social_networks: user.social_networks ? user.social_networks.map(n => ({
        social_platform_id: n.social_platform_id,
        url: n.url,
    })) : [],
    experiences: user.experiences ? user.experiences.map(e => ({
        title: e.title,
        place: e.place,
        description: e.description || '',
        start_date: e.start_date ? e.start_date.substring(0, 10) : '',
        end_date: e.end_date ? e.end_date.substring(0, 10) : '',
        current: !e.end_date
    })) : [],
});

const addNetwork = () => {
    form.social_networks.push({ social_platform_id: '', url: '' });
};

const removeNetwork = (index) => {
    form.social_networks.splice(index, 1);
};

const moveUp = (index) => {
    if (index > 0) {
        const item = form.social_networks[index];
        form.social_networks.splice(index, 1);
        form.social_networks.splice(index - 1, 0, item);
    }
};

const moveDown = (index) => {
    if (index < form.social_networks.length - 1) {
        const item = form.social_networks[index];
        form.social_networks.splice(index, 1);
        form.social_networks.splice(index + 1, 0, item);
    }
};

const addExperience = () => {
    form.experiences.push({ 
        title: '', 
        place: '', 
        description: '', 
        start_date: '', 
        end_date: '', 
        current: false 
    });
};

const removeExperience = (index) => {
    form.experiences.splice(index, 1);
};

// Sort experiences by start_date descending automatically usually, but manual reordering could be nice.
// For now, let's just stick to add/remove.

import { computed } from 'vue';
import { object, string, array, number, boolean } from 'yup';

const availableCities = computed(() => {
    if (!form.country_id) return [];
    // Ensure ID comparison works (form value might be string/number)
    const country = props.countries.find(c => c.id == form.country_id);
    return country ? country.cities : [];
});

const profileSchema = object({
    name: string().required('Full Name is required'),
    email: string().required('Email is required').email('Must be a valid email address'),
    username: string().required('Username is required'),
    phone: string().nullable(),
    biography: string().nullable(),
    social_networks: array().of(
        object({
            social_platform_id: number().required('Platform is required').typeError('Platform is required'),
            url: string().required('URL or Username is required'),
        })
    ),
    experiences: array().of(
        object({
            title: string().required('Title is required'),
            place: string().required('Place/Company is required'),
            description: string().nullable(),
            start_date: string().required('Start Date is required'),
            current: boolean(),
            end_date: string().nullable().when('current', {
                is: false,
                then: (schema) => schema.required('End Date is required').test('is-after', 'End Date must be after Start Date', function(value) {
                    const { start_date } = this.parent;
                    return !start_date || !value || new Date(value) >= new Date(start_date);
                }),
                otherwise: (schema) => schema.nullable()
            })
        })
    ),
});

const submit = async () => {
    form.clearErrors();

    try {
        // Validate form data
        await profileSchema.validate(form.data(), { abortEarly: false });

        // Transform data before submit (specifically handling optional end_date based on 'current')
        form.transform((data) => ({
            ...data,
            experiences: data.experiences.map(e => ({
                ...e,
                end_date: e.current ? null : e.end_date
            })),
            _method: 'PATCH',
        })).post(route('profile.update'), {
            forceFormData: true,
        });

    } catch (err) {
        // Map Yup errors to Inertia form errors
        if (err.inner) {
            err.inner.forEach((validationError) => {
                // Ensure array paths are compatible with Inertia 
                // social_networks[0].url -> social_networks.0.url
                // experiences[0].title -> experiences.0.title
                const path = validationError.path.replace(/\[(\d+)\]/g, '.$1');
                form.setError(path, validationError.message);
            });
        }
    }
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="submit"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="Full Name" />
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

            <div>
                <InputLabel for="profile_image" value="Profile Photo" />
                <input 
                    type="file" 
                    @input="form.profile_image = $event.target.files[0]"
                    class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-primary file:text-white hover:file:bg-violet-600"
                />
                <InputError class="mt-2" :message="form.errors.profile_image" />
            </div>

            <div>
                <InputLabel for="username" value="Username (DJ Name)" />
                <TextInput
                    id="username"
                    type="text"
                    v-model="form.username"
                    required
                    disabled
                    class="mt-1 block w-full bg-gray-100 cursor-not-allowed text-gray-500"
                />
                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                     <InputLabel for="phone" value="Phone" />
                    <TextInput
                        id="phone"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.phone"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
                <div>
                    <InputLabel for="dj_type_id" value="DJ Type" />
                    <select
                        id="dj_type_id"
                        v-model="form.dj_type_id"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    >
                        <option value="" disabled>Select Type</option>
                        <option v-for="type in djTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="country_id" value="Country" />
                    <select
                        id="country_id"
                        v-model="form.country_id"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    >
                        <option value="" disabled>Select Country</option>
                        <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                    </select>
                </div>
                <div>
                    <InputLabel for="city_id" value="City" />
                    <select
                        id="city_id"
                        v-model="form.city_id"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        :disabled="!form.country_id"
                    >
                        <option value="" disabled>Select City</option>
                        <option v-for="city in availableCities" :key="city.id" :value="city.id">{{ city.name }}</option>
                    </select>
                </div>
            </div>

            <div>
                 <InputLabel for="biography" value="Biography" />
                <textarea
                    id="biography"
                    v-model="form.biography"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    rows="4"
                ></textarea>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <!-- Social Networks Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Social Networks</h3>
                
                <div class="space-y-4">
                    <div 
                        v-for="(network, index) in form.social_networks" 
                        :key="index"
                        class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700"
                    >
                        <!-- Drag/Move Handles -->
                        <div class="flex flex-col gap-1 text-gray-400">
                             <button type="button" @click="moveUp(index)" :disabled="index === 0" class="hover:text-brand-primary disabled:opacity-30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                             </button>
                             <button type="button" @click="moveDown(index)" :disabled="index === form.social_networks.length - 1" class="hover:text-brand-primary disabled:opacity-30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                             </button>
                        </div>

                        <!-- Platform Select -->
                        <div class="w-1/3">
                            <select 
                                v-model="network.social_platform_id" 
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm"
                                required
                            >
                                <option value="" disabled>Select Platform</option>
                                <option v-for="platform in socialPlatforms" :key="platform.id" :value="platform.id">
                                    {{ platform.name }}
                                </option>
                            </select>
                        </div>

                        <!-- URL Input -->
                        <div class="flex-1">
                            <TextInput
                                type="text"
                                class="w-full text-sm"
                                v-model="network.url"
                                placeholder="URL or Username"
                                required
                            />
                        </div>

                        <!-- Remove Button -->
                        <button type="button" @click="removeNetwork(index)" class="text-red-500 hover:text-red-700 p-2">
                             <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>

                    <div v-if="form.social_networks.length === 0" class="text-sm text-gray-500 italic text-center py-4">
                        No social networks added.
                    </div>

                    <div class="flex justify-end">
                        <button 
                            type="button" 
                            @click="addNetwork"
                            class="flex items-center gap-2 px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-md text-sm hover:bg-gray-700 dark:hover:bg-gray-600 transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add Social Link
                        </button>
                    </div>
                </div>
            </div>

            <!-- Experiences Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Professional Experience</h3>
                
                <div class="space-y-6">
                    <div 
                        v-for="(experience, index) in form.experiences" 
                        :key="index"
                        class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 relative"
                    >
                        <button type="button" @click="removeExperience(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1">
                             <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel value="Job Title / Role" />
                                <TextInput
                                    type="text"
                                    class="mt-1 block w-full text-sm"
                                    v-model="experience.title"
                                    placeholder="e.g. Resident DJ"
                                    required
                                />
                                <div v-if="form.errors[`experiences.${index}.title`]" class="text-red-500 text-xs mt-1">{{ form.errors[`experiences.${index}.title`] }}</div>
                            </div>

                            <div>
                                <InputLabel value="Place / Company" />
                                <AutocompleteInput
                                    v-model="experience.place"
                                    :items="clubs"
                                    placeholder="e.g. Club Amnesia"
                                />
                                <div v-if="form.errors[`experiences.${index}.place`]" class="text-red-500 text-xs mt-1">{{ form.errors[`experiences.${index}.place`] }}</div>
                            </div>

                            <div>
                                <InputLabel value="Start Date" />
                                <TextInput
                                    type="date"
                                    class="mt-1 block w-full text-sm"
                                    v-model="experience.start_date"
                                    required
                                />
                                <div v-if="form.errors[`experiences.${index}.start_date`]" class="text-red-500 text-xs mt-1">{{ form.errors[`experiences.${index}.start_date`] }}</div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center">
                                    <InputLabel value="End Date" />
                                    <label class="flex items-center text-xs text-gray-500 cursor-pointer">
                                        <input type="checkbox" v-model="experience.current" class="mr-1 rounded border-gray-300 text-brand-primary focus:ring-brand-primary">
                                        Currently working here
                                    </label>
                                </div>
                                <TextInput
                                    type="date"
                                    class="mt-1 block w-full text-sm disabled:opacity-50 disabled:bg-gray-200 dark:disabled:bg-gray-700"
                                    v-model="experience.end_date"
                                    :disabled="experience.current"
                                    :required="!experience.current"
                                />
                                <div v-if="form.errors[`experiences.${index}.end_date`]" class="text-red-500 text-xs mt-1">{{ form.errors[`experiences.${index}.end_date`] }}</div>
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel value="Description" />
                                <textarea
                                    v-model="experience.description"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm"
                                    rows="3"
                                    placeholder="Describe your responsibilities..."
                                ></textarea>
                                <div v-if="form.errors[`experiences.${index}.description`]" class="text-red-500 text-xs mt-1">{{ form.errors[`experiences.${index}.description`] }}</div>
                            </div>
                        </div>
                    </div>

                    <div v-if="form.experiences.length === 0" class="text-sm text-gray-500 italic text-center py-4">
                        No experiences added yet.
                    </div>

                    <div class="flex justify-start">
                        <button 
                            type="button" 
                            @click="addExperience"
                            class="flex items-center gap-2 px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-md text-sm hover:bg-gray-700 dark:hover:bg-gray-600 transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add Experience
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <!-- ... saved message ... -->
            </div>
        </form>
    </section>
</template>
