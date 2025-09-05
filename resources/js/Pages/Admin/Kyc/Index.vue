<script setup>
import { ref, onMounted } from 'vue'
import { useMainStore } from '@/Stores/main'
import { useAxios } from '@/composables/axios.js'
import { Head, router } from '@inertiajs/vue3'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import CardBoxModal from '@/Components/CardBoxModal.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import BaseDivider from '@/Components/BaseDivider.vue'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import FormCheckRadioGroup from '@/Components/FormCheckRadioGroup.vue'

import { mdiOfficeBuildingMarker, mdiDotsVertical, mdiEye } from '@mdi/js'

const props = defineProps({
    auth: Object,
    status: {
        default: 'all'
    }
})

const mainStore = useMainStore()

// Pagination & filters
const businesses = ref({})
const rejectionReason = ref('')
const showActionModal = ref(false)
const selectedBusiness = ref(null)
const showKycModal = ref(false)
const showBusinessDetailsModal = ref(false)
const selectedKyc = ref(null)

const statusOptions = ref({ pending: 'pending', approved: 'approved', rejected: 'rejected', all: 'all' });

const businesses_props = ref({
    page: 1,
    length: 10,
    status: props.status,
    user_name: null,
    full_name: null,
    business_name: null,
    email: null,
    phone: null,
    state: null,
    city: null,
    date: null,
    start_date: null,
    end_date: null,
})

const openKycModal = (business) => {
    selectedKyc.value = business
    showKycModal.value = true
}

const openBusinessDetailsModal = (business) => {
    selectedBusiness.value = business
    showBusinessDetailsModal.value = true
}



// Load businesses
const loadBusinesses = async (url = null) => {
    if (url != null) {
        const urlParams = new URLSearchParams(url)
        businesses_props.value.page = urlParams.get('page')
    }

    const { response, error } = await useAxios(route('admin.load_all'), 'Loading Businesses...', businesses_props.value)

    if (response?.value?.data) {
        businesses.value = response.value.data
    } else if (error?.value) {
        mainStore.toast = 'Failed to load businesses'
    }
}

// Clear filters
const clearFilters = () => {
    businesses_props.value = {
        page: 1,
        length: 10,
        status: props.status,
        user_name: null,
        full_name: null,
        business_name: null,
        email: null,
        phone: null,
        state: null,
        city: null,
        date: null,
        start_date: null,
        end_date: null,
    }
}

// Actions
const openActionModal = (business) => {
    selectedBusiness.value = business
    showActionModal.value = true
}

const approveBusiness = async () => {
    if (!selectedBusiness.value) return
    const { response, error } = await useAxios(route('admin.kyc.approve', selectedBusiness.value.id), 'Approving business...')
    if (response?.value) {
        mainStore.toast = 'Business approved'
        showActionModal.value = false
        loadBusinesses()
    }
}

const rejectBusiness = async (reason) => {
    if (!selectedBusiness.value) return
    const { response, error } = await useAxios(route('admin.kyc.reject', selectedBusiness.value.id), 'Rejecting business...', { reason })
    if (response?.value) {
        mainStore.toast = 'Business rejected'
        showActionModal.value = false
        loadBusinesses()
    }
}

const deleteBusiness = async () => {
    if (!selectedBusiness.value) return
    const { response, error } = await useAxios(route('admin.kyc.delete', selectedBusiness.value.id), 'Deleting business...')
    if (response?.value) {
        mainStore.toast = 'Business deleted'
        showActionModal.value = false
        loadBusinesses()
    }
}

onMounted(() => {
    loadBusinesses()
})
</script>

<template>
    <LayoutAuthenticated>

        <Head title="Manage Pending Businesses" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiOfficeBuildingMarker" title="Manage Pending Businesses" main />

            <CardBox>
                <!-- Filters -->
                <CardBox isForm @submit.prevent="loadBusinesses">
                    <div class="sm:grid sm:grid-cols-12 sm:gap-6">
                        <FormField class="sm:col-span-4" label="Length">
                            <FormControl v-model="businesses_props.length" :options="[10, 20, 50, 100]" />
                        </FormField>
                        <!-- <FormField class="sm:col-span-4" label="Status">
                            <FormControl v-model="businesses_props.status" :options="statusOptions" />
                        </FormField> -->

                        <FormField class="sm:col-span-4 capitalize" label="Status">
                            <FormCheckRadioGroup v-model="businesses_props.status"  :options="statusOptions" type="radio" />
                        </FormField>
                        <FormField class="sm:col-span-4" label="Merchant Username">
                            <FormControl v-model="businesses_props.user_name" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-4" label="Merchant Fullname">
                            <FormControl v-model="businesses_props.full_name" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-4" label="Business Name">
                            <FormControl v-model="businesses_props.business_name" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-4" label="Email">
                            <FormControl v-model="businesses_props.email" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-4" label="Phone">
                            <FormControl v-model="businesses_props.phone" type="text" />
                        </FormField>
                    </div>
                    <BaseButtons class="mt-5 mb-2">
                        <BaseButton type="submit" color="info" label="Filter" />
                        <BaseButton type="reset" color="info" outline label="Clear" @click="clearFilters" />
                    </BaseButtons>
                    <BaseDivider />
                </CardBox>

                <!-- Table -->
                <CardBox has-table>
                    <div v-if="businesses.data && businesses.data.length > 0">
                        <table class="table table-striped w-full text-gray-800 dark:text-slate-200">
                            <thead class="bg-gray-100 dark:bg-slate-700">
                                <tr>
                                    <th>#</th>
                                    <th>Merchant Username</th>
                                    <th>Merchant Name</th>
                                    <th>Business Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in businesses.data" :key="row.id"
                                    class="border-t border-gray-200 dark:border-slate-700">
                                    <td>
                                        {{ index + 1 + ((businesses.current_page - 1) *
                                            businesses_props.length) }}
                                    </td>
                                    <td>{{ row.user.user_name }}</td>
                                    <td>{{ row.user.name }}</td>
                                    <td>{{ row.business_name }}</td>
                                    <td>{{ row.user.email }}</td>
                                    <td>{{ row.user.phone }}</td>
                                    <td>{{ row.state.name }}</td>
                                    <td>{{ row.city.name }}</td>
                                    <td>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200':
                                                    row?.status === 'pending',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200':
                                                    row?.status === 'approved',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200':
                                                    row?.status === 'rejected'
                                            }">
                                            {{ row?.status }}
                                        </span>
                                    </td>
                                    <td>{{ mainStore.formatDate(row.created_at) }}</td>
                                    <td>
                                        <BaseButtons>
                                            <BaseButton title="View Business Details" color="success" :icon="mdiEye"
                                                small @click="openBusinessDetailsModal(row)" />

                                            <BaseButton title="View KYC Documents" color="warning"
                                                :icon="mdiOfficeBuildingMarker" small @click="openKycModal(row)" />

                                            <BaseButton v-if="row.status == 'pending'" title="Perform Actions"
                                                @click="openActionModal(row)" :icon="mdiDotsVertical" color="info"
                                                small />
                                        </BaseButtons>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-3 text-gray-700 dark:text-slate-400">
                            <BaseButtons>
                                <BaseButton v-for="page in businesses.links" :key="page.label" :label="page.label"
                                    :active="page.active" small @click="loadBusinesses(page.url)"
                                    :disabled="!page.url" />
                            </BaseButtons>
                            <small>Page {{ businesses.current_page }} of {{ businesses.last_page }}</small> <br>
                            <small>{{ businesses.total }} total records</small>
                        </div>
                    </div>
                </CardBox>
            </CardBox>

            <!-- Business Details Modal -->
            <CardBoxModal v-model="showBusinessDetailsModal" button="primary" buttonLabel="Close"
                :title="`Business Details – ${selectedBusiness?.business_name}`" size="xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Owner -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-slate-400">
                            Owner
                        </h3>
                        <p class="text-base font-medium text-gray-800 dark:text-slate-200">
                            {{ selectedBusiness?.user?.name }}
                        </p>
                    </div>

                    <!-- State -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-slate-400">
                            State
                        </h3>
                        <p class="text-base font-medium text-gray-800 dark:text-slate-200">
                            {{ selectedBusiness?.state?.name }}
                        </p>
                    </div>

                    <!-- City -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-slate-400">
                            City
                        </h3>
                        <p class="text-base font-medium text-gray-800 dark:text-slate-200">
                            {{ selectedBusiness?.city?.name }}
                        </p>
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-slate-400">
                            Address
                        </h3>
                        <p class="text-base font-medium text-gray-800 dark:text-slate-200">
                            {{ selectedBusiness?.address }}
                        </p>
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-slate-400">
                            Postal Code
                        </h3>
                        <p class="text-base font-medium text-gray-800 dark:text-slate-200">
                            {{ selectedBusiness?.postal_code }}
                        </p>
                    </div>

                    <!-- Approval Date -->
                    <div v-if="selectedBusiness?.approved_at">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-slate-400">
                            Last Approval Date
                        </h3>
                        <p class="text-base font-medium text-gray-800 dark:text-slate-200">
                            {{ mainStore.formatDate(selectedBusiness?.approved_at) }}
                        </p>
                    </div>

                    <!-- Rejection Date -->
                    <div v-if="selectedBusiness?.rejected_at">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-slate-400">
                            Last Rejected Date
                        </h3>
                        <p class="text-base font-medium text-gray-800 dark:text-slate-200">
                            {{ mainStore.formatDate(selectedBusiness?.rejected_at) }}
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-slate-400">
                            Status
                        </h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" :class="{
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200':
                                selectedBusiness?.status === 'pending',
                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200':
                                selectedBusiness?.status === 'approved',
                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200':
                                selectedBusiness?.status === 'rejected'
                        }">
                            {{ selectedBusiness?.status }}
                        </span>
                    </div>

                    <!-- Rejection Reason -->
                    <div v-if="selectedBusiness?.rejection_reason" class="md:col-span-2">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-slate-400">
                            Previous Rejection Reason
                        </h3>
                        <p class="text-base font-medium text-red-600 dark:text-red-400">
                            {{ selectedBusiness?.rejection_reason }}
                        </p>
                    </div>
                </div>
            </CardBoxModal>

            <!-- KYC Modal -->
            <CardBoxModal v-model="showKycModal" button="secondary" buttonLabel="Close"
                :title="`KYC Documents – ${selectedKyc?.business_name}`">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Certificate -->
                    <div class="p-4 border rounded-xl shadow-sm flex flex-col items-start bg-gray-50 dark:bg-slate-700 dark:border-slate-600"
                        v-if="selectedKyc?.certificate">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="mdi mdi-file-document-outline text-blue-600 dark:text-blue-400 text-xl"></i>
                            <h5 class="font-semibold text-gray-800 dark:text-slate-200">
                                Certificate of Incorporation
                            </h5>
                        </div>
                        <a :href="selectedKyc.certificate_url" target="_blank"
                            class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition">Preview
                            Document</a>
                    </div>

                    <!-- MOA -->
                    <div class="p-4 border rounded-xl shadow-sm flex flex-col items-start bg-gray-50 dark:bg-slate-700 dark:border-slate-600"
                        v-if="selectedKyc?.moa">
                        <div class="flex items-center space-x-2 mb-2">
                            <i
                                class="mdi mdi-file-document-edit-outline text-green-600 dark:text-green-400 text-xl"></i>
                            <h5 class="font-semibold text-gray-800 dark:text-slate-200">
                                Memorandum & Articles of Association
                            </h5>
                        </div>
                        <a :href="selectedKyc.moa_url" target="_blank"
                            class="px-3 py-1 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 transition">Preview
                            Document</a>
                    </div>

                    <!-- COSC -->
                    <div class="p-4 border rounded-xl shadow-sm flex flex-col items-start bg-gray-50 dark:bg-slate-700 dark:border-slate-600"
                        v-if="selectedKyc?.cosc">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="mdi mdi-account-group-outline text-purple-600 dark:text-purple-400 text-xl"></i>
                            <h5 class="font-semibold text-gray-800 dark:text-slate-200">
                                Certificate of Share Capital
                            </h5>
                        </div>
                        <a :href="selectedKyc.cosc_url" target="_blank"
                            class="px-3 py-1 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 transition">Preview
                            Document</a>
                    </div>

                    <!-- Valid ID -->
                    <div class="p-4 border rounded-xl shadow-sm flex flex-col items-start bg-gray-50 dark:bg-slate-700 dark:border-slate-600"
                        v-if="selectedKyc?.valid_id">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="mdi mdi-card-account-details-outline text-red-600 dark:text-red-400 text-xl"></i>
                            <h5 class="font-semibold text-gray-800 dark:text-slate-200">
                                Valid ID ({{ selectedKyc.valid_id_type }})
                            </h5>
                        </div>
                        <a :href="selectedKyc.valid_id_url" target="_blank"
                            class="px-3 py-1 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 transition">Preview
                            Document</a>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!selectedKyc?.certificate && !selectedKyc?.moa && !selectedKyc?.cosc && !selectedKyc?.valid_id"
                    class="text-center py-6 text-gray-500 dark:text-slate-400">
                    <i class="mdi mdi-alert-circle-outline text-3xl mb-2"></i>
                    <p>No KYC documents uploaded for this business.</p>
                </div>
            </CardBoxModal>

            <!-- Actions Modal -->
            <CardBoxModal v-model="showActionModal" button="secondary" buttonLabel="Close"
                :title="`Choose Action for ${selectedBusiness?.business_name}`">
                <div class="space-y-6">
                    <!-- Approve -->
                    <div class="border rounded p-4 bg-green-50 dark:bg-green-900 dark:border-green-700">
                        <h5 class="font-semibold mb-2 text-gray-800 dark:text-slate-200">
                            Approve Business
                        </h5>
                        <p class="text-sm text-gray-600 dark:text-slate-400 mb-3">
                            Approve this business registration to allow the merchant to
                            transact.
                        </p>
                        <BaseButton color="success" label="Approve" class="w-full" :disabled="mainStore.processing"
                            @click="approveBusiness" />
                    </div>

                    <!-- Reject -->
                    <div class="border rounded p-4 bg-yellow-50 dark:bg-yellow-900 dark:border-yellow-700">
                        <h5 class="font-semibold mb-2 text-gray-800 dark:text-slate-200">
                            Reject Business
                        </h5>
                        <p class="text-sm text-gray-600 dark:text-slate-400 mb-3">
                            Provide a reason for rejecting this registration.
                        </p>
                        <FormField label="Rejection Reason">
                            <FormControl v-model="rejectionReason" placeholder="Enter reason..." type="text" required />
                        </FormField>
                        <BaseButton color="warning" label="Reject" class="w-full"
                            :disabled="!rejectionReason || mainStore.processing"
                            @click="rejectBusiness(rejectionReason)" />
                    </div>

                    <!-- Delete -->
                    <div class="border rounded p-4 bg-red-50 dark:bg-red-900 dark:border-red-700">
                        <h5 class="font-semibold mb-2 text-gray-800 dark:text-slate-200">
                            Delete Business
                        </h5>
                        <p class="text-sm text-gray-600 dark:text-slate-400 mb-3">
                            This will permanently delete the business and its user account.
                            Use with caution.
                        </p>
                        <BaseButton color="danger" label="Delete" class="w-full" :disabled="mainStore.processing"
                            @click="deleteBusiness" />
                    </div>
                </div>
            </CardBoxModal>
        </SectionMain>
    </LayoutAuthenticated>
</template>
