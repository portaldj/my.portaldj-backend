<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    items: {
        type: Array, // [{ id, name, type }, ...]
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Tag people or places...',
    },
});

const emit = defineEmits(['update:modelValue']);

const searchQuery = ref('');
const isOpen = ref(false);
const containerRef = ref(null);

const filteredItems = computed(() => {
    if (!searchQuery.value) return props.items.filter(i => !props.modelValue.some(sel => sel.id === i.id && sel.type === i.type));
    
    const query = searchQuery.value.toLowerCase();
    return props.items.filter(item => {
        // Exclude already selected
        if (props.modelValue.some(sel => sel.id === item.id && sel.type === item.type)) return false;

        return item.name.toLowerCase().includes(query);
    });
});

const selectItem = (item) => {
    const newValue = [...props.modelValue, item];
    emit('update:modelValue', newValue);
    searchQuery.value = '';
    isOpen.value = false;
};

const removeItem = (index) => {
    const newValue = [...props.modelValue];
    newValue.splice(index, 1);
    emit('update:modelValue', newValue);
};

const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

const onInput = () => {
    isOpen.value = true;
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="relative" ref="containerRef">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" v-if="modelValue.length > 0">
            {{ __('Selected Tags:') }}
        </label>
        <div class="flex flex-wrap gap-2 mb-2" v-if="modelValue.length > 0">
            <div 
                v-for="(tag, index) in modelValue" 
                :key="index"
                class="flex items-center gap-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-1 rounded-full text-xs font-medium"
            >
                <span class="capitalize" v-if="tag.type">[{{ tag.type }}]</span>
                <span>{{ tag.name }}</span>
                <button type="button" @click="removeItem(index)" class="hover:text-indigo-500 rounded-full p-0.5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        <input
            type="text"
            v-model="searchQuery"
            @input="onInput"
            @focus="isOpen = true"
            :placeholder="placeholder"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
        />
        
        <div 
            v-if="isOpen && filteredItems.length > 0"
            class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto"
        >
            <ul class="py-1">
                <li 
                    v-for="(item, index) in filteredItems" 
                    :key="index"
                    @click="selectItem(item)"
                    class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer transition-colors flex justify-between"
                >
                    <span>{{ item.name }}</span>
                    <span class="text-xs text-gray-500 uppercase">{{ item.type }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>
