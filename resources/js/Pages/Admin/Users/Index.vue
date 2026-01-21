<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

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

// Subscription Management
const managingUser = ref(null);
const showSubscriptionModal = ref(false);
const subForm = useForm({
    action: 'grant',
    expires_at: '',
});

const openSubscriptionModal = (user) => {
    managingUser.value = user;
    const activeSub = getActiveSub(user);
    
    // Set default date to +1 month
    const nextMonth = new Date();
    nextMonth.setMonth(nextMonth.getMonth() + 1);
    
    if (activeSub) {
        subForm.expires_at = activeSub.expires_at.split('T')[0]; // Format YYYY-MM-DD
    } else {
        subForm.expires_at = nextMonth.toISOString().split('T')[0];
    }
    
    showSubscriptionModal.value = true;
};

const closeSubscriptionModal = () => {
    showSubscriptionModal.value = false;
    managingUser.value = null;
    subForm.reset();
};

const getActiveSub = (user) => {
    if (!user || !user.subscriptions || user.subscriptions.length === 0) return null;
    const sub = user.subscriptions[0];
    const now = new Date();
    const expires = new Date(sub.expires_at);
    if (sub.status === 'paid' && expires > now) return sub;
    return null;
};

const submitSubscription = (action) => {
    subForm.action = action;
    subForm.post(route('admin.users.subscription', managingUser.value.id), {
        onSuccess: () => closeSubscriptionModal(),
    });
};

const grantDuration = (months) => {
    const date = new Date();
    date.setMonth(date.getMonth() + months);
    subForm.expires_at = date.toISOString().split('T')[0];
};

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
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Subscription</th>
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span v-if="getActiveSub(user)" class="px-2 inline-flex flex-col text-xs leading-tight font-semibold rounded bg-amber-900/50 text-amber-200 border border-amber-700 p-1 text-center">
                                <span>PRO</span>
                                <span class="text-[10px] opacity-75">Exp: {{ new Date(getActiveSub(user).expires_at).toLocaleDateString() }}</span>
                            </span>
                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-700 text-gray-300">
                                Free
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openSubscriptionModal(user)" class="text-amber-400 hover:text-amber-300 mr-4">Subscription</button>
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

        <!-- Subscription Modal -->
        <Modal :show="showSubscriptionModal" @close="closeSubscriptionModal">
            <div class="p-6 bg-gray-800 text-white">
                <h2 class="text-lg font-medium text-white mb-4">
                    Manage Subscription: <span class="text-brand-accent">{{ managingUser?.name }}</span>
                </h2>

                <div v-if="getActiveSub(managingUser)" class="mb-6 bg-green-900/20 border border-green-700 p-4 rounded text-center">
                    <p class="text-green-400 font-bold text-lg">Active PRO Subscription</p>
                    <p class="text-sm text-gray-300">Expires: {{ new Date(getActiveSub(managingUser).expires_at).toLocaleDateString() }}</p>
                </div>
                <div v-else class="mb-6 bg-gray-700/50 p-4 rounded text-center">
                    <p class="text-gray-400 font-bold">User is currently on FREE plan</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <InputLabel value="Expiration Date" />
                        <TextInput 
                            type="date" 
                            v-model="subForm.expires_at" 
                            class="mt-1 block w-full bg-gray-900 border-gray-600 text-white" 
                        />
                         <div class="flex gap-2 mt-2">
                            <button @click.prevent="grantDuration(1)" type="button" class="text-xs bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded">+1 Month</button>
                            <button @click.prevent="grantDuration(6)" type="button" class="text-xs bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded">+6 Months</button>
                            <button @click.prevent="grantDuration(12)" type="button" class="text-xs bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded">+1 Year</button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-700">
                        <SecondaryButton @click="closeSubscriptionModal">Cancel</SecondaryButton>
                        
                        <div v-if="getActiveSub(managingUser)" class="flex gap-3">
                             <DangerButton @click="submitSubscription('revoke')" :disabled="subForm.processing">
                                Revoke PRO
                            </DangerButton>
                             <PrimaryButton @click="submitSubscription('update')" :disabled="subForm.processing">
                                Update Expiry
                            </PrimaryButton>
                        </div>
                        <div v-else>
                            <PrimaryButton @click="submitSubscription('grant')" :disabled="subForm.processing">
                                Grant PRO Access
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

    </AdminLayout>

</template>
