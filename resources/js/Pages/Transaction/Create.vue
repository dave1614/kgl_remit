<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { useMainStore } from '@/Stores/main'
import { useAxios } from '@/composables/axios.js'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { mdiSwapHorizontal, mdiCurrencyUsd } from '@mdi/js'

const props = defineProps({
    currencies: Array,
    exchangeRates: Array
})

const mainStore = useMainStore()

const fromCurrencyId = ref('')
const toCurrencyId = ref('')
const amount = ref('')
const rate = ref(null)
const convertedAmount = ref(null)
const fromCurrency = ref(null)
const toCurrency = ref(null)
const loading = ref(false)
const submitting = ref(false)

// valid "to" currencies based on selected "from"
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
        convertedAmount.value = res.data.converted_amount
        fromCurrency.value = res.data.from_currency
        toCurrency.value = res.data.to_currency
    } catch (err) {
        console.error(err.response?.data ?? err)
        convertedAmount.value = null
        rate.value = null
    } finally {
        loading.value = false
    }
}

// const submitTransaction = async () => {
//     submitting.value = true
//     try {
//         await router.post(route('client.transactions.store'), {
//             from_currency_id: Number(fromCurrencyId.value),
//             to_currency_id: Number(toCurrencyId.value),
//             amount_to_receive: amount.value,
//             amount_to_pay: convertedAmount.value // the pre-calculated amount
//         })
//     } finally {
//         submitting.value = false
//     }
// }



const submitTransaction = async () => {
    // confirmation first
    const result = await Swal.fire({
        title: 'Confirm Transaction',
        text: 'Are you sure you want to submit this transaction?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, submit',
        cancelButtonText: 'Cancel'
    })
    if (!result.isConfirmed) return

    submitting.value = true
    try {
        // call axios directly or use router.post with preserveState:false
        // const { data } = await axios.post(route('client.transactions.store'), {
        //     from_currency_id: Number(fromCurrencyId.value),
        //     to_currency_id: Number(toCurrencyId.value),
        //     amount_to_receive: amount.value,
        //     amount_to_pay: convertedAmount.value
        // })

        const { response } = await useAxios(route('client.transactions.store'), 'Processing your transaction request...', {
            from_currency_id: Number(fromCurrencyId.value),
            to_currency_id: Number(toCurrencyId.value),
            amount_to_receive: amount.value,
            amount_to_pay: convertedAmount.value
        }, 'post');

        // console.log(response.value)
        const data = response.value.data

        if(data.success){

            // show toast
            mainStore.toast = 'Successful'

            // now show success Swal AFTER response is done
            await Swal.fire({
                icon: 'success',
                title: 'Submitted',
                text: data.message ?? `Transaction request has been submitted successfully to admin. You'll be notified with updates.`,
                allowEscapeKey: false,
                allowOutsideClick: false
            })

            // optional reload or route change
            document.location.reload()
        }else{
            // show toast
            mainStore.toast = 'Error'

            // show error Swal
            await Swal.fire({
                icon: 'error',
                title: 'Oops…',
                text: data.message ?? `Something went wrong while submitting your transaction.`
            })
        }
        // or router.visit(route('profile.show', user.slug))
    } catch (err) {
        console.error(err)
        await Swal.fire({
            icon: 'error',
            title: 'Oops…',
            text: 'Something went wrong while submitting your transaction.'
        })
    } finally {
        submitting.value = false
    }
}


</script>

<template>
    <LayoutAuthenticated>

        <Head title="Initiate Transaction" />

        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiSwapHorizontal" title="Initiate New Transaction" main />
            <CardBox class="bg-gradient-to-br from-green-50 via-white to-blue-50 shadow-md">
                <p class="text-gray-700 mb-4">
                    Choose the currencies, enter the amount you want to transfer, then review and submit.
                </p>

                <div class="space-y-5">
                    <FormField label="Currency to Send *">
                        <select v-model="fromCurrencyId"
                            class="p-3 border rounded w-full focus:ring-2 focus:ring-green-400">
                            <option value="">Select</option>
                            <option v-for="c in props.currencies" :key="c.id" :value="c.id">
                                {{ c.code }} - {{ c.name }}
                            </option>
                        </select>
                    </FormField>

                    <FormField label="Amount You Want Beneficiary to Receive">
                        <FormControl v-model="amount" type="number" placeholder="1000" class="focus:ring-green-400" />
                    </FormField>

                    <FormField label="Currency You Will Pay In *">
                        <select v-model="toCurrencyId"
                            class="p-3 border rounded w-full focus:ring-2 focus:ring-blue-400"
                            :disabled="!fromCurrencyId">
                            <option value="">Select</option>
                            <option v-for="c in validToCurrencies" :key="c.id" :value="c.id">
                                {{ c.code }} - {{ c.name }}
                            </option>
                        </select>
                    </FormField>

                    <BaseButton :disabled="loading || !validToCurrencies.length" color="success" label="Calculate"
                        class="w-full" @click="calculate" />

                    <div v-if="convertedAmount" class="bg-green-50 border border-green-200 p-4 rounded text-center">
                        <div class="text-xl font-bold text-green-700">
                            You will pay:
                            {{ toCurrency?.symbol ?? '' }}{{ Number(convertedAmount).toLocaleString() }}
                            {{ toCurrency?.code }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            Rate: 1 {{ fromCurrency?.code }} = {{ rate }} {{ toCurrency?.code }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            This is an indicative amount. Final amount may change on admin review.
                        </div>
                    </div>

                    <BaseButton v-if="convertedAmount" :disabled="submitting" color="info"
                        :label="submitting ? 'Submitting...' : 'Submit Transaction'" class="w-full"
                        @click="submitTransaction" />
                </div>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
