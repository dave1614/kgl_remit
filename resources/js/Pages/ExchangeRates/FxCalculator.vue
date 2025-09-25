<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { useAxios } from '@/composables/axios.js'
import { mdiCurrencyUsd } from '@mdi/js'

const props = defineProps({
    currencies: Array,       // all currencies
    exchangeRates: Array     // array of {from_currency_id, to_currency_id, rate}
})

const fromCurrencyId = ref(null)
const toCurrencyId = ref(null)
const amount = ref('')
const result = ref(null)
const rate = ref(null)
const fromCurrency = ref(null)
const toCurrency = ref(null)
const loading = ref(false)
// Compute only valid to-currencies for selected from-currency
const validToCurrencies = computed(() => {
    if (!fromCurrencyId.value) return []
    const fromId = Number(fromCurrencyId.value)
    const validToIds = props.exchangeRates
        .filter(r => r.from_currency_id === fromId)
        .map(r => r.to_currency_id)
    return props.currencies.filter(c => validToIds.includes(c.id))
})

const calculate = async () => {
    loading.value = true
    try {
        const res = await axios.post(route('client.fx.calculate'), {
            from_currency_id: Number(fromCurrencyId.value),
            to_currency_id: Number(toCurrencyId.value),
            amount: amount.value
        })
        rate.value = res.data.rate
        result.value = res.data.converted_amount
        fromCurrency.value = res.data.from_currency
        toCurrency.value = res.data.to_currency
    } catch (err) {
        console.error(err.response?.data ?? err)
        result.value = null
        rate.value = null
    } finally {
        loading.value = false
    }
};
</script>

<template>
    <LayoutAuthenticated>

        <Head title="FX Calculator" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiCurrencyUsd" title="FX Calculator" main />
            <CardBox>
                <p class="text-gray-600 mb-4">
                    The rates below reflect the benchmark rates and do not include any additional fees.
                </p>

                <div class="space-y-4">
                    <FormField label="Currency to Send *">
                        <select v-model="fromCurrencyId" class="p-2 border rounded w-full">
                            <option value="">Select</option>
                            <option v-for="c in props.currencies" :key="c.id" :value="c.id">
                                {{ c.code }} - {{ c.name }}
                            </option>
                        </select>
                    </FormField>

                    <FormField label="Amount">
                        <FormControl v-model="amount" type="number" placeholder="1000" />
                    </FormField>

                    <FormField label="You're Paying In *">
                        <select v-model="toCurrencyId" class="p-2 border rounded w-full" :disabled="!fromCurrencyId">
                            <option value="">Select</option>
                            <option v-for="c in validToCurrencies" :key="c.id" :value="c.id">
                                {{ c.code }} - {{ c.name }}
                            </option>
                        </select>
                    </FormField>

                    <BaseButton :disabled="loading || !validToCurrencies.length" color="success" label="Calculate"
                        class="w-full" @click="calculate" />

                    <div v-if="result" class="bg-gray-50 p-4 rounded text-center">
                        <div class="text-xl font-bold">
                            {{ fromCurrency?.symbol ?? '' }}{{ Number(amount).toLocaleString() }}
                            {{ fromCurrency?.code }}
                            =
                            {{ toCurrency?.symbol ?? '' }}{{ Number(result).toLocaleString() }}
                            {{ toCurrency?.code }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            Rate: 1 {{ fromCurrency?.code }} = {{ rate }} {{ toCurrency?.code }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            Approximate rate, final rate will be determined upon transaction approval
                        </div>
                    </div>

                </div>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
