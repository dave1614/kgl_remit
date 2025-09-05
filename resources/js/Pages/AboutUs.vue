<script setup>
import { useForm, usePage, Head } from '@inertiajs/vue3';
import { computed, ref, onMounted } from "vue";
import { useMainStore } from "@/Stores/main";
import {
  mdiAccountEdit,
  mdiAccount,
  mdiMail,
  mdiBellBadge,
  mdiClose,
  mdiAlertCircle,
  mdiContentCopy
} from "@mdi/js";
import * as chartConfig from "@/Components/Charts/chart.config.js";
import LineChart from "@/Components/Charts/LineChart.vue";
import SectionMain from "@/Components/SectionMain.vue";
import CardBoxWidget from "@/Components/CardBoxWidget.vue";
import CardBox from "@/Components/CardBox.vue";
import TableSampleClients from "@/Components/TableSampleClients.vue";
import CustomNotificationBar from "@/Components/CustomNotificationBar.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import CardBoxTransaction from "@/Components/CardBoxTransaction.vue";
import CardBoxClient from "@/Components/CardBoxClient.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import SectionBannerStarOnGitHub from "@/Components/SectionBannerStarOnGitHub.vue";
import UserCard from "@/Components/UserCard.vue";
import UserAvatarCurrentUser from "@/Components/UserAvatarCurrentUser.vue";
import MultipurposeButton from '@/Components/MultipurposeButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import FormField from '@/Components/FormField.vue';
import FormControl from '@/Components/FormControl.vue';
import UserAvatar from '@/Components/UserAvatar.vue';
import BaseDivider from '@/Components/BaseDivider.vue';
import FormLoaderDark from '@/Loaders/form_loader_dark.gif'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'



const page = usePage()
const chartData = ref(null);
const btn_hovered = ref(false);
const errors_size = ref(0);





const fillChartData = () => {
  chartData.value = chartConfig.sampleChartData();
};

const mainStore = useMainStore();
onMounted(() => {
  fillChartData();
  // mainStore.changeIsAdminVal(true)
});

const i = 1;


const clientBarItems = computed(() => mainStore.clients.slice(0, 4));

const transactionBarItems = computed(() => mainStore.history);


const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const user = page.props.user;




const referralLinkSwal = () => {
  const text = `${route('register',{u: user.user_name})}`
  Swal.fire({
    title: 'Referral Link',
    html: `Your referral link is ${text} <br> <b>Click the copy button to copy to clipboard.</b>`,
    icon: 'success',

    allowEscapeKey: false,
    allowOutsideClick: false,
  }).then((result) => {
    if (result.isConfirmed) {
      mainStore.copyText(text)

      mainStore.toast = 'Referral link copied to clipboard'

      // referralLinkSwal()
    }
  });
}

// referralLinkSwal();
</script>

<template>
  <LayoutAuthenticated>

    <Head title="About Us" />
    <SectionMain>
      <div class="h-screen relative">
        <CardBox class="mb-6 absolute mx-auto left-0 right-0 sm:w-6/12 w-full">
          <h3 class="text-3xl font-bold">Revolution! Airtime and Data</h3>

          <p class="mt-[40px]">Join the revolution today where we collectively solve your Airtime and Data needs. Imagine no longer having to spend money again on data and airtime at some point when you Connect with WeConnect , a social with a fantastic commission system that earns you money simply by doing what you would have done anywhere.<br><br>

          Say goodbye to unnecessary expenses and Hello to Airtime and Data Freedom at some point, by connecting with our exceptional social community and using our services. <br><br>

          Join WeConnect today, enjoy unbeatable prices and excellent services. Enjoy our N2,000 Free Sign Up bonus when you use and invite others and over time enjoy Free Airtime and Data For Life. Time matters, click to take your position now.</p>
        </CardBox>
      </div>


    </SectionMain>
  </LayoutAuthenticated>
</template>

<style>
.social-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  color: white;
  transition: all 0.3s ease;
}

.social-btn:hover {
  transform: scale(1.1);
}
</style>
