<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    countries: Array,
    cities: Array,
    clubs: Array,
    djTypes: Array,
});

const activeTab = ref('countries');

// Country Form
const countryForm = useForm({ name: '', code: '' });
const submitCountry = () => {
    countryForm.post(route('admin.countries.store'), {
        onSuccess: () => countryForm.reset(),
    });
};
const toggleCountry = (country) => {
    router.post(route('admin.countries.toggle', country.id), {}, { preserveScroll: true });
};
const deleteCountry = (id) => {
    if (confirm('Delete this country?')) router.delete(route('admin.countries.destroy', id), { preserveScroll: true });
};

// City Form
const cityForm = useForm({ country_id: '', name: '' });
const submitCity = () => {
    cityForm.post(route('admin.cities.store'), {
        onSuccess: () => cityForm.reset(),
    });
};
const toggleCity = (city) => {
    router.post(route('admin.cities.toggle', city.id), {}, { preserveScroll: true });
};
const deleteCity = (id) => {
    if (confirm('Delete this city?')) router.delete(route('admin.cities.destroy', id), { preserveScroll: true });
};

// Club Form
const clubForm = useForm({ city_id: '', name: '', type: 'club', address: '' });
const submitClub = () => {
    clubForm.post(route('admin.clubs.store'), {
        onSuccess: () => clubForm.reset(),
    });
};
const deleteClub = (id) => {
    if (confirm('Delete this club?')) router.delete(route('admin.clubs.destroy', id), { preserveScroll: true });
};

// DJ Type Form
const djTypeForm = useForm({ name: '' });
const submitDjType = () => {
    djTypeForm.post(route('admin.dj-types.store'), {
        onSuccess: () => djTypeForm.reset(),
    });
};
const deleteDjType = (id) => {
    if (confirm('Delete this DJ Type?')) router.delete(route('admin.dj-types.destroy', id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Admin - Taxonomy Manager" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Taxonomy Manager
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Tabs -->
                <div class="flex border-b border-gray-700 mb-6">
                    <button @click="activeTab = 'countries'" :class="{'border-b-2 border-brand-primary text-brand-primary': activeTab === 'countries', 'text-gray-400 hover:text-white': activeTab !== 'countries'}" class="px-6 py-3 font-medium transition">Countries</button>
                    <button @click="activeTab = 'cities'" :class="{'border-b-2 border-brand-primary text-brand-primary': activeTab === 'cities', 'text-gray-400 hover:text-white': activeTab !== 'cities'}" class="px-6 py-3 font-medium transition">Cities</button>
                    <button @click="activeTab = 'clubs'" :class="{'border-b-2 border-brand-primary text-brand-primary': activeTab === 'clubs', 'text-gray-400 hover:text-white': activeTab !== 'clubs'}" class="px-6 py-3 font-medium transition">Clubs</button>
                    <button @click="activeTab = 'djtypes'" :class="{'border-b-2 border-brand-primary text-brand-primary': activeTab === 'djtypes', 'text-gray-400 hover:text-white': activeTab !== 'djtypes'}" class="px-6 py-3 font-medium transition">DJ Types</button>
                </div>

                <!-- Countries Tab -->
                <div v-if="activeTab === 'countries'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Form -->
                    <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700 h-fit">
                        <h3 class="text-lg font-bold text-white mb-4">Add Country</h3>
                        <form @submit.prevent="submitCountry" class="space-y-4">
                            <div>
                                <InputLabel for="c_name" value="Name" />
                                <TextInput id="c_name" v-model="countryForm.name" class="w-full mt-1" required />
                                <InputError :message="countryForm.errors.name" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="c_code" value="Code (ISO)" />
                                <TextInput id="c_code" v-model="countryForm.code" class="w-full mt-1" required placeholder="US" />
                                <InputError :message="countryForm.errors.code" class="mt-2" />
                            </div>
                            <PrimaryButton :disabled="countryForm.processing">Add Country</PrimaryButton>
                        </form>
                    </div>
                    <!-- List -->
                    <div class="md:col-span-2 bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
                        <h3 class="text-lg font-bold text-white mb-4">Countries List</h3>
                        <ul class="space-y-2 max-h-96 overflow-y-auto">
                            <li v-for="country in countries" :key="country.id" class="flex justify-between items-center bg-gray-700/50 p-3 rounded group">
                                <span class="text-gray-200" :class="{ 'opacity-50 line-through': !country.is_active }">
                                    {{ country.name }} ({{ country.code }}) 
                                    <span v-if="!country.is_active" class="ml-2 text-xs text-red-400 font-bold no-underline inline-block">(Inactive)</span>
                                </span>
                                <div class="flex items-center space-x-3">
                                    <button @click="toggleCountry(country)" class="text-xs font-semibold hover:underline" :class="country.is_active ? 'text-yellow-400' : 'text-green-400'">
                                        {{ country.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button @click="deleteCountry(country.id)" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Cities Tab -->
                <div v-if="activeTab === 'cities'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700 h-fit">
                         <h3 class="text-lg font-bold text-white mb-4">Add City</h3>
                        <form @submit.prevent="submitCity" class="space-y-4">
                            <div>
                                <InputLabel for="ci_country" value="Country" />
                                <select id="ci_country" v-model="cityForm.country_id" class="w-full mt-1 border-gray-700 bg-gray-900 text-gray-300 rounded-md" required>
                                    <option value="" disabled>Select Country</option>
                                    <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                                <InputError :message="cityForm.errors.country_id" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="ci_name" value="Name" />
                                <TextInput id="ci_name" v-model="cityForm.name" class="w-full mt-1" required />
                                <InputError :message="cityForm.errors.name" class="mt-2" />
                            </div>
                            <PrimaryButton :disabled="cityForm.processing">Add City</PrimaryButton>
                        </form>
                    </div>
                    <div class="md:col-span-2 bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
                         <h3 class="text-lg font-bold text-white mb-4">Cities List</h3>
                         <ul class="space-y-2 max-h-96 overflow-y-auto">
                            <li v-for="city in cities" :key="city.id" class="flex justify-between items-center bg-gray-700/50 p-3 rounded">
                                <span class="text-gray-200" :class="{ 'opacity-50 line-through': !city.is_active }">
                                    {{ city.name }}, {{ city.country?.name }}
                                     <span v-if="!city.is_active" class="ml-2 text-xs text-red-400 font-bold no-underline inline-block">(Inactive)</span>
                                </span>
                                <div class="flex items-center space-x-3">
                                    <button @click="toggleCity(city)" class="text-xs font-semibold hover:underline" :class="city.is_active ? 'text-yellow-400' : 'text-green-400'">
                                        {{ city.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button @click="deleteCity(city.id)" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Clubs Tab -->
                <div v-if="activeTab === 'clubs'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                     <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700 h-fit">
                         <h3 class="text-lg font-bold text-white mb-4">Add Club / Venue</h3>
                        <form @submit.prevent="submitClub" class="space-y-4">
                            <div>
                                <InputLabel for="cl_city" value="City" />
                                <select id="cl_city" v-model="clubForm.city_id" class="w-full mt-1 border-gray-700 bg-gray-900 text-gray-300 rounded-md" required>
                                    <option value="" disabled>Select City</option>
                                    <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }} ({{ c.country?.code }})</option>
                                </select>
                                <InputError :message="clubForm.errors.city_id" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="cl_name" value="Name" />
                                <TextInput id="cl_name" v-model="clubForm.name" class="w-full mt-1" required />
                                <InputError :message="clubForm.errors.name" class="mt-2" />
                            </div>
                             <div>
                                <InputLabel for="cl_address" value="Address (Optional)" />
                                <TextInput id="cl_address" v-model="clubForm.address" class="w-full mt-1" />
                                <InputError :message="clubForm.errors.address" class="mt-2" />
                            </div>
                             <div>
                                <InputLabel for="cl_type" value="Type" />
                                <select id="cl_type" v-model="clubForm.type" class="w-full mt-1 border-gray-700 bg-gray-900 text-gray-300 rounded-md" required>
                                    <option value="club">Club</option>
                                    <option value="event_center">Event Center</option>
                                </select>
                            </div>
                            <PrimaryButton :disabled="clubForm.processing">Add Club</PrimaryButton>
                        </form>
                    </div>
                    <div class="md:col-span-2 bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
                         <h3 class="text-lg font-bold text-white mb-4">Clubs List</h3>
                         <ul class="space-y-2 max-h-96 overflow-y-auto">
                            <li v-for="club in clubs" :key="club.id" class="flex justify-between items-center bg-gray-700/50 p-3 rounded">
                                <div>
                                    <span class="text-gray-200 font-bold">{{ club.name }}</span>
                                    <span class="text-gray-500 text-sm ml-2">({{ club.city?.name }}) - {{ club.type }}</span>
                                </div>
                                <button @click="deleteClub(club.id)" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- DJ Types Tab -->
                <div v-if="activeTab === 'djtypes'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700 h-fit">
                        <h3 class="text-lg font-bold text-white mb-4">Add DJ Type</h3>
                        <form @submit.prevent="submitDjType" class="space-y-4">
                            <div>
                                <InputLabel for="dt_name" value="Name" />
                                <TextInput id="dt_name" v-model="djTypeForm.name" class="w-full mt-1" required />
                                <InputError :message="djTypeForm.errors.name" class="mt-2" />
                            </div>
                            <PrimaryButton :disabled="djTypeForm.processing">Add Type</PrimaryButton>
                        </form>
                    </div>
                   <div class="md:col-span-2 bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
                        <h3 class="text-lg font-bold text-white mb-4">DJ Types List</h3>
                        <ul class="space-y-2 max-h-96 overflow-y-auto">
                            <li v-for="type in djTypes" :key="type.id" class="flex justify-between items-center bg-gray-700/50 p-3 rounded">
                                <span class="text-gray-200">{{ type.name }}</span>
                                <button @click="deleteDjType(type.id)" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
