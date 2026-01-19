<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    base_url: '',
    icon: '',
});

const submit = () => {
    form.post(route('admin.social-platforms.store'));
};
</script>

<template>
    <Head title="Admin - Create Platform" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Create Social Platform
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="name" value="Platform Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                placeholder="e.g. Instagram"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="base_url" value="Base URL (Optional)" />
                            <TextInput
                                id="base_url"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.base_url"
                                placeholder="e.g. https://instagram.com/"
                            />
                            <p class="text-xs text-gray-500 mt-1">If set, user just enters their username.</p>
                            <InputError class="mt-2" :message="form.errors.base_url" />
                        </div>

                        <div>
                            <InputLabel for="icon" value="Icon (SVG Code)" />
                            <textarea
                                id="icon"
                                v-model="form.icon"
                                class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-brand-accent focus:ring-brand-accent h-32 font-mono text-xs"
                                placeholder="<svg ...></svg>"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.icon" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <Link :href="route('admin.social-platforms.index')" class="text-gray-400 hover:text-white transition">Cancel</Link>
                            <PrimaryButton :disabled="form.processing">Create Platform</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
