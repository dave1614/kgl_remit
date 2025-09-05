<script setup>
import { ref, onMounted } from 'vue'
import { useMainStore } from '@/Stores/main'
import { useAxios } from '@/composables/axios.js'
import { Head } from '@inertiajs/vue3'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import CardBoxModal from '@/Components/CardBoxModal.vue'
import BaseButton from '@/Components/BaseButton.vue'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import BaseDivider from '@/Components/BaseDivider.vue'
import { mdiCurrencyUsd, mdiPlus, mdiPencil, mdiTrashCan } from '@mdi/js'

const props = defineProps({
    currencies: Array
})

const mainStore = useMainStore()

const errors = ref({})
const exchangeRates = ref({})
const selectedRate = ref(null)
const showModal = ref(false)
const showDeleteModal = ref(false)

const filters_ref = ref({
    page: 1,
    length: 10,
    from_currency_id: '',
    to_currency_id: '',
    rate_min: null,
    rate_max: null,
})

const form = ref({
    from_currency_id: '',
    to_currency_id: '',
    rate: ''
})

const loadRates = async (url = null) => {
    if (url) {
        const urlParams = new URLSearchParams(url)
        filters_ref.value.page = urlParams.get('page') || 1
    }
    const { response, error } = await useAxios(route('admin.rates.get_current'), 'Loading exchange rates...', filters_ref.value)
    if (response?.value?.data) {
        exchangeRates.value = response.value.data
    } else if (error?.value) {
        mainStore.toast = 'Failed to load exchange rates'
    }
}

const openModal = (rate = null) => {
    selectedRate.value = rate
    if (rate) {
        form.value.from_currency_id = rate.from_currency_id
        form.value.to_currency_id = rate.to_currency_id
        form.value.rate = rate.rate
    } else {
        form.value.from_currency_id = ''
        form.value.to_currency_id = ''
        form.value.rate = ''
    }
    showModal.value = true
}

const saveRate = async () => {
    errors.value = {}
    const payload = { ...form.value }

    try {
        if (selectedRate.value) {
            await useAxios(route('admin.rates.update', selectedRate.value.id), 'Updating rate...', payload)
            mainStore.toast = 'Rate updated successfully'
        } else {
            await useAxios(route('admin.rates.store'), 'Creating rate...', payload)
            mainStore.toast = 'Rate created successfully'
        }

        showModal.value = false
        loadRates()
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors
        } else {
            mainStore.toast = 'Something went wrong'
            console.error(err)
        }
    }
}



const deleteRate = async () => {
    if (!selectedRate.value) return
    await useAxios(route('admin.rates.destroy', selectedRate.value.id), 'Deleting rate...')
    mainStore.toast = 'Rate deleted'
    showDeleteModal.value = false
    loadRates()
}

onMounted(() => loadRates())
</script>

<template>
    <LayoutAuthenticated>

        <Head title="Manage Exchange Rates" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiCurrencyUsd" title="Manage Exchange Rates" main>
                <BaseButton color="success" :icon="mdiPlus" label="Add New Rate" small @click="openModal()" />
            </SectionTitleLineWithButton>

            <CardBox>
                <!-- Filters -->
                <CardBox isForm @submit.prevent="loadRates">
                    <div class="sm:grid sm:grid-cols-12 sm:gap-6">
                        <FormField class="sm:col-span-3" label="From Currency">
                            <select v-model="filters_ref.from_currency_id" class="p-2 border rounded w-full">
                                <option value="">Any</option>
                                <option v-for="c in props.currencies" :key="c.id" :value="c.id">{{ c.code }} - {{ c.name
                                }}</option>
                            </select>
                        </FormField>
                        <FormField class="sm:col-span-3" label="To Currency">
                            <select v-model="filters_ref.to_currency_id" class="p-2 border rounded w-full">
                                <option value="">Any</option>
                                <option v-for="c in props.currencies" :key="c.id" :value="c.id">{{ c.code }} - {{ c.name
                                }}</option>
                            </select>
                        </FormField>
                        <FormField class="sm:col-span-3" label="Min Rate">
                            <FormControl v-model="filters_ref.rate_min" type="number" step="0.0001" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Max Rate">
                            <FormControl v-model="filters_ref.rate_max" type="number" step="0.0001" />
                        </FormField>
                    </div>

                    <BaseButtons class="mt-5 mb-2">
                        <BaseButton type="submit" color="info" label="Filter" />
                        <BaseButton type="reset" color="info" outline label="Clear"
                            @click="() => { filters_ref = { page: 1, length: 10 } }" />
                    </BaseButtons>
                    <BaseDivider />
                </CardBox>

                <!-- Listing -->
                <CardBox has-table>
                    <div v-if="exchangeRates.data && exchangeRates.data.length > 0">
                        <div v-for="(rate, index) in exchangeRates.data" :key="rate.id"
                            class="p-4 border-b flex justify-between">
                            <div>
                                <div class="font-bold">{{ rate.from_currency.code }} → {{ rate.to_currency.code }}</div>
                                <div class="text-sm text-gray-500">{{ rate.from_currency.name }} → {{
                                    rate.to_currency.name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold">{{ rate.rate }}</div>
                                <BaseButtons>
                                    <BaseButton color="info" :icon="mdiPencil" small @click="openModal(rate)" />
                                    <BaseButton color="danger" :icon="mdiTrashCan" small
                                        @click="() => { selectedRate = rate; showDeleteModal = true }" />
                                </BaseButtons>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            <BaseButtons>
                                <BaseButton v-for="page in exchangeRates.links" :key="page.label" :label="page.label"
                                    :active="page.active" small @click="loadRates(page.url)" :disabled="!page.url" />
                            </BaseButtons>
                        </div>
                    </div>
                </CardBox>
            </CardBox>

            <!-- Add/Edit Modal -->
            <CardBoxModal v-model="showModal" button="secondary" buttonLabel="Close"
                :title="selectedRate ? 'Edit Exchange Rate' : 'Add Exchange Rate'">
                <div class="space-y-4">
                    <FormField label="From Currency">
                        <select v-model="form.from_currency_id" class="p-2 border rounded w-full">
                            <option value="">Select currency</option>
                            <option v-for="c in props.currencies" :key="c.id" :value="c.id">{{ c.code }} - {{ c.name }}
                            </option>
                        </select>

                    </FormField>
                    <p v-if="errors.from_currency_id" class="text-red-600 text-sm">{{ errors.from_currency_id[0] }}
                        </p>
                    <FormField label="To Currency">
                        <select v-model="form.to_currency_id" class="p-2 border rounded w-full">
                            <option value="">Select currency</option>
                            <option v-for="c in props.currencies" :key="c.id" :value="c.id">{{ c.code }} - {{ c.name }}
                            </option>
                        </select>

                    </FormField>
                    <p v-if="errors.to_currency_id" class="text-red-600 text-sm w-full">{{ errors.to_currency_id[0] }}</p>
                    <FormField label="Rate">
                        <FormControl v-model="form.rate" type="number" step="0.0001" placeholder="0.95" />

                    </FormField>
                    <p v-if="errors.rate" class="text-red-600 text-sm">{{ errors.rate[0] }}</p>
                    <BaseButton color="success" label="Save" class="w-full" @click="saveRate" />
                </div>
            </CardBoxModal>

            <!-- Delete Modal -->
            <CardBoxModal v-model="showDeleteModal" button="secondary" buttonLabel="Close" title="Confirm Delete">
                <p>Are you sure you want to delete this exchange rate?</p>
                <BaseButton color="danger" label="Delete" class="w-full mt-3" @click="deleteRate" />
            </CardBoxModal>
        </SectionMain>
    </LayoutAuthenticated>
</template>
