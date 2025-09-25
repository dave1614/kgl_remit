<script setup>
import { ref, onMounted, computed } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import { useMainStore } from '@/Stores/main'
import dayjs from 'dayjs'

const props = defineProps({
    transaction: Object,
    business: Object,
    now: String,
    APP_NAME: String
})


// console.log(props.transaction.toCurrency)
const mainStore = useMainStore()
const remainingSeconds = ref(0)
const expired = ref(false)

onMounted(() => {
    const expiresAt = dayjs(props.transaction.invoice_expires_at)
    const startNow = dayjs(props.now)
    remainingSeconds.value = expiresAt.diff(startNow, 'second')
    expired.value = remainingSeconds.value <= 0

    const interval = setInterval(() => {
        if (remainingSeconds.value > 0) {
            remainingSeconds.value--
        } else {
            expired.value = true
            clearInterval(interval)
        }
    }, 1000)
})

const formattedCountdown = computed(() => {
    if (expired.value) return 'Expired'
    const hrs = Math.floor(remainingSeconds.value / 3600)
    const mins = Math.floor((remainingSeconds.value % 3600) / 60)
    const secs = remainingSeconds.value % 60
    return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
})

const statusColor = computed(() => {
    switch (props.transaction.status) {
        case 'pending_payment': return 'bg-yellow-100 text-yellow-800'
        case 'payment_proof_submitted': return 'bg-blue-100 text-blue-800'
        case 'completed': return 'bg-green-100 text-green-800'
        case 'expired': return 'bg-red-100 text-red-800'
        default: return 'bg-gray-100 text-gray-700'
    }
})

const form = useForm({ payment_proof: null })

function onFileChange(e) {
    form.payment_proof = e.target.files[0]
}
function submitProof() {
    // form.post(route('client.transactions.uploadProof', props.transaction.id))

    form.post(route('client.transactions.uploadProof', props.transaction.id), {
        preserveScroll: true,
        onSuccess: (page) => {

            var response = page.props.flash.data;
            if (response.success) {
                mainStore.toast = 'Submitted'

                Swal.fire({
                    icon: 'success',
                    title: 'Successfully Submitted',
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
</script>

<template>
    <Head :title="`Transaction invoice for #${transaction.trans_id}`"/>
    <div class="max-w-4xl mx-auto bg-white shadow p-8 rounded-2xl text-gray-700">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6 border-b pb-4">
            <div class="flex items-center space-x-3">
                <!-- <img src="/logo.png" alt="Logo" class="h-12"> -->
                <div>
                    <h1 class="text-xl font-bold">{{ APP_NAME }}</h1>
                    <!-- <p class="text-sm">{{ props.business?.address }}</p>
                    <p class="text-sm">{{ props.business?.postal_code }}</p> -->
                </div>
            </div>
            <div class="text-right">
                <div class="font-semibold text-sm">Invoice #{{ props.transaction.invoice_number }}</div>
                <div :class="['mt-1 inline-block px-3 py-1 rounded-full text-xs font-semibold', statusColor]">
                    {{ props.transaction.status.replace('_', ' ') }}
                </div>
                <div v-if="props.transaction.status === 'pending_payment' && !expired" class="mt-2">
                    <div class="text-xs text-gray-500">Time left:</div>
                    <div class="text-lg font-bold text-red-600">{{ formattedCountdown }}</div>
                </div>
            </div>
        </div>

        <!-- Invoice To / Pay To -->
        <div class="sm:grid sm:grid-cols-2 gap-6 mb-6">
            <!-- Invoiced To (Business) -->
            <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg p-4 mb-6 sm:mb-0">
                <h3 class="font-semibold text-sm mb-1">Invoiced To</h3>
                <p class="text-sm font-medium">{{ props.business?.business_name }}</p>
                <p class="text-sm">{{ props.business?.address }}</p>
                <p class="text-sm">{{ props.business?.postal_code }}</p>
            </div>

            <!-- Pay To -->
            <div class="bg-green-50 border-l-4 border-green-400 rounded-lg p-4">
                <h3 class="font-semibold text-sm mb-1">Pay To</h3>
                <p class="text-sm font-medium">{{ props.transaction?.account_name }}</p>
                <p class="text-sm">{{ props.transaction?.bank_name }}</p>
                <p class="text-sm">Acct No: {{ props.transaction?.account_number }}</p>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-left border mb-6">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2">Description</th>
                    <th class="p-2">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="p-2">
                        This exact amount should be paid into the account indicated above
                    </td>
                    <td class="p-2 font-semibold">{{
                        props.transaction.to_currency?.code }} {{
                            mainStore.addCommas(parseFloat(props.transaction.final_amount_to_pay).toFixed(2)) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Upload Proof -->
        <div v-if="props.transaction.status === 'pending_payment' && !expired">
            <h3 class="font-semibold mb-2">Upload Payment Proof</h3>
            <!-- inside your component -->
            <form @submit.prevent="submitProof" class="space-y-3">
                <div>
                    <input type="file" accept=".jpg,.jpeg,.png,.pdf" @change="onFileChange"
                        class="border p-2 rounded w-full" />
                    <p v-if="form.errors.payment_proof" class="text-red-600 text-sm mt-1">
                        {{ form.errors.payment_proof }}
                    </p>
                </div>
                <button type="submit" :disabled="form.processing"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50">
                    <span v-if="form.processing">Uploading…</span>
                    <span v-else>Submit Proof</span>
                </button>
                <p v-if="$page.props.flash.success" class="text-green-600 text-sm mt-2">
                    {{ $page.props.flash.success }}
                </p>
            </form>

        </div>

        <div v-else-if="expired" class="text-red-600 font-bold text-lg mt-4">
            This invoice has expired.
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <!-- <a :href="route('client.transactions.downloadInvoice', props.transaction.id)"
                class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Download PDF</a> -->
            <a :href="route('client.dashboard')" class="bg-gray-100 px-4 py-2 rounded hover:bg-gray-200">Back</a>
        </div>
    </div>
</template>
