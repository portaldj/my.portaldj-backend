<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    platforms: Array,
});

const deletePlatform = (id) => {
    if (confirm('Are you sure you want to delete this platform?')) {
        router.delete(route('admin.social-platforms.destroy', id));
    }
};
</script>

<template>
    <Head title="Admin - Social Platforms" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Social Platforms
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Actions -->
                <div class="mb-6 flex justify-end">
                    <Link
                        :href="route('admin.social-platforms.create')"
                        class="px-4 py-2 bg-brand-primary text-white rounded-md hover:bg-brand-secondary transition"
                    >
                        + Add Platform
                    </Link>
                </div>

                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                    <div class="p-6 text-gray-100">
                        <table class="w-full text-left text-sm text-gray-400">
                            <thead class="bg-gray-700 text-gray-200 uppercase">
                                <tr>
                                    <th class="px-6 py-3">Icon</th>
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Slug</th>
                                    <th class="px-6 py-3">Base URL</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="platform in platforms" :key="platform.id" class="border-b border-gray-700 hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <div v-html="platform.icon" class="w-6 h-6 text-brand-accent"></div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-white">{{ platform.name }}</td>
                                    <td class="px-6 py-4">{{ platform.slug }}</td>
                                    <td class="px-6 py-4 text-xs font-mono">{{ platform.base_url }}</td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <Link :href="route('admin.social-platforms.edit', platform.id)" class="text-blue-400 hover:underline">Edit</Link>
                                        <button @click="deletePlatform(platform.id)" class="text-red-400 hover:underline">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="platforms.length === 0">
                                    <td colspan="5" class="text-center py-6">No platforms found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
