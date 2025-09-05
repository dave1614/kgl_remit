<script setup>
import { useForm, usePage, Head, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from "vue";
import { useMainStore } from "@/Stores/main";
import {
    mdiAccountMultiple,
    mdiCartOutline,
    mdiChartTimelineVariant,
    mdiMonitorCellphone,
    mdiReload,
    mdiGithub,
    mdiChartPie,
    mdiWallet,
    mdiCash,
    mdiCashCheck,
    mdiTable,
    mdiOpenInNew,
    mdiFacebook,
    mdiTwitter,
    mdiWhatsapp,
    mdiAccount,
    mdiCashPlus,
    mdiCellphoneWireless,
    mdiCellphoneDock,
    mdiTelevisionClassic,
    mdiLightningBoltOutline,
    mdiRouterWireless,
    mdiSchoolOutline,
    mdiWalletBifold,
    mdiAccountGroup,
    mdiHumanGreetingProximity,
    mdiViewDashboard
} from "@mdi/js";
import * as chartConfig from "@/Components/Charts/chart.config.js";
import LineChart from "@/Components/Charts/LineChart.vue";
import SectionMain from "@/Components/SectionMain.vue";
import CardBoxWidget from "@/Components/CardBoxWidget.vue";
import CardBox from "@/Components/CardBox.vue";
import TableSampleClients from "@/Components/TableSampleClients.vue";
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
import BaseIcon from '@/Components/BaseIcon.vue';



const props = defineProps({


});

const page = usePage()
const chartData = ref(null);

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


const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.jetstream?.hasTermsAndPrivacyPolicyFeature)
const user = ref(page.props.auth.user);


</script>


<template>
    <LayoutAuthenticated>

        <Head title="Dashboard" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiViewDashboard" title="Dashboard" main>
                <!-- <BaseButton
          href="https://github.com/justboil/admin-one-vue-tailwind"
          target="_blank"
          :icon="mdiGithub"
          label="Star on GitHub"
          color="contrast"
          rounded-full
          small
        /> -->
            </SectionTitleLineWithButton>




            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4 mb-6">


                <!-- <CardBoxWidget color="text-red-500" :icon="mdiCashCheck" :number="parseFloat(user.total_withdrawan)" prefix="₦"
          label="Withdrawn" /> -->



                <!-- <CardBoxWidget color="text-primary" :icon="mdiTable" :number="total_spent_on_vtu_today" prefix="₦"
          label="Total Spent On Vtu Today" /> -->

                <!-- <CardBoxWidget color="text-slate-500" :icon="mdiCashPlus" :number="total_credited_today" prefix="₦"
          label="Total Credited Today" /> -->

                <!-- <CardBoxWidget color="text-emerald-500" :icon="mdiCashPlus" :number="totalEarnings" prefix="₦"
                    label="Total commission earned" />

                <CardBoxWidget color="text-red-500" :icon="mdiCashPlus" :number="progressiveCommission" prefix="₦"
                    label="Weekly Progressive Commission" />

                <CardBoxWidget color="text-orange-500" :icon="mdiAccountGroup" :number="total_referrals"
                    label="Eligibility Referrals" /> -->
                <!-- <CardBoxWidget
          trend="Overflow"
          trend-type="alert"
          color="text-red-500"
          :icon="mdiChartTimelineVariant"
          :number="total_amount_wthdrawn"
          suffix="%"
          label="Withdrawn"
        /> -->

            </div>





            <!-- <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-1">
        <CardBox class="">
          <UserAvatarCurrentUser class="h-24 w-24 mx-auto" />

          <div class="mt-6 text-center">
            <p class="text-xs text-gray-400 font-semibold">{{ user.email }}</p>
            <p class="text-lg text-gray-500 font-bold mt-3" v-html="`${user.phone_code}${user.phone}`"></p>
            <p class="text-2xl dark:text-white text-black font-bold mt-3" v-html="`${user.name}`"></p>
          </div>
          <div class="flex justify-center mt-6 text-center">
            <MultipurposeButton :href="route('edit_profile')" label="Edit Profile" />
          </div>

        </CardBox>
      </div> -->


            <!-- <SectionBannerStarOnGitHub class="mt-6 mb-6" /> -->

            <!-- <SectionTitleLineWithButton :icon="mdiChartPie" title="Trends overview">
        <BaseButton
          :icon="mdiReload"
          color="whiteDark"
          @click="fillChartData"
        />
      </SectionTitleLineWithButton>

      <CardBox class="mb-6">
        <div v-if="chartData">
          <line-chart :data="chartData" class="h-96" />
        </div>
      </CardBox> -->

            <!-- <SectionTitleLineWithButton :icon="mdiAccountMultiple" title="Team Perfomance" />

      <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Top 10 Mlm Earners</b>
      </NotificationBar> -->

            <!-- <CardBox has-table>
        <table>
          <thead>
            <tr>

              <th>#</th> -->
            <!-- <th /> -->
            <!-- <th>Full Name</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user,index) in top_ten_earners.data" :key="user.id"> -->
            <!-- <td>{{ i++ }}.</td> -->
            <!-- <td v-html="`${(index + 1) + ((top_ten_earners.current_page - 1) * 10)}.`"></td> -->
            <!-- <td class="border-b-0 lg:w-6 before:hidden">
                <UserAvatar :username="user.name" class="w-24 h-24 mx-auto lg:w-6 lg:h-6" />
              </td> -->
            <!-- <td data-label="Name">
                {{ user.name }}
              </td>
              <td data-label="Amount" v-html="mainStore.addCommas(user.total_earnings)">

              </td>

            </tr>
          </tbody>
        </table> -->
            <!-- <TableSampleClients /> -->


            <!-- </CardBox> -->


        </SectionMain>
    </LayoutAuthenticated>
</template>
