<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    modelValue: String, // The text value (model name)
    brandId: [Number, String],
    typeId: [Number, String],
    placeholder: String,
});

const emit = defineEmits(['update:modelValue', 'select']);

const query = ref(props.modelValue || '');
const suggestions = ref([]);
const showSuggestions = ref(false);
const loading = ref(false);

watch(() => props.modelValue, (newVal) => {
    query.value = newVal;
});

let debounceTimer = null;

const onInput = () => {
    emit('update:modelValue', query.value);
    
    // Clear selection if user types
    emit('select', null); 

    if (query.value.length < 2) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchSuggestions, 300);
};

const fetchSuggestions = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/equipment', {
            params: {
                search: query.value,
                brand_id: props.brandId,
                equipment_type_id: props.typeId
            }
        });
        suggestions.value = response.data;
        showSuggestions.value = suggestions.value.length > 0;
    } catch (error) {
        console.error('Failed to fetch equipment suggestions', error);
    } finally {
        loading.value = false;
    }
};

const selectItem = (item) => {
    query.value = item.name;
    emit('update:modelValue', item.name);
    emit('select', item);
    showSuggestions.value = false;
};

const closeSuggestions = () => {
    // Delay to allow click event to process
    setTimeout(() => {
        showSuggestions.value = false;
    }, 200);
};
</script>

<template>
    <div class="relative">
        <TextInput
            type="text"
            v-model="query"
            @input="onInput"
            @focus="onInput"
            @blur="closeSuggestions"
            class="w-full"
            :placeholder="placeholder"
            autocomplete="off"
        />
        
        <!-- Suggestions Dropdown -->
        <div v-if="showSuggestions" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto">
            <ul class="py-1">
                <li 
                    v-for="item in suggestions" 
                    :key="item.id"
                    @click="selectItem(item)"
                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200"
                >
                    <div class="font-medium">{{ item.name }}</div>
                </li>
            </ul>
        </div>
    </div>
</template>
