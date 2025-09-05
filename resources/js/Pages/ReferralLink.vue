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

    <Head title="Referral Link" />
    <SectionMain>
      <div class="h-screen">
        <CardBox class="mb-6">
          <div class="text-center">
            <h2 class="text-2xl font-bold mb-4">Share Your Referral Link</h2>
            <div class="mb-4">
              <p class="mb-2">Your referral link:</p>
              <div class="flex items-center justify-center">
                <input
                  type="text"
                  readonly
                  :value="route('register',{u: user.user_name})"
                  class="border rounded px-3 py-2 w-full max-w-md text-center"
                />
                <BaseButton
                  :icon="mdiContentCopy"
                  color="info"
                  class="ml-2"
                  @click="mainStore.copyText(route('register',{u: user.user_name}))"
                >
                  Copy
                </BaseButton>
              </div>
            </div>

            <BaseDivider />

            <div class="mt-4">
              <h3 class="font-bold mb-3">Share on Social Media</h3>
              <div class="flex justify-center space-x-4">
                <!-- Facebook -->
                <a
                  :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(route('register',{u: user.user_name}))"
                  target="_blank"
                  class="social-btn bg-blue-600 hover:bg-blue-700"
                  title="Share on Facebook"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                  </svg>
                </a>

                <!-- Twitter/X -->
                <a
                  :href="'https://twitter.com/intent/tweet?url=' + encodeURIComponent(route('register',{u: user.user_name})) + '&text=' + encodeURIComponent(' Join me on WeConnect, Say goodbye to unnecessary expenses and Hello to Airtime and Data Freedom through an exceptional social community. Free sign up with my referral link')"
                  target="_blank"
                  class="social-btn bg-black hover:bg-gray-800"
                  title="Share on Twitter/X"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                  </svg>
                </a>

                <!-- LinkedIn -->
                <a
                  :href="'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(route('register',{u: user.user_name}))"
                  target="_blank"
                  class="social-btn bg-blue-700 hover:bg-blue-800"
                  title="Share on LinkedIn"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/>
                  </svg>
                </a>

                <!-- WhatsApp -->
                <a
                  :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent('Join me on WeConnect, Say goodbye to unnecessary expenses and Hello to Airtime and Data Freedom through an exceptional social community. Free sign up with my referral link ' + route('register',{u: user.user_name}))"
                  target="_blank"
                  class="social-btn bg-green-600 hover:bg-green-700"
                  title="Share on WhatsApp"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                  </svg>
                </a>

                <!-- Telegram -->
                <a
                  :href="'https://t.me/share/url?url=' + encodeURIComponent(route('register',{u: user.user_name})) + '&text=' + encodeURIComponent(' Join me on WeConnect, Say goodbye to unnecessary expenses and Hello to Airtime and Data Freedom through an exceptional social community. Free sign up with my referral link')"
                  target="_blank"
                  class="social-btn bg-blue-500 hover:bg-blue-600"
                  title="Share on Telegram"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>
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
