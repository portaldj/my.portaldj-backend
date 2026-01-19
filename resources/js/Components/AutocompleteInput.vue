<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    items: {
        type: Array, // Expected: [{ id, name }, ...] or strings
        default: () => [],
    },
    placeholder: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const searchQuery = ref(props.modelValue);
const isOpen = ref(false);
const containerRef = ref(null);

watch(() => props.modelValue, (newVal) => {
    if (newVal !== searchQuery.value) {
        searchQuery.value = newVal;
    }
});

const filteredItems = computed(() => {
    if (!searchQuery.value) return props.items;
    const query = searchQuery.value.toLowerCase();
    
    return props.items.filter(item => {
        const name = typeof item === 'object' ? item.name : item;
        return name.toLowerCase().includes(query) && name.toLowerCase() !== query;
    });
});

const onInput = () => {
    isOpen.value = true;
    emit('update:modelValue', searchQuery.value);
};

const selectItem = (item) => {
    const name = typeof item === 'object' ? item.name : item;
    searchQuery.value = name;
    emit('update:modelValue', name);
    isOpen.value = false;
};

const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        isOpen.value = false;
    }
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
                    class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer transition-colors"
                >
                    {{ typeof item === 'object' ? item.name : item }}
                </li>
            </ul>
        </div>
    </div>
</template>
