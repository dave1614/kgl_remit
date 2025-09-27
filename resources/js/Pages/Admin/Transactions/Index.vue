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
    user_name: '',
    business_name: '',
    email: '',
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
    const { response, error } = await useAxios(route('admin.transactions.all'), 'Loading transactions...', txProps.value)
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
        user_name: '',
        business_name: '',
        email: '',
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

const allowedActions = computed(() => {
    if (!selectedTransaction.value) return {}
    const st = selectedTransaction.value.status
    return {
        canApprove: st === 'pending_request',
        canReject: st === 'pending_request' || st === 'payment_proof_submitted',
        canMarkPaymentReceived: st === 'payment_proof_submitted',
        canMarkProcessing: st === 'payment_verified',
        canMarkCompleted: st === 'processing'
    }
})

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

const approvalForm = useForm({
    official_rate: '',
    final_rate: '',
    final_amount_to_pay: '',
    invoice_expiry_minutes: '',
    bank_name: '',
    account_number: '',
    account_name: ''
})

// Watch final_rate and recompute final_amount_to_pay automatically
watch(
    () => approvalForm.final_rate,
    (newRate) => {
        if (selectedTransaction.value && newRate) {
            // compute based on amount_to_receive and final_rate
            const baseAmount = parseFloat(selectedTransaction.value.amount_to_receive) || 0
            const rate = parseFloat(newRate) || 0
            approvalForm.final_amount_to_pay = (baseAmount * rate).toFixed(2)
        } else {
            approvalForm.final_amount_to_pay = ''
        }
    }
)

const currentStepIndex = computed(() => {
    if (!selectedTransaction.value) return -1
    return statusSteps.findIndex(s => s.key === selectedTransaction.value.status)
})

const fetchTransaction = async (id) => {
    const { response, error } = await useAxios(route('admin.transactions.show', id), 'Loading transaction details...', {}, 'get')
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
    const freshTx = await fetchTransaction(tx.id)
    if (!freshTx) return

    selectedTransaction.value = freshTx

    // update the row in table without reload
    const idx = transactions.value.data.findIndex(t => t.id === tx.id)
    if (idx !== -1) transactions.value.data[idx] = freshTx

    showDetailsModal.value = true
}

const openActionModal = async (tx) => {
    const freshTx = await fetchTransaction(tx.id)
    if (!freshTx) return

    selectedTransaction.value = freshTx

    const idx = transactions.value.data.findIndex(t => t.id === tx.id)
    if (idx !== -1) transactions.value.data[idx] = freshTx

    // now also pull approval data like before:
    const { response } = await useAxios(route('admin.transactions.approval-data', tx.id), 'Fetching details...', {}, 'get')
    if (response?.value?.data) {
        const data = response.value.data
        const defaults = data.defaults
        approvalForm.final_rate = data.rate || ''
        approvalForm.final_amount_to_pay = (parseFloat(freshTx.amount_to_receive) * parseFloat(data.rate || 0)).toFixed(2)
        approvalForm.invoice_expiry_minutes = defaults.invoice_expiry_minutes
        approvalForm.bank_name = defaults.bank_name
        approvalForm.account_number = defaults.account_number
        approvalForm.account_name = defaults.account_name
    }

    showActionModal.value = true
}


const approveTransaction = async () => {


    approvalForm.post(route('admin.transactions.approve', selectedTransaction.value.id), {
        preserveScroll: true,
        onSuccess: (page) => {

            var response = page.props.flash.data;
            if (response.success) {
                mainStore.toast = 'Submitted'

                Swal.fire({
                    icon: 'success',
                    title: 'Successfully Approved',
                    text: response.message,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                }).then((result) => {

                    if (result.isConfirmed) {
                        document.location.reload()

                    }
                })
            }
            else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong.',
                })
            }
        }, onError: (errors) => {
            var size = Object.keys(errors).length;
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: `There are ${size} form errors. Please fix them`,
            })
        },
    });
}



const rejectTransaction = async () => {
    const confirmResult = await Swal.fire({
        title: 'You are about to reject this transaction. This is irreversible. Do you wish to proceed?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes Proceed!',
    });

    if (!confirmResult.isConfirmed) return;

    const { response } = await useAxios(route('admin.transactions.reject', selectedTransaction.value.id), 'Rejecting transaction...', { reason: rejectionReason.value });

    if (response?.value.data.success) {
        mainStore.toast = 'Transaction rejected';
        await Swal.fire({
            icon: 'success',
            title: 'Successful',
            text: 'Transaction marked as rejected successfully',
            allowEscapeKey: false,
            allowOutsideClick: false
        });
        document.location.reload();
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "Something went wrong",
        });
    }
}


const markPaymentReceived = async () => {
    const { response } = await useAxios(route('admin.transactions.payment_received', selectedTransaction.value.id), 'Marking payment as received...')
    if (response?.value.data.success) {
        mainStore.toast = 'Success'


        Swal.fire({
            icon: 'success',
            title: 'Successful',
            text: 'Marked as payment received',
            allowEscapeKey: false,
            allowOutsideClick: false
        }).then((result) => {

            if (result.isConfirmed) {
                document.location.reload()

            }
        })

    } else {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "Something went wrong",
        })
    }
}

const markProcessing = async () => {
    const { response } = await useAxios(route('admin.transactions.processing', selectedTransaction.value.id), 'Marking as processing...')
    if (response?.value) {
        mainStore.toast = 'Transaction now processing'
        showActionModal.value = false
        loadTransactions()
    }
}

const markProcessed = async () => {
    const { response } = await useAxios(route('admin.transactions.payment_processed', selectedTransaction.value.id), 'Marking payment as processed...')
    if (response?.value.data.success) {
        mainStore.toast = 'Success'


        Swal.fire({
            icon: 'success',
            title: 'Successful',
            text: 'Marked as payment processed',
            allowEscapeKey: false,
            allowOutsideClick: false
        }).then((result) => {

            if (result.isConfirmed) {
                document.location.reload()

            }
        })

    } else {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "Something went wrong",
        })
    }
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

                        <FormField class="sm:col-span-3" label="Business Name">
                            <FormControl v-model="txProps.business_name" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="User Name">
                            <FormControl v-model="txProps.user_name" type="text" />
                        </FormField>




                        <FormField class="sm:col-span-3" label="Email">
                            <FormControl v-model="txProps.email" type="text" />
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
                                    <th>Business</th>
                                    <th>User</th>
                                    <th>Email</th>
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
                                    <td>{{ row.business?.name }}</td>
                                    <td>{{ row.user?.name }}</td>
                                    <td>{{ row.user?.email }}</td>
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
                                            <BaseButton v-if="row.status !== 'processed'" :icon="mdiDotsVertical"
                                                color="info" small @click="openActionModal(row)" />
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
                        <h3 class="text-sm font-semibold">Business Name</h3>
                        <p>{{ selectedTransaction?.business?.business_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">User</h3>
                        <p>{{ selectedTransaction?.user?.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">Email</h3>
                        <p>{{ selectedTransaction?.user?.email }}</p>
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

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12"></div>
                    <BaseButton v-if="selectedTransaction?.invoice_number != null" target="_blank" color="success"
                        label="View Invoice" class="col-span-6"
                        :href="route('client.transactions.invoice', selectedTransaction?.id)" />

                    <BaseButton v-if="selectedTransaction?.payment_proof_path != null" target="_blank" color="info"
                        label="View Payment Proof" class="col-span-6"
                        :href="`/storage/${selectedTransaction.payment_proof_path}`" />

                    <BaseButton v-if="selectedTransaction?.receipt_number != null" target="_blank" color="warning"
                        label="View Receipt" class="col-span-6"
                        :href="route('client.transactions.receipt', selectedTransaction?.id)" />
                </div>
            </CardBoxModal>

            <!-- Actions Modal -->
            <CardBoxModal v-model="showActionModal" button="secondary" buttonLabel="Close"
                :title="`Actions for transaction <em class='text-emerald-500'>${selectedTransaction?.trans_id}</em>`">




                <div class="space-y-6">
                    <!-- Approve -->
                    <!-- Inside CardBoxModal for Actions -->
                    <form @submit.prevent="approveTransaction" v-if="allowedActions.canApprove"
                        class="border rounded p-4 bg-green-50 space-y-3">
                        <h5 class="font-semibold mb-2">Approve & Send Invoice</h5>

                        <!-- <FormField label="Official Rate">
                            <FormControl v-model="approvalForm.official_rate" type="number"
                                :error="approvalForm.errors.official_rate" step="0.000001" />
                        </FormField> -->

                        <FormField label="Final Rate">
                            <FormControl v-model="approvalForm.final_rate" type="number"
                                :error="approvalForm.errors.final_rate" step="0.000001" />
                        </FormField>
                        <FormField label="Final Amount To Pay">
                            <FormControl v-model="approvalForm.final_amount_to_pay"
                                :error="approvalForm.errors.final_amount_to_pay" type="number" step="0.01" readonly />
                        </FormField>

                        <FormField label="Invoice Expiry (minutes)">
                            <FormControl v-model="approvalForm.invoice_expiry_minutes"
                                :error="approvalForm.errors.invoice_expiry_minutes" type="number" />
                        </FormField>
                        <FormField label="Bank Name">
                            <FormControl v-model="approvalForm.bank_name" :error="approvalForm.errors.bank_name"
                                type="text" />
                        </FormField>
                        <FormField label="Account Number">
                            <FormControl v-model="approvalForm.account_number"
                                :error="approvalForm.errors.account_number" type="number" />
                        </FormField>
                        <FormField label="Account Name">
                            <FormControl v-model="approvalForm.account_name" :error="approvalForm.errors.account_name"
                                type="text" />
                        </FormField>

                        <BaseButton color="success" label="Approve & Send Invoice" class="w-full" type="submit" />
                    </form>

                    <!-- View Payment Proof -->
                    <div v-if="selectedTransaction?.payment_proof_path" class="border rounded p-4 bg-green-50">
                        <h5 class="font-semibold mb-2">Payment Proof Uploaded</h5>

                        <!-- Thumbnail preview if image -->
                        <div v-if="selectedTransaction.payment_proof_path.match(/\.(jpg|jpeg|png)$/i)" class="mb-3">
                            <img :src="`/storage/${selectedTransaction.payment_proof_path}`" alt="Payment Proof"
                                class="h-32 rounded shadow" />
                        </div>

                        <p class="text-sm text-gray-600 mb-3">
                            Click below to open the payment proof file in a new tab.
                        </p>

                        <BaseButton color="success" label="View Payment Proof" class="w-full"
                            @click="viewPaymentProof" />
                    </div>


                    <!-- Mark Payment Received -->
                    <div v-if="allowedActions.canMarkPaymentReceived" class="border rounded p-4 bg-blue-50">
                        <h5 class="font-semibold mb-2">Mark Payment Received</h5>
                        <BaseButton color="info" label="Mark Payment Received" class="w-full"
                            @click="markPaymentReceived" />
                    </div>

                    <!-- Reject -->
                    <div v-if="allowedActions.canReject" class="border rounded p-4 bg-yellow-50">
                        <h5 class="font-semibold mb-2">Reject Transaction</h5>
                        <FormField label="Reason">
                            <FormControl v-model="rejectionReason" placeholder="Enter reason..." type="text" required />
                        </FormField>
                        <BaseButton color="warning" label="Reject" class="w-full" :disabled="!rejectionReason"
                            @click="rejectTransaction()" />
                    </div>



                    <!-- Mark Processing -->
                    <div v-if="allowedActions.canMarkProcessing" class="border rounded p-4 bg-purple-50">
                        <h5 class="font-semibold mb-2">Mark Processing</h5>
                        <BaseButton color="primary" label="Mark as Processing" class="w-full" @click="markProcessing" />
                    </div>

                    <!-- Mark Completed -->
                    <div v-if="allowedActions.canMarkCompleted" class="border rounded p-4 bg-indigo-50">
                        <h5 class="font-semibold mb-2">Mark Completed</h5>
                        <BaseButton color="info" label="Mark as Fully Processed" class="w-full"
                            @click="markProcessed" />
                    </div>
                </div>
            </CardBoxModal>

        </SectionMain>
    </LayoutAuthenticated>
</template>
