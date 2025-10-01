<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import { useMainStore } from "@/Stores/main";

import {
    mdiChartTimelineVariant,
    mdiCashMultiple,
    mdiReceiptText,
    mdiSwapHorizontal,
    mdiAccountCircle,
    mdiWalletOutline,
    mdiReload,
    mdiMonitorCellphone,
    mdiChartPie
} from '@mdi/js'

import SectionMain from '@/Components/SectionMain.vue'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBoxWidget from '@/Components/CardBoxWidget.vue'
import CardBox from '@/Components/CardBox.vue'
import NotificationBar from '@/Components/NotificationBar.vue'
import BaseButton from '@/Components/BaseButton.vue'
import CardBoxTransactionFull from '@/Components/CardBoxTransactionFull.vue'
import CardBoxInvoice from '@/Components/CardBoxInvoice.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import * as chartConfig from '@/Components/Charts/chart.config.js'

const page = usePage()
const mainStore = useMainStore()

// data coming from Laravel Inertia props
const user = computed(() => page.props.user)
const business = computed(() => page.props.business)
const stats = computed(() => page.props.dashboard_stats) // {transactions:0, invoices:0, rates:0, wallets:0}
const recentTransactions = computed(() => page.props.recent_transactions) // array
const recentInvoices = computed(() => page.props.recent_invoices) // array

const chartData = ref(null)

const fillChartData = () => {
    chartData.value = chartConfig.sampleChartData()
}

onMounted(() => {
    fillChartData()
})
</script>

<template>
    <LayoutAuthenticated>

        <Head title="Client Dashboard" />

        <SectionMain>
            <!-- Top Overview -->
            <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Overview" main>
                <BaseButton :icon="mdiReload" color="whiteDark" @click="fillChartData" />
            </SectionTitleLineWithButton>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <CardBoxWidget color="text-emerald-500" :icon="mdiCashMultiple" :number="stats.transactions"
                    label="My Transactions" />
                <CardBoxWidget color="text-blue-500" :icon="mdiReceiptText" :number="stats.invoices"
                    label="Invoices / Receipts" />
                <CardBoxWidget color="text-indigo-500" :icon="mdiSwapHorizontal" :number="stats.rates"
                    label="Available Rates" />
                <!-- <CardBoxWidget color="text-orange-500" :icon="mdiWalletOutline" :number="stats.wallets"
                    label="Wallet Balance" prefix="₦" /> -->
            </div>

            <!-- Notification Bar -->
            <NotificationBar color="info" :icon="mdiMonitorCellphone">
                <b>Welcome to your dashboard!</b> Here you can view an overview of your account and quick links to your
                recent activity.
            </NotificationBar>

            <!-- Recent Transactions & Invoices -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="flex flex-col space-y-3">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Recent Transactions
                    </h2>

                    <CardBoxTransactionFull v-for="tx in recentTransactions.slice(0, 5)" :key="tx.id"
                        :transId="tx.trans_id" :date="mainStore.formatDate(tx.created_at)"
                        :business="business?.business_name || 'Personal'" :name="user.user_name" :status="tx.status"
                        :fromCurrency="tx.from_currency?.code" :toCurrency="tx.to_currency?.code"
                        :amountToPay="Number(tx.final_amount_to_pay ?? tx.amount_to_pay)"
                        :amountToReceive="Number(tx.amount_to_receive)" />

                    <div v-if="!recentTransactions.length" class="text-gray-500 dark:text-gray-400 text-sm">
                        No recent transactions found.
                    </div>

                    <!-- view all link -->
                    <a v-if="recentTransactions.length > 5" :href="route('client.transactions.index')" class="inline-block mt-3 px-6 py-3 text-white font-semibold rounded-lg shadow-lg
           bg-gradient-to-r from-blue-500 to-indigo-500
           hover:from-indigo-500 hover:to-blue-500 transition-all duration-200">
                        View all transactions →
                    </a>
                </div>


                <div class="flex flex-col space-y-3">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Recent Invoices
                    </h2>

                    <CardBoxInvoice v-for="invoice in recentInvoices.slice(0, 5)" :key="invoice.id"
                        :transId="invoice.id" :invoice-number="invoice.invoice_number"
                        :date="mainStore.formatDate(invoice.created_at)"
                        :business="business ? business.business_name : 'Personal'" :name="user.user_name"
                        :status="invoice.status" :from-currency="invoice.from_currency?.code"
                        :to-currency="invoice.to_currency?.code"
                        :amount-to-pay="Number(invoice.final_amount_to_pay ?? invoice.amount_to_pay)"
                        :amount-to-receive="Number(invoice.amount_to_receive)" />

                    <div v-if="!recentInvoices.length" class="text-gray-500 dark:text-gray-400 text-sm">
                        No invoices yet.
                    </div>

                    <!-- view all link -->


                    <a v-if="recentInvoices.length > 5" :href="route('client.invoices.index')" class="inline-block mt-3 px-6 py-3 text-white font-semibold rounded-lg shadow-lg
           bg-gradient-to-r from-blue-500 to-indigo-500
           hover:from-indigo-500 hover:to-blue-500 transition-all duration-200">
                        View all invoices →
                    </a>
                </div>

            </div>

            <!-- Chart Section -->
            <!-- <SectionTitleLineWithButton :icon="mdiChartPie" title="Trends overview">
                <BaseButton :icon="mdiReload" color="whiteDark" @click="fillChartData" />
            </SectionTitleLineWithButton> -->

            <!-- <CardBox class="mb-6">
                <div v-if="chartData">
                    <line-chart :data="chartData" class="h-96" />
                </div>
            </CardBox> -->


        </SectionMain>
    </LayoutAuthenticated>
</template>
