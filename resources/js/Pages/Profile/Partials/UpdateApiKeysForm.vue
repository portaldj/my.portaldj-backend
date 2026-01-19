<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

const form = useForm({
    openai_key: user.openai_key || '',
    gemini_key: user.gemini_key || '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">AI API Keys</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Provide your own API keys to query the AI Assistant without limits. Your keys are stored encrypted.
                Leave empty to use the platform's default limited quota.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="openai_key" value="OpenAI API Key (sk-...)" />

                <TextInput
                    id="openai_key"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.openai_key"
                    placeholder="sk-..."
                />

                <InputError class="mt-2" :message="form.errors.openai_key" />
            </div>

            <div>
                <InputLabel for="gemini_key" value="Google Gemini API Key" />

                <TextInput
                    id="gemini_key"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.gemini_key"
                    placeholder="AI..."
                />

                <InputError class="mt-2" :message="form.errors.gemini_key" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save Keys</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
