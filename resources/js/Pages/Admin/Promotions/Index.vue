<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    promotions: Object,
});

// Modal State
const showApproveModal = ref(false);
const showRejectModal = ref(false);
const selectedPromotion = ref(null);

const approveForm = useForm({
    blog_url: '',
});

const rejectForm = useForm({
    rejection_reason: '',
});

function openApprove(promotion) {
    selectedPromotion.value = promotion;
    approveForm.reset();
    showApproveModal.value = true;
}

function openReject(promotion) {
    selectedPromotion.value = promotion;
    rejectForm.reset();
    showRejectModal.value = true;
}

function closeModals() {
    showApproveModal.value = false;
    showRejectModal.value = false;
    selectedPromotion.value = null;
    approveForm.reset();
    rejectForm.reset();
}

function submitApprove() {
    if (!selectedPromotion.value) return;
    approveForm.post(route('admin.promotions.approve', selectedPromotion.value.id), {
        onSuccess: () => closeModals(),
    });
}

function submitReject() {
    if (!selectedPromotion.value) return;
    rejectForm.post(route('admin.promotions.reject', selectedPromotion.value.id), {
        onSuccess: () => closeModals(),
    });
}
</script>

<template>
    <Head title="Booking Promotions" />

    <AdminLayout>
        <template #header>
            Booking Promotions
        </template>

        <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-100">
                <div v-if="promotions.data.length === 0" class="text-center py-10 text-gray-400">
                    No pending promotion requests.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">DJ</th>
                                <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-3 bg-gray-700 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700">
                            <tr v-for="promo in promotions.data" :key="promo.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">{{ promo.dj_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ promo.event_title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ promo.event_date }}</td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    <div class="max-w-xs truncate" :title="promo.press_release">
                                        {{ promo.press_release }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openApprove(promo)" class="text-green-400 hover:text-green-300 mr-4">Approve</button>
                                    <button @click="openReject(promo)" class="text-red-400 hover:text-red-300">Reject</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                 <div v-if="promotions.links.length > 3" class="mt-6 flex justify-center">
                    <!-- Simple pagination implementation if needed, or use a component -->
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <Modal :show="showApproveModal" @close="closeModals">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Approve Promotion
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                     Please provide the URL to the published blog post.
                </p>

                <form @submit.prevent="submitApprove">
                    <div>
                        <InputLabel for="blog_url" value="Blog Post URL" />
                        <TextInput id="blog_url" v-model="approveForm.blog_url" type="url" class="mt-1 block w-full" placeholder="https://portaldj.cl/blog/..." required />
                        <InputError :message="approveForm.errors.blog_url" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModals">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': approveForm.processing }" :disabled="approveForm.processing">
                            Confirm Approval
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Reject Modal -->
        <Modal :show="showRejectModal" @close="closeModals">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Reject Promotion
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                     Please provide a reason for the rejection. This will be visible to the DJ.
                </p>

                <form @submit.prevent="submitReject">
                    <div>
                        <InputLabel for="rejection_reason" value="Reason" />
                        <textarea 
                            id="rejection_reason" 
                            v-model="rejectForm.rejection_reason" 
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            rows="3"
                            required
                        ></textarea>
                        <InputError :message="rejectForm.errors.rejection_reason" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModals">Cancel</SecondaryButton>
                        <DangerButton :class="{ 'opacity-25': rejectForm.processing }" :disabled="rejectForm.processing">
                            Reject Request
                        </DangerButton>
                    </div>
                </form>
            </div>
        </Modal>

    </AdminLayout>
</template>
