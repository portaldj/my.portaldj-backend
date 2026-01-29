<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    exam: Object,
});

const form = useForm({
    answers: {}, // { question_id: answer_id }
});

const submitExam = () => {
    form.post(route('academy.submit', props.exam.id), {
        onSuccess: () => {
            // alert('Exam submitted'); // Ideally redirect to results page
        },
    });
};
</script>

<template>
    <Head :title="`Exam: ${exam.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                {{ __('Exam') }}: {{ exam.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-brand-surface overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-8">
                    <form @submit.prevent="submitExam">
                        <div v-for="(question, index) in exam.questions" :key="question.id" class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-200 mb-4">{{ index + 1 }}. {{ question.text }}</h3>
                            
                            <div class="space-y-2">
                                <div v-for="answer in question.answers" :key="answer.id" class="flex items-center">
                                    <input 
                                        type="radio" 
                                        :id="`q${question.id}_a${answer.id}`" 
                                        :name="`question_${question.id}`" 
                                        :value="answer.id"
                                        v-model="form.answers[question.id]"
                                        class="w-4 h-4 text-brand-primary bg-gray-100 border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-brand-primary focus:ring-2"
                                    >
                                    <label :for="`q${question.id}_a${answer.id}`" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                        {{ answer.text }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-6 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white font-bold rounded shadow hover:opacity-90 transition disabled:opacity-50"
                            >
                                {{ __('Submit Exam') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
