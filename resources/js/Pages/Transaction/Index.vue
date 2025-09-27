<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useMainStore } from '@/Stores/main'
import { useAxios } from '@/composables/axios.js'
import { Head, useForm } from '@inertiajs/vue3'
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
import { mdiCashMultiple, mdiDotsVertical, mdiEye } from '@mdi/js'

const mainStore = useMainStore()

const props = defineProps({
    trans_id: {},
    status: String,
})

const transactions = ref({})
const showActionModal = ref(false)
const showDetailsModal = ref(false)
const selectedTransaction = ref(null)
const rejectionReason = ref('')

const statusOptions = ref({

    pending_request: 'pending_request',
    pending_payment: 'pending_payment',
    payment_proof_submitted: 'payment_proof_submitted',
    payment_verified: 'payment_verified',
    processing: 'processing',
    completed: 'completed',
    rejected: 'rejected',
    expired: 'expired',
    all: 'all'
})

// Filters
const txProps = ref({
    page: 1,
    length: 10,
    status: props.status == null ? 'all' : props.status,
    trans_id: props.trans_id,

    from_currency: '',
    to_currency: '',
    amount_to_receive: '',
    amount_to_pay: '',
    final_amount_to_pay: '',
    date: '',
    start_date: '',
    end_date: '',
})

function viewPaymentProof() {
    if (!selectedTransaction.value?.payment_proof_path) return
    // open in new tab:
    window.open(`/storage/${selectedTransaction.value.payment_proof_path}`, '_blank')
    // OR if you want to navigate to a dedicated page:
    // router.visit(route('admin.transactions.paymentProof', selectedTransaction.value.id))
}


// Load transactions
const loadTransactions = async (url = null) => {
    if (url) {
        const urlParams = new URLSearchParams(url)
        txProps.value.page = urlParams.get('page')
    }
    const { response, error } = await useAxios(route('client.transactions.all'), 'Loading transactions...', txProps.value)
    if (response?.value?.data) {
        transactions.value = response.value.data
    } else if (error?.value) {
        mainStore.toast = 'Failed to load transactions'
    }
}

const clearFilters = () => {
    txProps.value = {
        page: 1,
        length: 10,
        status: 'all',
        trans_id: '',

        from_currency: '',
        to_currency: '',
        amount_to_receive: '',
        amount_to_pay: '',
        final_amount_to_pay: '',
        date: '',
        start_date: '',
        end_date: '',
    }
}


const statusSteps = [
    { key: 'pending_request', label: 'Request Submitted' },
    { key: 'pending_payment', label: 'Invoice Sent / Waiting Payment' },
    { key: 'payment_proof_submitted', label: 'Proof Uploaded' },
    { key: 'payment_verified', label: 'Payment Verified' },
    { key: 'processing', label: 'Processing Transfer' },
    { key: 'completed', label: 'Completed' },
    { key: 'rejected', label: 'Rejected' },
    { key: 'expired', label: 'Expired' },
]


// Watch final_rate and recompute final_amount_to_pay automatically


const currentStepIndex = computed(() => {
    if (!selectedTransaction.value) return -1
    return statusSteps.findIndex(s => s.key === selectedTransaction.value.status)
})

const fetchTransaction = async (id) => {
    const { response, error } = await useAxios(route('client.transactions.show', id), 'Loading transaction details...', {}, 'get')
    if (response?.value?.data) {
        console.log(response.value.data)
        return response.value.data
    }
    if (error?.value) {
        mainStore.toast = 'Failed to load transaction details'
        return null
    }
}


const openDetailsModal = async (tx) => {
    // show loader/toast if needed
    const freshTx = await fetchTransaction(tx.id)
    if (!freshTx) return

    // update selectedTransaction
    selectedTransaction.value = freshTx

    // also update the row in the table without reloading all:
    const index = transactions.value.data.findIndex(t => t.id === tx.id)
    if (index !== -1) transactions.value.data[index] = freshTx

    // finally show modal
    showDetailsModal.value = true
}

const openActionModal = async (tx) => {
    const freshTx = await fetchTransaction(tx.id)
    if (!freshTx) return

    selectedTransaction.value = freshTx

    const index = transactions.value.data.findIndex(t => t.id === tx.id)
    if (index !== -1) transactions.value.data[index] = freshTx

    showActionModal.value = true
}




onMounted(() => {
    loadTransactions()
})
</script>


<template>
    <LayoutAuthenticated>

        <Head title="Manage Transactions" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiCashMultiple" title="Manage Transactions" main />

            <CardBox>
                <!-- Filters -->
                <CardBox isForm @submit.prevent="loadTransactions">
                    <div class="sm:grid sm:grid-cols-12 sm:gap-6">
                        <FormField class="sm:col-span-3" label="Length">
                            <FormControl v-model="txProps.length" :options="[10, 20, 50, 100]" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Status">
                            <FormCheckRadioGroup v-model="txProps.status" :options="statusOptions" type="radio" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Transaction ID">
                            <FormControl v-model="txProps.trans_id" type="text" />
                        </FormField>

                        <FormField class="sm:col-span-3" label="Amount To Receive">
                            <FormControl v-model="txProps.amount_to_receive" type="number" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Amount To Pay">
                            <FormControl v-model="txProps.amount_to_pay" type="number" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Final Amount To Pay">
                            <FormControl v-model="txProps.final_amount_to_pay" type="number" />
                        </FormField>

                        <FormField class="sm:col-span-3" label="From Currency">
                            <FormControl v-model="txProps.from_currency" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="To Currency">
                            <FormControl v-model="txProps.to_currency" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Date">
                            <FormControl v-model="txProps.date" type="date" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Start Date">
                            <FormControl v-model="txProps.start_date" type="date" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="End Date">
                            <FormControl v-model="txProps.end_date" type="date" />
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
                    <div v-if="transactions.data && transactions.data.length > 0">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Txn ID</th>

                                    <th>From</th>
                                    <th>To</th>
                                    <th>Amount To Receive</th>
                                    <th>Amount To Pay</th>
                                    <th>Final Amount To Pay</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in transactions.data" :key="row.id">
                                    <td>{{ index + 1 + ((transactions.current_page - 1) * txProps.length) }}</td>
                                    <td>{{ row.trans_id }}</td>

                                    <td>{{ row.from_currency.code }}</td>
                                    <td>{{ row.to_currency.code }}</td>
                                    <!-- show currency symbol & formatted amounts -->
                                    <td>{{ row.from_currency?.symbol }}{{
                                        mainStore.addCommas(parseFloat(row.amount_to_receive).toFixed(2)) }}</td>
                                    <td>{{ row.to_currency?.symbol }}{{
                                        mainStore.addCommas(parseFloat(row.amount_to_pay).toFixed(2)) }}</td>
                                    <td>{{ row.to_currency?.symbol }}{{
                                        mainStore.addCommas(parseFloat(row.final_amount_to_pay).toFixed(2)) ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold" :class="{
                                            'bg-yellow-100 text-yellow-800': row.status === 'pending_request',
                                            'bg-blue-100 text-blue-800': row.status === 'processing',
                                            'bg-teal-100 text-teal-800': row.status === 'payment_proof_submitted',
                                            'bg-orange-100 text-orange-800': row.status === 'pending_payment',
                                            'bg-green-100 text-green-800': row.status === 'completed',
                                            'bg-red-100 text-red-800': row.status === 'rejected',
                                            'bg-slate-100 text-slate-800': row.status === 'expired'
                                        }">
                                            {{ row.status }}
                                        </span>
                                    </td>
                                    <td>{{ mainStore.formatDate(row.created_at) }}</td>
                                    <td>
                                        <BaseButtons>
                                            <BaseButton color="success" :icon="mdiEye" small
                                                @click="openDetailsModal(row)" />
                                            <BaseButton :icon="mdiDotsVertical" color="info" small
                                                @click="openActionModal(row)" />
                                        </BaseButtons>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-3">
                            <BaseButtons>
                                <BaseButton v-for="page in transactions.links" :key="page.label" :label="page.label"
                                    :active="page.active" small @click="loadTransactions(page.url)"
                                    :disabled="!page.url" />
                            </BaseButtons>
                            <small>Page {{ transactions.current_page }} of {{ transactions.last_page }}</small>
                            <br>
                            <small>{{ transactions.total }} total records</small>
                        </div>
                    </div>
                </CardBox>
            </CardBox>

            <!-- Transaction Details Modal -->
            <CardBoxModal v-model="showDetailsModal" button="primary" buttonLabel="Close"
                :title="`Transaction Details – ${selectedTransaction?.trans_id}`" size="xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-semibold">Transaction Id</h3>
                        <p>{{ selectedTransaction?.trans_id }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold">From Currency</h3>
                        <p>{{ selectedTransaction?.from_currency.code }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">To Currency</h3>
                        <p>{{ selectedTransaction?.to_currency.code }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold">Initial Rate</h3>
                        <p>{{ selectedTransaction?.to_currency?.symbol }}{{
                            mainStore.addCommas(parseFloat(selectedTransaction?.first_rate).toFixed(2)) }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold">Final Rate</h3>
                        <p>{{ selectedTransaction?.to_currency?.symbol }}{{
                            mainStore.addCommas(parseFloat(selectedTransaction?.final_rate).toFixed(2)) }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">Amount To Receive</h3>
                        <p>{{ selectedTransaction?.from_currency?.symbol }}{{
                            mainStore.addCommas(parseFloat(selectedTransaction?.amount_to_receive).toFixed(2)) }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">Amount To Pay</h3>
                        <p>{{ selectedTransaction?.to_currency?.symbol }}{{
                            mainStore.addCommas(parseFloat(selectedTransaction?.amount_to_pay).toFixed(2)) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">Final Amount To Pay</h3>
                        <p>{{ selectedTransaction?.to_currency?.symbol }}{{
                            mainStore.addCommas(parseFloat(selectedTransaction?.final_amount_to_pay).toFixed(2))
                        }}</p>
                    </div>



                    <div v-if="invoice_expiry_minutes != null">
                        <h3 class="text-sm font-semibold">Invoice Expiry (Minutes)</h3>
                        <p>{{ selectedTransaction?.invoice_expiry_minutes }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold">Status</h3>
                        <p>{{ selectedTransaction?.status }}</p>
                    </div>

                    <div v-if="selectedTransaction?.bank_name != null">
                        <h3 class="text-sm font-semibold">Bank Name</h3>
                        <p>{{ selectedTransaction?.bank_name }}</p>
                    </div>

                    <div v-if="selectedTransaction?.account_number != null">
                        <h3 class="text-sm font-semibold">Account Number</h3>
                        <p>{{ selectedTransaction?.account_number }}</p>
                    </div>

                    <div v-if="selectedTransaction?.account_name != null">
                        <h3 class="text-sm font-semibold">Account Name</h3>
                        <p>{{ selectedTransaction?.account_name }}</p>
                    </div>

                    <div v-if="selectedTransaction?.invoice_generated_at != null">
                        <h3 class="text-sm font-semibold">Invoice Generated At</h3>
                        <p>{{ mainStore.formatDate(selectedTransaction?.invoice_generated_at) }}</p>
                    </div>

                    <div v-if="selectedTransaction?.invoice_expires_at != null">
                        <h3 class="text-sm font-semibold">Invoice Expires At</h3>
                        <p>{{ mainStore.formatDate(selectedTransaction?.invoice_expires_at) }}</p>
                    </div>

                    <div v-if="selectedTransaction?.payment_received_at != null">
                        <h3 class="text-sm font-semibold">Payment Received At</h3>
                        <p>{{ mainStore.formatDate(selectedTransaction?.payment_received_at) }}</p>
                    </div>

                    <div v-if="selectedTransaction?.receipt_generated_at != null">
                        <h3 class="text-sm font-semibold">Receipt Generated At</h3>
                        <p>{{ mainStore.formatDate(selectedTransaction?.receipt_generated_at) }}</p>
                    </div>

                    <div v-if="selectedTransaction?.status == 'completed'">
                        <h3 class="text-sm font-semibold">Completed At</h3>
                        <p>{{ mainStore.formatDate(selectedTransaction?.processed_at) }}</p>
                    </div>

                    <div v-if="selectedTransaction?.status == 'rejected'">
                        <h3 class="text-sm font-semibold">Rejected Reason</h3>
                        <p>{{ selectedTransaction?.rejection_reason }}</p>
                    </div>

                    <div v-if="selectedTransaction?.status == 'rejected'">
                        <h3 class="text-sm font-semibold">Rejected At</h3>
                        <p>{{ mainStore.formatDate(selectedTransaction?.rejected_at) }}</p>
                    </div>





                </div>


            </CardBoxModal>

            <!-- Actions Modal -->
            <CardBoxModal v-model="showActionModal" button="secondary" buttonLabel="Close"
                :title="`Actions for transaction <em class='text-emerald-500'>${selectedTransaction?.trans_id}</em>`">
                <ul class="divide-y divide-gray-200">
                    <li v-if="selectedTransaction?.invoice_number != null"
                        class="p-4 hover:bg-green-50 transition-colors cursor-pointer rounded-md bg-green-100 flex items-center justify-between mb-2">
                        <a :href="route('client.transactions.invoice', selectedTransaction?.id)" target="_blank"
                            class="flex-1 text-green-800 font-medium">
                            View Invoice
                        </a>
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </li>

                    <li v-if="selectedTransaction?.payment_proof_path != null"
                        class="p-4 hover:bg-blue-50 transition-colors cursor-pointer rounded-md bg-blue-100 flex items-center justify-between mb-2">
                        <a :href="`/storage/${selectedTransaction.payment_proof_path}`" target="_blank"
                            class="flex-1 text-blue-800 font-medium">
                            View Payment Proof
                        </a>
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </li>

                    <li v-if="selectedTransaction?.receipt_number != null"
                        class="p-4 hover:bg-yellow-50 transition-colors cursor-pointer rounded-md bg-yellow-100 flex items-center justify-between mb-2">
                        <a :href="route('client.transactions.receipt', selectedTransaction?.id)" target="_blank"
                            class="flex-1 text-yellow-800 font-medium">
                            View Receipt
                        </a>
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </li>
                </ul>
            </CardBoxModal>


        </SectionMain>
    </LayoutAuthenticated>
</template>
