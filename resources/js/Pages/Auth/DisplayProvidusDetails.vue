<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import { mdiEmail } from '@mdi/js'
import LayoutGuest from '@/Layouts/LayoutGuest.vue'
import SectionFullScreen from '@/Components/SectionFullScreen.vue'
import CardBox from '@/Components/CardBox.vue'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseDivider from '@/Components/BaseDivider.vue'
import BaseButton from '@/Components/BaseButton.vue'
import FormValidationErrors from '@/Components/FormValidationErrors.vue'
import NotificationBarInCard from '@/Components/NotificationBarInCard.vue'
import BaseLevel from '@/Components/BaseLevel.vue'

defineProps({
    user: {
        type: Object,

    }
})


</script>

<template>
    <LayoutGuest>

        <Head title="Forgot Password" />

        <SectionFullScreen v-slot="{ cardClass }" bg="purplePink">
            <CardBox :class="cardClass">


                <!-- <NotificationBarInCard v-if="status" color="info">
                    {{ status }}
                </NotificationBarInCard> -->


                <div class="mb-4 text-xl font-bold ">
                    Registration fee is ₦100. Please credit your personalised bank account below to complete
                    registration
                </div>

                <table>
                    <thead>
                        <tr>

                            <th></th>

                            <th>Bank Name</th>
                            <th>Account Name</th>
                            <th>Account Number</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-if="user.providus_account_number != null">
                            <td></td>
                            <td data-label="Bank Name">Providus Bank</td>
                            <td data-label="Account Name">{{ user.providus_account_name }}</td>
                            <td data-label="Account Number">{{ user.providus_account_number }}</td>
                        </tr>

                    </tbody>
                </table>

                <h5 class="text-lg font-bold my-3 mt-6">After transfer, please reload this page.</h5>



                <BaseDivider />


            </CardBox>
        </SectionFullScreen>
    </LayoutGuest>
</template>
