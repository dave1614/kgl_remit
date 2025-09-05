<script setup>
import { ref, reactive } from 'vue'
import { useMainStore } from '@/Stores/main'
import { Head, router } from '@inertiajs/vue3'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'

import { mdiSwapHorizontal } from '@mdi/js'

const props = defineProps({
    rates: Object,
    filters: Object,
})

const form = reactive({
    search: props.filters.search || '',
})

const applySearch = () => {
    router.get(route('client.rates.index'), { search: form.search }, { preserveState: true, replace: true })
}

const goToPage = (page) => {
    router.get(route('client.rates.index'), { search: form.search, page }, { preserveState: true, replace: true })
}
</script>

<template>
    <LayoutAuthenticated>

        <Head title="All Exchange Rates" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiSwapHorizontal" title="Available Exchange Rates" main />

            <div class="p-6">
                <!-- Header -->
                <p class="text-gray-500 mb-6">
                    Search and view the latest exchange rates for international transfers
                </p>

                <!-- Search Bar -->
                <form @submit.prevent="applySearch" class="mb-6">
                    <input v-model="form.search" type="text" placeholder="Search by currency (e.g. USD, NGN, EUR)"
                        class="w-full sm:w-96 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </form>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="rate in rates.data" :key="rate.id"
                        class="bg-white rounded-2xl shadow-md p-6 flex flex-col justify-between hover:shadow-lg transition">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-semibold text-gray-700 flex items-center space-x-2">
                                    <!-- From Currency -->
                                    <span>
                                        {{ rate.from_currency.code }}
                                        <span class="text-gray-400">{{ rate.from_currency.symbol }}</span>
                                    </span>
                                    <span class="mx-2 text-indigo-500">→</span>
                                    <!-- To Currency -->
                                    <span>
                                        {{ rate.to_currency.code }}
                                        <span class="text-gray-400">{{ rate.to_currency.symbol }}</span>
                                    </span>
                                </span>

                                <span class="bg-indigo-100 text-indigo-700 text-xs font-medium px-2 py-1 rounded-full">
                                    Live
                                </span>
                            </div>

                            <p class="text-3xl font-bold text-indigo-600 mt-3">
                                {{ rate.to_currency.symbol }}{{ parseFloat(rate.rate).toFixed(2) }}
                            </p>
                        </div>

                        <div class="mt-4">
                            <button
                                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-xl hover:bg-indigo-700 transition">
                                Transfer Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- No results -->
                <div v-if="rates.data.length === 0" class="mt-10 text-center text-gray-500">
                    No exchange rates found
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-10 space-x-2">
                    <button v-if="rates.prev_page_url" @click="goToPage(rates.current_page - 1)"
                        class="px-4 py-2 rounded-lg border bg-gray-100 hover:bg-gray-200">
                        Prev
                    </button>

                    <span class="px-4 py-2 text-gray-700">
                        Page {{ rates.current_page }} of {{ rates.last_page }}
                    </span>

                    <button v-if="rates.next_page_url" @click="goToPage(rates.current_page + 1)"
                        class="px-4 py-2 rounded-lg border bg-gray-100 hover:bg-gray-200">
                        Next
                    </button>
                </div>
            </div>
        </SectionMain>
    </LayoutAuthenticated>
</template>
