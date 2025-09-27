<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import { mdiFileDocumentOutline } from '@mdi/js'
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


    pending_payment: 'pending_payment',
    payment_proof_submitted: 'payment_proof_submitted',
    payment_verified: 'payment_verified',
    processing: 'processing',
    completed: 'completed',
    rejected: 'rejected',
    expired: 'expired',
    all: 'all'
})
const filters = ref({
    page: 1,
    length: 10,
    status: props.status == null ? 'all' : props.status,
    amount: '',
    invoice_number: '',
    trans_id: '',

    date: '',
    start_date: '',
    end_date: ''
})

const invoices = ref({})

const loadInvoices = async (url = null) => {
    if (url) {
        const urlParams = new URLSearchParams(url)
        filters.value.page = urlParams.get('page')
    }
    // call your API to load invoices
    const { response, error } = await useAxios(route('client.invoices.all'), 'Loading invoices...', filters.value)
    if (response?.value?.data) {
        invoices.value = response.value.data
    } else if (error?.value) {
        mainStore.toast = 'Failed to load invoices'
    }
}

const clearFilters = () => {
    filters.value = {
        page: 1,
        length: 10,
        status: props.status == null ? 'all' : props.status,
        amount: '',
        trans_id: '',
        invoice_number: '',
        user_name: '',
        business_name: '',
        email: '',
        date: '',
        start_date: '',
        end_date: ''
    }
}

onMounted(() => loadInvoices())
</script>

<template>
    <LayoutAuthenticated>

        <Head title="Invoices" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiFileDocumentOutline" title="Invoices Issued" main />

            <CardBox>
                <!-- Filters -->
                <form @submit.prevent="loadInvoices" class="sm:grid sm:grid-cols-12 sm:gap-6">
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
                        <label class="block text-sm font-medium">Invoice No.</label>
                        <input v-model="filters.invoice_number" type="text" class="mt-1 block w-full rounded" />
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
                    <div v-if="invoices.data && invoices.data.length">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice #</th>
                                    <th>Txn ID</th>

                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Issued At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-xs" v-for="(row, index) in invoices.data" :key="row.id">
                                    <td>{{ index + 1 + ((invoices.current_page - 1) * filters.length) }}</td>
                                    <td class="text-xs">{{ row.invoice_number }}</td>
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
                                    <td class="">{{ mainStore.formatDate(row.invoice_generated_at) }}</td>
                                    <td>
                                        <BaseButton color="info" small label="View Invoice"
                                            :href="route('client.transactions.invoice', row.id)" target="_blank" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <BaseButtons>
                                <BaseButton v-for="page in invoices.links" :key="page.label" :label="page.label"
                                    :active="page.active" small @click="loadInvoices(page.url)" :disabled="!page.url" />
                            </BaseButtons>
                            <small>Page {{ invoices.current_page }} of {{ invoices.last_page }}</small>
                            <br>
                            <small>{{ invoices.total }} total invoices</small>
                        </div>
                    </div>
                </CardBox>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
