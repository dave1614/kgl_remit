<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import { mdiFileDocumentOutline, mdiReceipt, mdiReceiptText } from '@mdi/js'
import { useMainStore } from '@/Stores/main'
import { useAxios } from '@/composables/axios.js'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import FormCheckRadioGroup from '@/Components/FormCheckRadioGroup.vue'

const mainStore = useMainStore()

// filters
const props = defineProps({
    status: String,
})

const statusOptions = ref({

    processing: 'processing',
    completed: 'completed',

    all: 'all'
})
const filters = ref({
    page: 1,
    length: 10,
    status: props.status == null ? 'all' : props.status,
    amount: '',
    receipt_number: '',
    trans_id: '',

    date: '',
    start_date: '',
    end_date: ''
})

const receipts = ref({})

const loadreceipts = async (url = null) => {
    if (url) {
        const urlParams = new URLSearchParams(url)
        filters.value.page = urlParams.get('page')
    }
    // call your API to load receipts
    const { response, error } = await useAxios(route('client.receipts.all'), 'Loading receipts...', filters.value)
    if (response?.value?.data) {
        receipts.value = response.value.data
    } else if (error?.value) {
        mainStore.toast = 'Failed to load receipts'
    }
}

const clearFilters = () => {
    filters.value = {
        page: 1,
        length: 10,
        status: props.status == null ? 'all' : props.status,
        amount: '',
        trans_id: '',
        receipt_number: '',

        date: '',
        start_date: '',
        end_date: ''
    }
}

onMounted(() => loadreceipts())
</script>

<template>
    <LayoutAuthenticated>

        <Head title="Receipts" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiReceiptText" title="Receipts Generated" main />

            <CardBox>
                <!-- Filters -->
                <form @submit.prevent="loadreceipts" class="sm:grid sm:grid-cols-12 sm:gap-6">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium">Length</label>
                        <select v-model="filters.length" class="mt-1 block w-full rounded">
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                    <FormField class="sm:col-span-3" label="Status">
                        <FormCheckRadioGroup v-model="filters.status" :options="statusOptions" type="radio" />
                    </FormField>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium">Transaction ID</label>
                        <input v-model="filters.trans_id" type="text" class="mt-1 block w-full rounded" />
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium">Receipt No.</label>
                        <input v-model="filters.receipt_number" type="text" class="mt-1 block w-full rounded" />
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium">Amount</label>
                        <input v-model="filters.amount" type="number" class="mt-1 block w-full rounded" />
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium">Issued Date</label>
                        <input v-model="filters.date" type="date" class="mt-1 block w-full rounded" />
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium">Start Date</label>
                        <input v-model="filters.start_date" type="date" class="mt-1 block w-full rounded" />
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium">End Date</label>
                        <input v-model="filters.end_date" type="date" class="mt-1 block w-full rounded" />
                    </div>
                    <div class="sm:col-span-12 mt-3">
                        <BaseButtons>
                            <BaseButton type="submit" color="info" label="Filter" />
                            <BaseButton type="reset" color="info" outline label="Clear" @click="clearFilters" />
                        </BaseButtons>
                    </div>
                </form>

                <!-- Table -->
                <CardBox has-table>
                    <div v-if="receipts.data && receipts.data.length">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Receipt #</th>
                                    <th>Txn ID</th>

                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Issued At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-xs" v-for="(row, index) in receipts.data" :key="row.id">
                                    <td>{{ index + 1 + ((receipts.current_page - 1) * filters.length) }}</td>
                                    <td class="text-xs">{{ row.receipt_number }}</td>
                                    <td class="text-xs">{{ row.trans_id }}</td>

                                    <td>{{ row.to_currency?.symbol }}{{
                                        mainStore.addCommas(parseFloat(row.final_amount_to_pay).toFixed(2)) }}</td>
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
                                    <td class="">{{ mainStore.formatDate(row.receipt_generated_at) }}</td>
                                    <td>
                                        <BaseButton color="info" small label="View Receipt"
                                            :href="route('client.transactions.receipt', row.id)" target="_blank" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <BaseButtons>
                                <BaseButton v-for="page in receipts.links" :key="page.label" :label="page.label"
                                    :active="page.active" small @click="loadreceipts(page.url)" :disabled="!page.url" />
                            </BaseButtons>
                            <small>Page {{ receipts.current_page }} of {{ receipts.last_page }}</small>
                            <br>
                            <small>{{ receipts.total }} total receipts</small>
                        </div>
                    </div>
                </CardBox>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
