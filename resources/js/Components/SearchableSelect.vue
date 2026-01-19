<script setup>
import { ref, computed, watch, nextTick } from 'vue';
// import { onClickOutside } from '@vueuse/core'; // using vanilla fallback

const props = defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Select option',
    },
});

const emit = defineEmits(['update:modelValue']);

const searchQuery = ref('');
const isOpen = ref(false);
const containerRef = ref(null);

// Initialize search query with selected item name if exists
watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        const selected = props.options.find(o => o.id === newVal);
        if (selected) {
            searchQuery.value = selected.name;
        }
    } else {
        searchQuery.value = '';
    }
}, { immediate: true });

const filteredOptions = computed(() => {
    if (!searchQuery.value) return props.options;
    // If exact match (user selected), show all? No, filter is fine.
    // Logic: If query matches specific item exactly, user might be done.
    return props.options.filter(option => 
        option.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const selectOption = (option) => {
    emit('update:modelValue', option.id);
    searchQuery.value = option.name;
    isOpen.value = false;
};

const openDropdown = () => {
    isOpen.value = true;
    // If current query is the selected item, maybe verify? 
    // Actually, if user focuses, let them see full list if they clear, or filtered list.
};

const onInput = () => {
    isOpen.value = true;
    if (searchQuery.value === '') {
        emit('update:modelValue', null); // Clear selection if text cleared
    }
};

// Handle click outside to close
// Simple custom implementation if vueuse not available
import { onMounted, onUnmounted } from 'vue';

const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        isOpen.value = false;
        // Revert invalid text input? 
        // Logic: if current query doesn't match a selected ID, verify.
        // For simple usage: If no ID selected, clear query. 
        if (!props.modelValue) {
            searchQuery.value = '';
        } else {
            // Restore name of selected ID
            const selected = props.options.find(o => o.id === props.modelValue);
            if (selected && searchQuery.value !== selected.name) {
                // searchQuery.value = selected.name; // Optional: revert or keep as is? 
                // Let's keep strict sync
                searchQuery.value = selected.name;
            }
        }
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
        <div class="relative">
            <input
                type="text"
                v-model="searchQuery"
                @focus="openDropdown"
                @input="onInput"
                :placeholder="placeholder"
                class="w-full bg-gray-800 border-gray-700 text-gray-300 text-sm rounded focus:ring-brand-accent focus:border-brand-accent py-1 px-2 pr-8"
            />
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>

        <!-- Dropdown List -->
        <div 
            v-if="isOpen && filteredOptions.length > 0"
            class="absolute z-50 mt-1 w-full bg-gray-800 border border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto"
        >
            <ul class="py-1">
                <li 
                    v-for="option in filteredOptions" 
                    :key="option.id"
                    @click="selectOption(option)"
                    class="px-3 py-2 text-sm text-gray-300 hover:bg-brand-primary hover:text-white cursor-pointer transition-colors"
                    :class="{'bg-gray-700': modelValue === option.id}"
                >
                    {{ option.name }}
                </li>
            </ul>
        </div>
        
        <div v-if="isOpen && filteredOptions.length === 0" class="absolute z-50 mt-1 w-full bg-gray-800 border border-gray-700 rounded p-2 text-sm text-gray-500">
            No results found.
        </div>
    </div>
</template>
