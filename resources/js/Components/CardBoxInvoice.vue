<script setup>
import {ref, computed} from 'vue'
import { useForm, usePage, Head, Link, router } from '@inertiajs/vue3'
import CardBox from "@/Components/CardBox.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import PillTag from "@/Components/PillTag.vue";
import IconRounded from "@/Components/IconRounded.vue";
import { useMainStore } from "@/Stores/main";
import {
    mdiReceiptText,
    mdiCashPlus,
    mdiCashMinus,
    mdiClockOutline,
} from "@mdi/js";

const props = defineProps({
    transId: {

        required: true,
    },
    invoiceNumber: {
        type: String,
        required: true,
    },
    date: {
        type: String,
        required: true,
    },
    business: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
    status: {
        type: String,
        required: true,
    },
    fromCurrency: {
        type: String,
        required: true,
    },
    toCurrency: {
        type: String,
        required: true,
    },
    amountToPay: {
        type: Number,
        required: true,
    },
    amountToReceive: {
        type: Number,
        required: true,
    },
});

const mainStore = useMainStore()

// pick icon/color based on status
const icon = computed(() => {
    if (props.status === "paid" || props.status === "payment_verified") {
        return { icon: mdiCashPlus, type: "success" };
    } else if (props.status === "expired" || props.status === "rejected") {
        return { icon: mdiCashMinus, type: "danger" };
    }
    return { icon: mdiReceiptText, type: "info" };
});
</script>

<template>
    <CardBox @click="router.visit(route('client.transactions.invoice', transId))" class="mb-6 last:mb-0 cursor-pointer" is-hoverable>
        <BaseLevel>
            <!-- Left section -->
            <BaseLevel type="justify-start">
                <IconRounded :icon="icon.icon" :color="icon.type" class="md:mr-6" />
                <div class="text-left space-y-1 md:mr-6">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Invoice #{{ invoiceNumber }}
                    </h4>
                    <p class="text-gray-500 dark:text-slate-400 text-sm">
                        <b>{{ date }}</b> – {{ business }}
                    </p>
                    <p class="text-gray-500 dark:text-slate-400 text-sm">
                        {{ fromCurrency }} → {{ toCurrency }}
                    </p>
                </div>
            </BaseLevel>

            <!-- Right section -->
            <div class="text-right space-y-2">
                <p class="text-sm text-gray-500">By: {{ name }}</p>
                <p class="text-gray-700 dark:text-gray-200 text-md font-bold">
                    Pay: ₦{{ mainStore.addCommas(parseFloat(amountToPay).toFixed(2)) }}
                </p>
                <p class="text-gray-700 dark:text-gray-200 text-md">
                    Receive: ₦{{ mainStore.addCommas(parseFloat(amountToReceive).toFixed(2)) }}
                </p>
                <PillTag :color="icon.type" :label="status" small />
            </div>
        </BaseLevel>
    </CardBox>
</template>
