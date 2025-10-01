<script setup>
import { computed } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import dayjs from 'dayjs'
import { useMainStore } from '@/Stores/main'

const props = defineProps({
  transaction: Object,  // includes receipt_number, payment_received_at etc
  business: Object,     // business info
  APP_NAME: String
})

const mainStore = useMainStore()

// status badge color for receipt
const statusColor = computed(() => {
  switch (props.transaction.status) {
    case 'payment_received': return 'bg-green-100 text-green-800'
    case 'completed': return 'bg-green-100 text-green-800'
    default: return 'bg-gray-100 text-gray-700'
  }
})
</script>

<template>
    <Head :title="`Transaction receipt for #${transaction.trans_id}`"/>
  <div class="max-w-4xl mx-auto bg-white shadow p-8 rounded-2xl text-gray-700">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6 border-b pb-4">
      <div class="flex items-center space-x-3">
        <div>
          <h1 class="text-xl font-bold">{{ APP_NAME }}</h1>
          <p class="text-sm text-gray-500">Official Receipt</p>
        </div>
      </div>
      <div class="text-right">
        <div class="font-semibold text-sm">Receipt #{{ props.transaction.receipt_number }}</div>
        <div :class="['mt-1 inline-block px-3 py-1 rounded-full text-xs font-semibold', statusColor]">
          {{ props.transaction.status.replace('_',' ') }}
        </div>
        <div class="text-xs text-gray-500 mt-1">
          Date Issued: {{ dayjs(props.transaction.receipt_generated_at).format('DD MMM YYYY HH:mm') }}
        </div>
      </div>
    </div>

    <!-- Receipt To / Paid By -->
    <div class="sm:grid sm:grid-cols-2 gap-6 mb-6">
      <!-- Paid By (Business) -->
      <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg p-4 mb-6 sm:mb-0">
        <h3 class="font-semibold text-sm mb-1">Paid By</h3>
        <p class="text-sm font-medium">{{ props.business?.business_name }}</p>
        <p class="text-sm">{{ props.business?.address }}</p>
        <p class="text-sm">{{ props.business?.postal_code }}</p>
      </div>

      <!-- Received By (Your Company) -->
      <div class="bg-green-50 border-l-4 border-green-400 rounded-lg p-4">
        <h3 class="font-semibold text-sm mb-1">Received By</h3>
        <p class="text-sm font-medium">{{ APP_NAME }}</p>
        <p class="text-sm">Transaction ID: {{ props.transaction.trans_id }}</p>
        <p class="text-sm">Payment Received At: {{ dayjs(props.transaction.payment_received_at).format('DD MMM YYYY HH:mm') }}</p>
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
          <td class="p-2">Payment received for transaction ({{ props.transaction.from_currency?.code }} → {{ props.transaction.to_currency?.code }})</td>
          <td class="p-2 font-semibold">
            {{ props.transaction.to_currency?.code }}
            {{ mainStore.addCommas(parseFloat(props.transaction.final_amount_to_pay).toFixed(2)) }}
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Notes -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm">
      This receipt confirms that the payment above has been received by {{ APP_NAME }} for the stated transaction.
    </div>

    <!-- Buttons -->
    <div class="mt-6 flex justify-end space-x-3">
      <!-- Optional: Download PDF -->
      <!-- <a :href="route('client.transactions.downloadReceipt', props.transaction.id)"
          class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Download PDF</a> -->
      <button @click="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Print Receipt</button>
      <a :href="route('client.dashboard')" class="bg-gray-100 px-4 py-2 rounded hover:bg-gray-200">Back</a>
    </div>
  </div>
</template>
