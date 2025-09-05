<script setup>
import { usePage, Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { useMainStore } from "@/Stores/main";
import {
  mdiBell,
} from "@mdi/js";

import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseLevel from "@/Components/BaseLevel.vue";

const page = usePage();
const mainStore = useMainStore();

const user = ref(page.props.auth.user);

const props = defineProps({
  notis: {
    type: Object,
    default: {},
  },
});

const notis = ref(props.notis);
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Your Notifications" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiBell"
        :title="`Notifications`"
        main
      />

      <div>
        <div v-if="notis.data.length > 0">
          <!-- Notification Cards -->
          <div
            v-for="(noti, index) in notis.data"
            :key="index"
            @click="router.visit(route('notification.show', noti.id))"
            class="group my-3 mx-1 rounded-xl shadow-md px-5 py-4 cursor-pointer transition transform hover:scale-[1.01] hover:shadow-lg"
            :class="noti.read_at == null
              ? 'bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-pink-500/10 border-l-4 border-indigo-500'
              : 'bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600'
            "
          >
            <div class="flex justify-between items-center mb-1">
              <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-100 group-hover:text-indigo-600">
                {{ mainStore.truncateStr(noti.data.subject, 40) }}
              </h5>
              <span class="text-[11px] font-bold text-gray-500 dark:text-gray-300">
                {{ noti.social_time }}
              </span>
            </div>

            <p class="text-xs text-gray-600 dark:text-gray-300 group-hover:text-gray-800">
              {{ mainStore.truncateStr(mainStore.stripHtml(noti.data.first_message), 100) }}
            </p>
          </div>

          <!-- Pagination -->
          <div class="p-4 lg:px-6 border-t border-gray-200 dark:border-slate-600 mt-6">
            <BaseLevel>
              <BaseButtons>
                <BaseButton
                  v-for="page in notis.links"
                  :key="page"
                  :active="page.active"
                  :label="page.label"
                  :color="page.active ? 'indigo' : 'whiteDark'"
                  small
                  :href="page.url"
                  :disabled="page.url === null"
                />
              </BaseButtons>
              <div class="text-xs text-gray-500 dark:text-gray-300 space-y-1">
                <p>Page {{ notis.current_page }} of {{ notis.last_page }}</p>
                <p>{{ notis.total }} total notifications</p>
              </div>
            </BaseLevel>
          </div>
        </div>
        <div v-else class="text-center py-10 text-gray-500 dark:text-gray-400">
          No notifications yet 🎉
        </div>
      </div>
    </SectionMain>
  </LayoutAuthenticated>
</template>
