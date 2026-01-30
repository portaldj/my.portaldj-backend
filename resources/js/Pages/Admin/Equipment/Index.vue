<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    brands: Array,
    types: Array,
    models: Object,
});

const activeTab = ref(new URLSearchParams(window.location.search).get('tab') || 'brands');

const formBrand = useForm({ id: null, name: '' });
const formType = useForm({ id: null, name: '' });
const formModel = useForm({ 
    id: null,
    brand_id: '',
    equipment_type_id: '',
    name: '',
    is_verified: false,
    documentation: ''
});

const addingBrand = ref(false);
const addingType = ref(false);
const addingModel = ref(false);
const isEditing = ref(false);

const openBrandModal = (brand = null) => {
    formBrand.reset();
    formBrand.clearErrors();
    if (brand) {
        formBrand.id = brand.id;
        formBrand.name = brand.name;
        isEditing.value = true;
    } else {
        formBrand.id = null;
        isEditing.value = false;
    }
    addingBrand.value = true;
};

const openTypeModal = (type = null) => {
    formType.reset();
    formType.clearErrors();
    if (type) {
        formType.id = type.id;
        formType.name = type.name;
        isEditing.value = true;
    } else {
        formType.id = null;
        isEditing.value = false;
    }
    addingType.value = true;
};

const formChunk = useForm({
    topic: '',
    content: ''
});

const currentChunks = ref([]);

const openModelModal = (model = null) => {
    formModel.reset();
    formModel.clearErrors();
    formChunk.reset();
    currentChunks.value = [];

    if (model) {
        formModel.id = model.id;
        formModel.brand_id = model.brand_id;
        formModel.equipment_type_id = model.equipment_type_id;
        formModel.name = model.name;
        formModel.is_verified = Boolean(model.is_verified);
        // Load chunks from the prop (which is eager loaded)
        currentChunks.value = model.chunks || [];
        isEditing.value = true;
    } else {
        formModel.id = null;
        isEditing.value = false;
    }
    addingModel.value = true;
};

const submitBrand = () => {
    if (isEditing.value) {
        formBrand.patch(route('admin.brands.update', formBrand.id), {
            onSuccess: () => { formBrand.reset(); addingBrand.value = false; }
        });
    } else {
        formBrand.post(route('admin.brands.store'), {
            onSuccess: () => { formBrand.reset(); addingBrand.value = false; }
        });
    }
};

const submitType = () => {
    if (isEditing.value) {
        formType.patch(route('admin.equipment-types.update', formType.id), {
            onSuccess: () => { formType.reset(); addingType.value = false; }
        });
    } else {
        formType.post(route('admin.equipment-types.store'), {
            onSuccess: () => { formType.reset(); addingType.value = false; }
        });
    }
};

const submitModel = () => {
    if (isEditing.value) {
        formModel.patch(route('admin.equipment-models.update', formModel.id), {
            onSuccess: () => { 
                formModel.reset(); 
                addingModel.value = false; 
            }
        });
    } else {
        formModel.post(route('admin.equipment-models.store'), {
            onSuccess: () => { formModel.reset(); addingModel.value = false; }
        });
    }
};

const submitChunk = () => {
    if (!formModel.id) return;

    formChunk.post(route('admin.equipment-chunks.store', formModel.id), {
        preserveScroll: true,
        onSuccess: () => {
            formChunk.reset();
            // We need to refresh the currentChunks list. 
            // Since we are using router state/props, the page prop updates.
            // We need to re-find the model in the updated props OR trust that the page reload updates everything?
            // Inertia reload updates props. But `currentChunks` is a local ref copy.
            // We need to watch props or manually update local ref.
            // A simpler way: Find the updated model in `props.models.data`
            // But since we are inside a method, `props` is reactive.
            // Let's rely on a watcher or manual re-fetch.
            // Actually, `onSuccess` happens after the visit completes.
            const updatedModel = props.models.data.find(m => m.id === formModel.id);
            if (updatedModel) {
                currentChunks.value = updatedModel.chunks;
            }
        }
    });
};

const deleteChunk = (chunkId) => {
    if (!confirm('Delete this chunk?')) return;
    router.delete(route('admin.equipment-chunks.destroy', chunkId), {
         preserveScroll: true,
         onSuccess: () => {
            const updatedModel = props.models.data.find(m => m.id === formModel.id);
            if (updatedModel) {
                currentChunks.value = updatedModel.chunks;
            }
         }
    });
};

const deleteItem = (routeUrl) => {
    if(confirm('Are you sure? This cannot be undone.')) {
        router.delete(routeUrl);
    }
};
</script>

<template>
    <Head title="Equipment Manager" />

    <AdminLayout>
        <template #header>Equipment Manager</template>

        <!-- Tabs -->
        <div class="flex space-x-4 border-b border-gray-700 mb-6">
            <button 
                @click="activeTab = 'brands'"
                class="pb-2 px-4 text-sm font-medium transition-colors"
                :class="activeTab === 'brands' ? 'text-brand-primary border-b-2 border-brand-primary' : 'text-gray-400 hover:text-white'"
            >
                Brands
            </button>
            <button 
                @click="activeTab = 'types'"
                class="pb-2 px-4 text-sm font-medium transition-colors"
                :class="activeTab === 'types' ? 'text-brand-primary border-b-2 border-brand-primary' : 'text-gray-400 hover:text-white'"
            >
                Types
            </button>
            <button 
                @click="activeTab = 'models'"
                class="pb-2 px-4 text-sm font-medium transition-colors"
                :class="activeTab === 'models' ? 'text-brand-primary border-b-2 border-brand-primary' : 'text-gray-400 hover:text-white'"
            >
                Models
            </button>
        </div>

        <!-- Brands Tab -->
        <div v-if="activeTab === 'brands'">
            <div class="flex justify-end mb-4">
                <PrimaryButton @click="openBrandModal()">+ Add Brand</PrimaryButton>
            </div>
            
            <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                <table class="w-full text-left text-gray-400">
                    <thead class="bg-gray-700 text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Slug</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr v-for="brand in brands" :key="brand.id" class="hover:bg-gray-750">
                            <td class="px-6 py-4">{{ brand.name }}</td>
                            <td class="px-6 py-4">{{ brand.slug }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openBrandModal(brand)" class="text-blue-400 hover:text-blue-300">Edit</button>
                                <button @click="deleteItem(route('admin.brands.destroy', brand.id))" class="text-red-400 hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <Modal :show="addingBrand" @close="addingBrand = false">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ isEditing ? 'Edit' : 'Add' }} Brand</h2>
                    <form @submit.prevent="submitBrand" class="mt-4 space-y-4">
                        <div>
                            <InputLabel for="brand_name" value="Name" />
                            <TextInput id="brand_name" v-model="formBrand.name" class="mt-1 block w-full" required autofocus />
                            <InputError class="mt-2" :message="formBrand.errors.name" />
                        </div>
                        <div class="flex justify-end gap-2">
                             <SecondaryButton @click="addingBrand = false">Cancel</SecondaryButton>
                             <PrimaryButton :disabled="formBrand.processing">{{ isEditing ? 'Update' : 'Save' }}</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>

        <!-- Types Tab -->
        <div v-if="activeTab === 'types'">
             <div class="flex justify-end mb-4">
                <PrimaryButton @click="openTypeModal()">+ Add Type</PrimaryButton>
            </div>
            
             <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                <table class="w-full text-left text-gray-400">
                    <thead class="bg-gray-700 text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Slug</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr v-for="type in types" :key="type.id" class="hover:bg-gray-750">
                            <td class="px-6 py-4">{{ type.name }}</td>
                            <td class="px-6 py-4">{{ type.slug }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openTypeModal(type)" class="text-blue-400 hover:text-blue-300">Edit</button>
                                <button @click="deleteItem(route('admin.equipment-types.destroy', type.id))" class="text-red-400 hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Modal :show="addingType" @close="addingType = false">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ isEditing ? 'Edit' : 'Add' }} Equipment Type</h2>
                    <form @submit.prevent="submitType" class="mt-4 space-y-4">
                        <div>
                            <InputLabel for="type_name" value="Name" />
                            <TextInput id="type_name" v-model="formType.name" class="mt-1 block w-full" required autofocus />
                            <InputError class="mt-2" :message="formType.errors.name" />
                        </div>
                        <div class="flex justify-end gap-2">
                             <SecondaryButton @click="addingType = false">Cancel</SecondaryButton>
                             <PrimaryButton :disabled="formType.processing">{{ isEditing ? 'Update' : 'Save' }}</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>

        <!-- Models Tab -->
        <div v-if="activeTab === 'models'">
             <div class="flex justify-end mb-4">
                <PrimaryButton @click="openModelModal()">+ Add Model</PrimaryButton>
            </div>
            
            <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                <table class="w-full text-left text-gray-400">
                    <thead class="bg-gray-700 text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Brand</th>
                            <th class="px-6 py-3">Model</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr v-for="model in models.data" :key="model.id" class="hover:bg-gray-750">
                            <td class="px-6 py-4 font-bold text-white">{{ model.brand?.name }}</td>
                            <td class="px-6 py-4">
                                {{ model.name }}
                                <span v-if="model.is_verified" class="ml-2 text-xs bg-green-900 text-green-300 px-1.5 py-0.5 rounded">Verified</span>
                            </td>
                            <td class="px-6 py-4">{{ model.type?.name }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openModelModal(model)" class="text-blue-400 hover:text-blue-300">Edit</button>
                                <button @click="deleteItem(route('admin.equipment-models.destroy', model.id))" class="text-red-400 hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
             <Pagination :links="models.links" class="mt-6" />

             <Modal :show="addingModel" @close="addingModel = false">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ isEditing ? 'Edit' : 'Add' }} Equipment Model</h2>
                    <form @submit.prevent="submitModel" class="mt-4 space-y-4">
                         <div>
                            <InputLabel for="model_brand" value="Brand" />
                            <select id="model_brand" v-model="formModel.brand_id" class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded" required>
                                <option value="">Select Brand</option>
                                <option v-for="b in brands" :value="b.id" :key="b.id">{{ b.name }}</option>
                            </select>
                        </div>
                         <div>
                            <InputLabel for="model_type" value="Type" />
                            <select id="model_type" v-model="formModel.equipment_type_id" class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded" required>
                                <option value="">Select Type</option>
                                <option v-for="t in types" :value="t.id" :key="t.id">{{ t.name }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="model_name" value="Name" />
                            <TextInput id="model_name" v-model="formModel.name" class="mt-1 block w-full" placeholder="e.g. CDJ-3000" required />
                            <InputError class="mt-2" :message="formModel.errors.name" />
                        </div>

                        <div v-if="isEditing" class="border-t border-gray-700 pt-4 mt-4">
                            <h3 class="text-md font-bold text-gray-200 mb-2">Knowledge Base (RAG)</h3>
                            <p class="text-xs text-gray-400 mb-4">Break down documentation into logical chunks for better AI retrieval.</p>
                            
                            <!-- Chunk List -->
                            <div v-if="currentChunks.length > 0" class="space-y-2 mb-4 max-h-60 overflow-y-auto pr-1">
                                <div v-for="chunk in currentChunks" :key="chunk.id" class="bg-gray-800 p-3 rounded border border-gray-700 text-sm">
                                    <div class="flex justify-between items-start">
                                        <span class="font-bold text-brand-primary">{{ chunk.topic }}</span>
                                        <button type="button" @click="deleteChunk(chunk.id)" class="text-red-400 text-xs hover:text-red-300">Delete</button>
                                    </div>
                                    <p class="text-gray-400 mt-1 text-xs truncate">{{ chunk.content }}</p>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500 italic mb-4">No chunks added yet.</div>

                            <!-- Add Chunk Form -->
                            <div class="bg-gray-800 p-3 rounded border border-gray-700">
                                <h4 class="text-sm font-semibold text-gray-300 mb-2">Add New Chunk</h4>
                                <div class="space-y-2">
                                    <TextInput 
                                        v-model="formChunk.topic" 
                                        placeholder="Topic (e.g. Jog Adjust)" 
                                        class="block w-full text-xs" 
                                    />
                                    <textarea 
                                        v-model="formChunk.content" 
                                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs"
                                        rows="3"
                                        placeholder="Content..."
                                    ></textarea>
                                    <div class="flex justify-end">
                                        <SecondaryButton 
                                            type="button" 
                                            @click="submitChunk" 
                                            :disabled="formChunk.processing || !formChunk.topic || !formChunk.content"
                                            class="text-xs"
                                        >
                                            Add Chunk
                                        </SecondaryButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="mt-4 p-4 bg-yellow-900/20 text-yellow-500 text-sm rounded">
                            Save this model first to start adding documentation chunks.
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                id="is_verified" 
                                v-model="formModel.is_verified"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600"
                            >
                            <label for="is_verified" class="text-sm text-gray-700 dark:text-gray-300">Verified Model</label>
                            <InputError class="mt-2" :message="formModel.errors.is_verified" />
                        </div>
                        
                        <div class="flex justify-end gap-2">
                             <SecondaryButton @click="addingModel = false">Cancel</SecondaryButton>
                             <PrimaryButton :disabled="formModel.processing">{{ isEditing ? 'Update' : 'Save' }}</PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>

    </AdminLayout>
</template>
