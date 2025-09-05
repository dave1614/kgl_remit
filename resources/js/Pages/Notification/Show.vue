<script setup>
import { usePage, Head } from '@inertiajs/vue3'
import { ref } from 'vue'

import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import BaseButton from '@/Components/BaseButton.vue'

import { mdiEmailNewsletter } from "@mdi/js"

const props = defineProps({
    notification: { type: Object, default: () => ({}) },
    auth_user: { type: Object }
})

const notification = ref({
    data: {
        subject: '',
        from: '',
        to: '',
        status: '',
        greeting: '',
        first_message: '',
        closing_message: '',
        action_button: [],
    },
    date: '',
    time: '',
    read_at_str: '',
    ...props.notification
})

const show_drop = ref(false)
</script>

<template>
  <Head :title="`Notification: ${notification.data.subject}`" />

  <LayoutAuthenticated>
    <SectionMain class="px-4 sm:px-8 py-10">
      <!-- Page Heading -->
      <SectionTitleLineWithButton
        :icon="mdiEmailNewsletter"
        :title="notification.data.subject"
        main
      />

      <div class="mt-8 max-w-4xl mx-auto">
        <!-- Notification Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-200 dark:border-slate-700 p-6 sm:p-8 space-y-6">

          <!-- Header: From / Date / Status -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                From: <span class="text-blue-600 dark:text-green-400">{{ notification.data.from }}</span>
              </h4>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Sent on {{ notification.date }} at {{ notification.time }}
              </p>
            </div>

            <div>
              <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                :class="notification.data.status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300'
                : notification.data.status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'">
                {{ notification.data.status }}
              </span>
            </div>
          </div>

          <!-- Dropdown Info -->
          <div class="relative">
            <button @click="show_drop = !show_drop" class="bg-blue-500 dark:bg-blue-600 px-4 py-2 rounded-lg shadow text-white font-medium hover:bg-blue-600 dark:hover:bg-blue-700 transition">
              View Details
            </button>
            <ul v-if="show_drop" class="absolute z-10 mt-2 w-60 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-lg p-3 space-y-1">
              <li><span class="font-semibold">From:</span> {{ notification.data.from }}</li>
              <li><span class="font-semibold">Reply-to:</span> {{ notification.data.from }}</li>
              <li><span class="font-semibold">To:</span> {{ notification.data.to }}</li>
              <li><span class="font-semibold">Date:</span> {{ notification.date }}</li>
              <li><span class="font-semibold">Time:</span> {{ notification.time }}</li>
              <li><span class="font-semibold">Status:</span> read</li>
              <li><span class="font-semibold">Read at:</span> {{ notification.read_at_str }}</li>
            </ul>
          </div>

          <!-- Body Message -->
          <div class="space-y-4 text-gray-700 dark:text-gray-300">
            <p class="text-lg font-semibold">{{ notification.data.greeting }},</p>
            <div v-html="notification.data.first_message" class="prose dark:prose-invert max-w-none"></div>

            <!-- Action Buttons -->
            <div v-for="(btn, index) in notification.data.action_button" class=""
                                                :key="index">
                                                <BaseButton v-if="btn[0] != 'Login'" target="_blank" :href="btn[1]"
                                                    type="button"
                                                    :color="index == 0 ? 'bg-green-600' : index == 1 ? 'bg-blue-500' : 'bg-orange-500'"
                                                    :label="btn[0]" class=" text-white" />
                                            </div>

            <p class="mt-6 italic text-gray-500 dark:text-gray-400">{{ notification.data.closing_message }}</p>
          </div>
        </div>
      </div>
    </SectionMain>
  </LayoutAuthenticated>
</template>
