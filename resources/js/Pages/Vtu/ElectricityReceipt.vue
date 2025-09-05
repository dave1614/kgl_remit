<script setup>
import {
  mdiMonitorCellphone,
  mdiTableBorder,
  mdiTableOff,
  mdiGithub,
  mdiHospitalBuilding,
  mdiClose,
  mdiCashRefund,
  mdiWalletPlus,
mdiHistory,
} from "@mdi/js";

import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import SectionMain from "@/Components/SectionMain.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import CardBox from "@/Components/CardBox.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import FloatingActionButton from "@/Components/FloatingActionButton.vue";
import { useMainStore } from "@/Stores/main";
import { useForm, usePage, Head, Link, router } from '@inertiajs/vue3'
//import { Inertia } from '@inertiajs/inertia'
import { computed, ref, reactive, watch } from 'vue'
import _ from 'lodash';

const page = usePage();
const mainStore = useMainStore();

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = defineProps({
  details: {
    type: Object
  }
});
// const user = props.user;


const response = ref(JSON.parse(props.details.response))

</script>



<template inheritAttrs="false">
  <Head :title="`Electricity Receipt (${details.order_id})`"/>
  <div >
    <div class="px-3 pt-2 min-h-screen bg-[lightgrey] relative">
      <div class="w-full ">
        <CardBox class="sm:w-6/12 w-full sm:absolute sm:mx-auto sm:left-0 sm:right-0" >
          <div class="text-center">
            <div class="" style="border-bottom: 2px dotted lightgrey; ">
              <!-- <img src="/images/logo-img.jpeg" style="width: 80px;"> -->
              <!-- <h4 class="font-semibold mt-1" style="color: #2596be;">WeConnect</h4> -->
              <h3 class="text-4xl font-bold font-grey_qo text-center  text-primary-100">WeConnect</h3>
              <h4 class="font-bold text-primary" style="font-size: 34px;">₦ {{ mainStore.addCommas(details.amount) }}</h4>
              <h6 class="font-bold text-emerald-600" style="font-size: 15px;">Successful Transaction</h6>
              <h6 class="font-bold text-secondary">{{ details.date }} {{ details.time }}</h6>
            </div>

            <div class="py-2 text-left grid grid-cols-12 gap-6 text-sm">
              <h6 class="col-span-6">Transaction Type</h6>
              <h6 class="font-semibold col-span-6 text-right">Electricity</h6>
              <!-- <h6 class="col-span-1"></h6> -->

              <h6 class="col-span-6" >Bill Provider</h6>
              <h6 class="font-semibold col-span-6 text-right" >{{ response.data.disco }}</h6>

              <h6 class="col-span-6">Meter Number</h6>
              <h6 class="font-semibold col-span-6 text-right">{{ details.number }}</h6>

              <h6 class="col-span-6">Units</h6>
              <h6 class="font-semibold col-span-6 text-right">{{ response.data.units }}kWh</h6>

              <h6 class="col-span-6">Account Name</h6>
              <h6 class="font-semibold col-span-6 text-right">{{ response.data.name }}</h6>

              <h6 class="col-span-6">Address</h6>
              <h6 class="font-semibold col-span-6 text-right">{{ response.data.address }}</h6>

              <h6 class="col-span-6">Payment Item</h6>
              <h6 class="font-semibold col-span-6 text-right" v-html="typeof response.data.token === 'undefined' ? 'Postpaid' : 'Prepaid'"></h6>

              <h6 v-if="typeof response.data.token != 'undefined'" class="col-span-6">Token</h6>
              <h6 v-if="typeof response.data.token != 'undefined'" class="font-semibold col-span-6 text-right text-primary">{{ response.data.token }}</h6>

              <div v-if="typeof response.data.token != 'undefined' && typeof response.data.parcels != 'undefined'" class="col-span-12">

                <div v-for="(tok, index) in response.data.parcels" class="" :key="index">
                  <h6 v-show="tok.type != 'TOKEN'" class="col-span-6" >{{ tok.type }}</h6>
                  <h6 v-show="tok.type != 'TOKEN'" class="font-semibold col-span-6 text-right text-primary">{{ tok.content }}</h6>
                </div>
              </div>

              <h6 class="col-span-6">Transaction Id</h6>
              <h6 class="font-semibold col-span-6 text-right">{{ response.data.orderId }}</h6>

              <h6 class="col-span-6">Transaction Time</h6>
              <h6 class="font-semibold col-span-6 text-right">{{ response.data.vendTime }}</h6>




            </div>

          </div>


        </CardBox>


      </div>
    </div>

    <div class="" style="position: absolute; bottom: 0; display: none;">
      <div class="row">
        <div class="col-2 pr-1 text-right" style="background: #2596be;">
          <i class="fas fa-map-marker-alt" style="color: #fff;"></i>
        </div>
        <span class="font-semibold col-10" style="font-size: 10px; line-height: 1.2;">NO8B AFOLABI ESAN CLOSE, ADEXON ILEKPO BUS STOP, AKESAN LAGOS</span>
      </div>
      <div class="row">
        <div class="col-2 pr-1 text-right" style="background: #2596be;">
          <i class="fas fa-phone" style="color: #fff;"></i>
        </div>
        <span class="font-semibold col-10 mt-1" style="font-size: 10px; line-height: 1.2;">08095812402</span>
      </div>
      <div class="row">
        <div class="col-2 pr-1 text-right" style="background: #2596be;">
          <i class="fas fa-envelope" style="color: #fff;"></i>
        </div>
        <span class="font-semibold col-10" style="font-size: 10px; line-height: 1.2; text-transform: lowercase;">geeklickservices@gmail.com</span>
      </div>

    </div>
  </div>
</template>
