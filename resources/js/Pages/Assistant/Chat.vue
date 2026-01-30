<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, nextTick, onMounted, watch } from 'vue';
import axios from 'axios';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    model: Object,
    openai_enabled: String,
    gemini_enabled: String,
});

const messages = ref([]);
const userInput = ref('');
const loading = ref(false);

// Determine default provider
const defaultProvider = props.openai_enabled === '1' ? 'openai' : (props.gemini_enabled === '1' ? 'gemini' : null);
const provider = ref(defaultProvider); 

const chatContainer = ref(null);

const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

const sendMessage = async () => {
    if (!userInput.value.trim() || loading.value) return;

    const text = userInput.value;
    userInput.value = '';
    
    // Add user message
    messages.value.push({ role: 'user', content: text });
    await scrollToBottom();

    loading.value = true;

    try {
        const response = await axios.post(route('assistant.sendMessage', props.model.id), {
            message: text,
            provider: provider.value
        });
        
        // Add AI response
        messages.value.push({ role: 'assistant', content: response.data.reply });
    } catch (error) {
        console.error(error);
        messages.value.push({ role: 'assistant', content: 'Lo siento, ocurrió un error. Por favor intenta de nuevo.' });
    } finally {
        loading.value = false;
        await scrollToBottom();
    }
};

// Initial welcome message
onMounted(() => {
    messages.value.push({ 
        role: 'assistant', 
        content: `¡Hola! Soy tu ${props.model.name}. ¿En qué puedo ayudarte con respecto a mis funciones hoy?` 
    });
});
</script>

<template>
    <Head :title="`Chat with ${model.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Chat with {{ model.brand?.name }} {{ model.name }}
                </h2>
                
                <!-- Provider Selector -->
                <div v-if="openai_enabled === '1' || gemini_enabled === '1'" class="flex items-center space-x-2 bg-white dark:bg-gray-800 p-1 rounded-lg border border-gray-200 dark:border-gray-700">
                    <button 
                        v-if="openai_enabled === '1'"
                        @click="provider = 'openai'"
                        class="px-3 py-1 text-xs font-medium rounded transition-colors"
                        :class="provider === 'openai' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    >
                        OpenAI
                    </button>
                    <button 
                        v-if="gemini_enabled === '1'"
                        @click="provider = 'gemini'"
                        class="px-3 py-1 text-xs font-medium rounded transition-colors"
                        :class="provider === 'gemini' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    >
                        Gemini
                    </button>
                </div>
                <div v-else class="text-xs text-red-500 font-bold bg-red-100 dark:bg-red-900/30 px-3 py-1 rounded">
                    AI Disabled
                </div>
            </div>
        </template>

        <div class="py-6 h-[calc(100vh-140px)] flex flex-col max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Chat Area -->
            <div 
                ref="chatContainer"
                class="flex-1 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 overflow-y-auto space-y-4 mb-4 border border-gray-200 dark:border-gray-700"
            >
                <div 
                    v-for="(msg, index) in messages" 
                    :key="index" 
                    class="flex w-full"
                    :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
                >
                    <div 
                        class="max-w-[80%] rounded-lg px-4 py-2 text-sm"
                        :class="msg.role === 'user' 
                            ? 'bg-indigo-600 text-white rounded-br-none' 
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-none'"
                    >
                        <p class="whitespace-pre-wrap">{{ msg.content }}</p>
                    </div>
                </div>
                
                <div v-if="loading" class="flex justify-start">
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2 rounded-bl-none flex items-center space-x-2">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-75"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-150"></div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                <form @submit.prevent="sendMessage" class="flex space-x-4">
                    <TextInput 
                        v-model="userInput" 
                        class="flex-1" 
                        :placeholder="provider ? 'Haz una pregunta sobre este equipo...' : 'El asistente IA está desactivado actualmente.'"
                        :disabled="loading || !provider"
                    />
                    <PrimaryButton 
                        type="submit" 
                        :disabled="loading || !userInput.trim() || !provider"
                    >
                        Send
                    </PrimaryButton>
                </form>
                <p v-if="provider" class="text-xs text-center text-gray-400 mt-2">
                    AI response generated using {{ provider === 'openai' ? 'OpenAI GPT' : 'Google Gemini' }}. Check manual for safety.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
