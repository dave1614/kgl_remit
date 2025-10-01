<script setup>
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import {
    mdiAccountGroup,
    mdiDomain,
    mdiCashMultiple,
    mdiReceiptText,
    mdiAlertCircle,
} from "@mdi/js";

import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBoxWidget from "@/Components/CardBoxWidget.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import CardBox from "@/Components/CardBox.vue";

const page = usePage();
const stats = computed(() => page.props.stats);
const recentUsers = computed(() => page.props.recent_users);
const recentTransactions = computed(() => page.props.recent_transactions);
const recentInvoices = computed(() => page.props.recent_invoices);
</script>

<template>
    <LayoutAuthenticated>

        <Head title="Admin Dashboard" />

        <SectionMain>
            <!-- Top Overview -->
            <SectionTitleLineWithButton :icon="mdiAlertCircle" title="System Overview" main />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <CardBoxWidget color="text-indigo-500" :icon="mdiAccountGroup" :number="stats.users"
                    label="Total Users" />
                <CardBoxWidget color="text-emerald-500" :icon="mdiDomain" :number="stats.businesses"
                    label="Businesses" />
                <CardBoxWidget color="text-blue-500" :icon="mdiCashMultiple" :number="stats.transactions"
                    label="Transactions" />
                <CardBoxWidget color="text-red-500" :icon="mdiReceiptText" :number="stats.invoices" label="Invoices" />
            </div>

            <!-- Quick Notification -->
            <NotificationBar color="warning" :icon="mdiAlertCircle">
                <b>Admin Notice:</b> You have
                {{ stats.pending_transactions }} pending transaction approvals.
            </NotificationBar>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Users -->
                <CardBox>
                    <h2 class="text-lg font-semibold mb-3">Recent Users</h2>
                    <ul>
                        <li v-for="user in recentUsers" :key="user.id" class="border-b py-2 text-sm">
                            {{ user.name }} — {{ user.email }}
                        </li>
                    </ul>
                    <a v-if="recentUsers.length" :href="route('admin.users.index')"
                        class="block mt-3 px-4 py-2 text-white text-center rounded-lg bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-indigo-500 hover:to-purple-500">
                        View all users →
                    </a>
                </CardBox>

                <!-- Transactions -->
                <CardBox>
                    <h2 class="text-lg font-semibold mb-3">Recent Transactions</h2>
                    <ul>
                        <li v-for="tx in recentTransactions" :key="tx.id" class="border-b py-2 text-sm">
                            {{ tx.trans_id }} — {{ tx.status }}
                        </li>
                    </ul>
                    <a v-if="recentTransactions.length" :href="route('admin.transactions.index')"
                        class="block mt-3 px-4 py-2 text-white text-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-indigo-500 hover:to-blue-500">
                        View all transactions →
                    </a>
                </CardBox>

                <!-- Invoices -->
                <CardBox>
                    <h2 class="text-lg font-semibold mb-3">Recent Invoices</h2>
                    <ul>
                        <li v-for="invoice in recentInvoices" :key="invoice.id" class="border-b py-2 text-sm">
                            Invoice #{{ invoice.invoice_number }} — {{ invoice.status }}
                        </li>
                    </ul>
                    <a v-if="recentInvoices.length" :href="route('admin.invoices.index')"
                        class="block mt-3 px-4 py-2 text-white text-center rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 hover:from-emerald-500 hover:to-green-500">
                        View all invoices →
                    </a>
                </CardBox>
            </div>
        </SectionMain>
    </LayoutAuthenticated>
</template>
