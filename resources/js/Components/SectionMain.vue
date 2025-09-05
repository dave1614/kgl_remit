<script setup>
import {
    mdiMonitorCellphone,
    mdiTableBorder,
    mdiTableOff,
    mdiGithub,
    mdiHospitalBuilding,
    mdiClose,
    mdiCellphoneWireless,
    mdiAccountCog,
    mdiAccountCash,
    mdiCurrencyNgn,
    mdiPhone,
    mdiWhatsapp,
} from "@mdi/js";
import { containerMaxW } from "@/config.js";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
</script>

<template>
    <section class="p-6" :class="containerMaxW">


        <slot />
    </section>
</template>
