<script setup>
import { computed } from "vue";
import { useForm, usePage, Head, Link, router } from '@inertiajs/vue3'
import { useMainStore } from "@/Stores/main";
import {
    mdiCashMinus,
    mdiCashPlus,
    mdiReceipt,
    mdiClockOutline,
    mdiCreditCardOutline,
} from "@mdi/js";

import CardBox from "@/Components/CardBox.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import PillTag from "@/Components/PillTag.vue";
import IconRounded from "@/Components/IconRounded.vue";

const props = defineProps({
    transId: String,
    date: String,
    business: String,
    name: String,
    status: String,
    // currency info
    fromCurrency: String,
    toCurrency: String,
    amountToPay: Number,
    amountToReceive: Number,
});

const mainStore = useMainStore();

const statusMeta = computed(() => {
    switch (props.status) {
        case "pending_request":
            return { label: "Pending Request", color: "gray" };
        case "pending_payment":
            return { label: "Pending Payment", color: "warning" };
        case "payment_proof_submitted":
            return { label: "Proof Submitted", color: "blue" };
        case "payment_verified":
            return { label: "Payment Verified", color: "emerald" };
        case "processing":
            return { label: "Processing", color: "indigo" };
        case "completed":
            return { label: "Completed", color: "success" };
        case "rejected":
            return { label: "Rejected", color: "danger" };
        case "expired":
            return { label: "Expired", color: "gray" };
        default:
            return { label: props.status, color: "info" };
    }
});

const icon = computed(() => {
    if (props.status === "completed") {
        return mdiCashPlus;
    } else if (props.status === "rejected" || props.status === "expired") {
        return mdiCashMinus;
    } else if (props.status === "pending_payment") {
        return mdiClockOutline;
    }
    return mdiCreditCardOutline;
});
</script>

<template>
    <CardBox @click="router.visit(route('client.transactions.index') + '?trans_id='+transId)" class="hover:shadow-md transition mb-4 last:mb-0 cursor-pointer">
        <BaseLevel>
            <!-- Left icon -->
            <IconRounded :icon="icon" :color="statusMeta.color" class="md:mr-6" />

            <!-- Middle info -->
            <div class="flex flex-col flex-grow space-y-1 md:mr-6">
                <h4 class="font-bold text-gray-800 dark:text-gray-100">
                    {{ transId }}
                </h4>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">{{ business }}</span> •
                    {{ name }}
                </p>

                <p class="text-sm text-gray-600 dark:text-gray-300">
                    <b>{{ date }}</b>
                </p>

                <div class="grid grid-cols-2 gap-2 text-sm mt-1">
                    <div class="bg-gray-50 dark:bg-slate-700 rounded-lg p-2">
                        <span class="block text-xs uppercase text-gray-400">
                            Pay In
                        </span>
                        <span class="font-semibold">
                            {{ toCurrency }} {{ mainStore.addCommas(parseFloat(amountToPay).toFixed(2)) }}
                        </span>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700 rounded-lg p-2">
                        <span class="block text-xs uppercase text-gray-400">
                            Receive
                        </span>
                        <span class="font-semibold">
                            {{ fromCurrency }} {{  mainStore.addCommas(parseFloat(amountToReceive).toFixed(2)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right status pill -->
            <div class="flex flex-col items-end space-y-2">
                <PillTag :color="statusMeta.color" :label="statusMeta.label" small />
            </div>
        </BaseLevel>
    </CardBox>
</template>
