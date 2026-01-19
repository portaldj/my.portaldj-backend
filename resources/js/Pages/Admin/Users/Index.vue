<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    users: Object,
    filters: Object,
});

const form = useForm({});
const search = ref(props.filters.search || '');

watch(search, debounce((value) => {
    router.get(route('admin.users.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deleteUser = (id) => {
    if (confirm('Delete this user? This action cannot be undone.')) {
        form.delete(route('admin.users.destroy', id));
    }
}
</script>

<template>
    <Head title="User Manager" />

    <AdminLayout>
        <template #header>
            User Manager
        </template>

        <div class="mb-6 flex justify-between items-center">
            <input 
                v-model="search"
                type="text" 
                placeholder="Search users..." 
                class="bg-gray-800 text-white rounded border-gray-700 w-64 focus:ring-brand-primary focus:border-brand-primary"
            />
            
            <Link :href="route('admin.users.create')" class="px-4 py-2 bg-brand-primary hover:bg-brand-secondary text-white rounded font-bold transition">
                Create User
            </Link>
        </div>

        <div class="bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Roles</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-300">
                    <tr v-for="user in users.data" :key="user.id">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-white">
                            {{ user.name }}
                             <span v-if="user.id === $page.props.auth.user.id" class="ml-2 text-xs text-brand-primary">(You)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span v-for="role in user.roles" :key="role.id" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full mr-1" :class="role.name === 'Admin' ? 'bg-purple-900 text-purple-200 border border-purple-700' : 'bg-blue-900 text-blue-200'">
                                {{ role.name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span v-if="user.banned_until && new Date(user.banned_until) > new Date()" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-900 text-red-200 border border-red-700">
                                Banned
                            </span>
                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-900 text-green-200 border border-green-700">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link :href="route('admin.users.edit', user.id)" class="text-brand-accent hover:text-white mr-4">Edit</Link>
                            <button @click="deleteUser(user.id)" class="text-red-400 hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
         <div class="mt-4 flex justify-between" v-if="users.links.length > 3">
             <Link 
                v-for="(link, k) in users.links" 
                :key="k" 
                :href="link.url || '#'" 
                v-html="link.label"
                class="px-3 py-1 border rounded text-sm"
                 :class="link.active ? 'bg-brand-primary border-brand-primary text-white' : 'border-gray-700 text-gray-400'" 
            />
        </div>
    </AdminLayout>
</template>
