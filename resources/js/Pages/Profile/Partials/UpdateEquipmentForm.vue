<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import EquipmentAutocomplete from '@/Components/EquipmentAutocomplete.vue';

const props = defineProps({
    brands: Array,
    equipmentTypes: Array,
    equipment: Array,
});

const form = useForm({
    brand_id: '',
    equipment_type_id: '',
    equipment_model_id: '',
    model_name: '',
    is_public: true,
});

const adding = ref(false);

const onModelSelect = (item) => {
    if (item) {
        form.equipment_model_id = item.id;
        form.model_name = item.name;
    } else {
        form.equipment_model_id = '';
        // model_name is updated via v-model
    }
};

const addEquipment = () => {
    form.post(route('dj-equipment.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            adding.value = false;
        },
    });
};

const toggleVisibility = (item) => {
    // We send the inverted value
    const form = useForm({
        is_public: !item.is_public
    });
    form.put(route('dj-equipment.update', item.id), {
        preserveScroll: true,
    });
};

const deleteItem = (item) => {
    if (!confirm('Remove this item?')) return;
    
    const form = useForm({});
    form.delete(route('dj-equipment.destroy', item.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('My Equipment') }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Share your setup with the world or keep track of your gear.') }}
            </p>
        </header>

        <!-- List -->
        <div class="mt-6 space-y-4" v-if="equipment.length > 0">
            <div v-for="item in equipment" :key="item.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-800 dark:text-white">{{ item.brand?.name || item.equipment_model?.brand?.name }}</span>
                        <span class="text-gray-600 dark:text-gray-300">
                            {{ item.model_name || item.equipment_model?.name }}
                            <span v-if="item.equipment_model?.is_verified" class="ml-1 text-[10px] bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 px-1 rounded">Verified</span>
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                         <span class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 rounded-full">{{ item.type?.name || item.equipment_model?.type?.name }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button 
                        @click="toggleVisibility(item)"
                        class="text-sm px-2 py-1 rounded transition"
                        :class="item.is_public ? 'text-green-600 hover:text-green-500 bg-green-100 dark:bg-green-900/30' : 'text-yellow-600 hover:text-yellow-500 bg-yellow-100 dark:bg-yellow-900/30'"
                        title="Toggle Visibility"
                    >
                        {{ item.is_public ? __('Public') : __('Private') }}
                    </button>
                    
                    <button 
                        @click="deleteItem(item)"
                        class="text-red-500 hover:text-red-700 p-1"
                        title="Remove"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="mt-6 text-gray-500 text-sm italic">
            {{ __('No equipment listed yet.') }}
        </div>

        <!-- Add Button -->
        <div class="mt-6">
            <SecondaryButton @click="adding = !adding" v-if="!adding">
                {{ __('+ Add Equipment') }}
            </SecondaryButton>
        </div>

        <!-- Add Form -->
        <div v-if="adding" class="mt-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">{{ __('Add New Item') }}</h3>
            <form @submit.prevent="addEquipment" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="brand" :value="__('Brand')" />
                        <select 
                            id="brand" 
                            v-model="form.brand_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        >
                            <option value="">{{ __('Select Brand') }}</option>
                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                {{ brand.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.brand_id" />
                    </div>

                    <div>
                        <InputLabel for="type" :value="__('Type')" />
                        <select 
                            id="type" 
                            v-model="form.equipment_type_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        >
                            <option value="">{{ __('Select Type') }}</option>
                            <option v-for="type in equipmentTypes" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.equipment_type_id" />
                    </div>
                </div>

                <div>
                    <InputLabel for="model" :value="__('Model Name')" />
                    <EquipmentAutocomplete
                        id="model"
                        v-model="form.model_name"
                        :brand-id="form.brand_id"
                        :type-id="form.equipment_type_id"
                        placeholder="e.g. CDJ-3000, SX3"
                        @select="onModelSelect"
                    />
                    <InputError class="mt-2" :message="form.errors.model_name" />
                    <p class="text-xs text-gray-500 mt-1" v-if="!form.equipment_model_id && form.model_name.length > 2">
                        {{ __('This will be added as a custom/unverified model.') }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <input 
                        type="checkbox" 
                        id="is_public" 
                        v-model="form.is_public"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600"
                    >
                    <label for="is_public" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Show on public profile') }}</label>
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">{{ __('Save') }}</PrimaryButton>
                    <button type="button" @click="adding = false" class="text-sm text-gray-500 underline hover:text-gray-700">{{ __('Cancel') }}</button>
                </div>
            </form>
        </div>
    </section>
</template>
