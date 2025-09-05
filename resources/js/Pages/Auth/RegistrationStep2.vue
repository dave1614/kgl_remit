<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Head, Link, router } from '@inertiajs/vue3'
import { mdiEmail, mdiLogout, mdiAccountChild, mdiArrowLeft } from '@mdi/js'
import { useMainStore } from "@/Stores/main";

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
import { useAxios } from '@/composables/axios.js'

const props = defineProps({
    user: {
        type: Object,

    },
    first_commitment_fee: {
        type: Number
    },
    balance: {
        type: Number
    },
})


const mainStore = useMainStore();

const page = ref(0);
const errors_size = ref(0);
const page_one_loading = ref(false);

const form = ref({

    placement: null,
    placement_user_id: null,
    available_position: null,
    positioning: null,
    mlm_db_id: null,
    position_selected: false,
})

const voucher_form = ref({
    voucher: null,
})

const openPage = (p) => {
    page.value = p
}

onMounted(() => {
    if (props.balance <= 0) {
        // const onConfirmRefresh = function (event) {
        //     event.preventDefault();
        //     return event.returnValue = "Are you sure you want to leave the page?";
        // }

        // window.addEventListener("beforeunload", onConfirmRefresh, { capture: true });

        console.log(props.balance)
        // Swal.fire({
        //     title: 'Registration fee paid successfully',
        //     html: 'Do You Have A Placement?',
        //     icon: 'success',
        //     showCancelButton: true,
        //     confirmButtonColor: '#3085d6',
        //     cancelButtonColor: '#d33',
        //     confirmButtonText: 'Yes',
        //     cancelButtonText: "No",
        //     allowEscapeKey: false,
        //     allowOutsideClick: false,
        // }).then((result) => {
        //     if (result.isConfirmed) {
        //         // mainStore.toast = 'Something went wrong'
        //         openPage(1);
        //     } else if (
        //         /* Read more about handling dismissals below */
        //         result.dismiss === Swal.DismissReason.cancel
        //     ) {
        //         processNoPlacement();
        //     }
        // });

        mainStore.toast = "Processing registration";
        processNoPlacement();
    }
});



const processNoPlacement = () => {
    openPage(1)
    console.log('getting you a placement..')

    form.value.placement_user_id = null;
    form.value.positioning = null;
    form.value.mlm_db_id = null;
    form.value.position_selected = false;
    selectThisPositionPlacement(null)

}

const submitPageOneForm = async () => {
    if (page_one_loading.value) {
        return;
    }

    page_one_loading.value = true
    let queryRoute = route('get_reg_placement_info');

    const { response, error, loading } = await useAxios(queryRoute, `Loading placement's information .....`, form.value);

    page_one_loading.value = false;
    console.log(response.value.data)
    if (response?.value?.data) {

        if (response.value.data.success) {
            var resp = response.value.data;
            Swal.fire({
                icon: 'success',
                title: `Do you select ${resp.placement.name} as your placement?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Proceed!',
                cancelButtonText: 'No',
                html: `<div class="max-w-md mx-auto bg-white border border-gray-200 rounded-2xl shadow-md p-6">
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-gray-700">
                    Full Name:
                    <em class="text-blue-600 font-normal">${resp.placement.name}</em>
                    </p>

                    <p class="text-sm font-semibold text-gray-700">
                    Username:
                    <em class="text-blue-600 font-normal">${resp.placement.user_name}</em>
                    </p>

                    <p class="text-sm font-semibold text-gray-700">
                    Email Address:
                    <em class="text-blue-600 font-normal">${resp.placement.email}</em>
                    </p>
                </div>
                </div>
                `,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.value.placement_user_id = resp.placement.id
                    // switch_pages('page_two_open')
                    checkIfAnyPositionIsAvailableUnderPlacement();
                }
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: `This placement does not exist`,
            })
        }

    }
}


const checkIfAnyPositionIsAvailableUnderPlacement = async () => {

    if (page_one_loading.value) {
        return;
    }

    page_one_loading.value = true
    let queryRoute = route('select_positioning_for_mlm_registration');

    const { response, error, loading } = await useAxios(queryRoute, `Loading available placement spaces .....`, form.value);

    page_one_loading.value = false;
    console.log(response.value.data)
    if (response?.value?.data) {
        var resp = response.value.data;
        if (resp.success && resp.available_position != "" && resp.mlm_db_id != "") {
            form.value.available_position = resp.available_position;
            form.value.mlm_db_id = resp.mlm_db_id;
            openPage(2);
            console.log(form.value)
        } else if (resp.invalid_placement) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Placement',
                text: `Placement selected is invalid`,
            })
        } else if (resp.no_available_position) {
            Swal.fire({
                icon: 'error',
                title: 'No Available Position',
                text: `Sorry No Available Position Under This Account.`,
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Ooops!',
                text: `Sorry something went wrong`,
            })
        }

    }
}

const selectThisPositionPlacement = async (position = null) => {
    if (page_one_loading.value) {
        return;
    }

    page_one_loading.value = true
    form.value.positioning = position
    form.value.position_selected = (position == null) ? false : true;
    openPage(1)
    let queryRoute = route('complete_registration_step_2');

    const { response, error, loading } = await useAxios(queryRoute, `Placement selected. Completing your registration. Please do not reload page ......`, form.value, true);

    page_one_loading.value = false;
    console.log(response.value.data)
    if (response?.value?.data) {
        var resp = response.value.data;
        if (resp.success) {
            mainStore.toast = 'Registration completed successfully';
            // router.visit(route('dashboard'));
            window.location.href = route('dashboard');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Ooops!',
                text: `Sorry something went wrong`,
            })
        }

    }

}

const submitVoucherForm = async () => {

    if (page_one_loading.value) {
        return;
    }

    console.log(voucher_form.value.voucher)

    page_one_loading.value = true

    let queryRoute = route('process_submit_voucher');

    const { response, error, loading } = await useAxios(queryRoute, `Verifying your voucher ......`, voucher_form.value);

    page_one_loading.value = false;
    console.log(response.value.data)
    if (response?.value?.data) {
        var resp = response.value.data;
        if (resp.success && resp.credited_amt != null) {
            Swal.fire({
                icon: 'success',
                title: 'Voucher applied successfully.',
                html: `Your account has been credited with <em class="text-emerald-500">${mainStore.addCommas(resp.credited_amt)}</em>`,
                allowEscapeKey: false,
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        } else if(resp.not_exists){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: `This voucher does not exist`,
            })
        }else if(resp.already_used){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: `This voucher has already been used`,
            })
        }else {
            Swal.fire({
                icon: 'error',
                title: 'Ooops!',
                text: `Sorry something went wrong`,
            })
        }

    }
};

</script>

<template>
    <LayoutGuest>

        <Head title="Complete your registration" />

        <SectionFullScreen v-slot="{ cardClass }" bg="purplePink">
            <CardBox :class="cardClass">



                <!-- <NotificationBarInCard v-if="status" color="info">
                    {{ status }}
                </NotificationBarInCard> -->



                <div v-if="balance > 0" class="">
                    <BaseLevel>
                        <BaseButton @click="mainStore.logout" color="danger" label="Logout" :icon="mdiLogout"
                            method="post" />
                    </BaseLevel>
                    <p class=" text-xl font-semibold my-2">Step 2 of registration</p>
                    <small class="text-sm font-semibold">Complete registration payment</small>

                    <h5 class=" text-lg font-bold mt-[30px]">Personal details: </h5>
                    <table class="table ">
                        <tbody>
                            <tr>
                                <td>Registration Fee</td>
                                <td><em class="text-primary-100" v-html="'₦' + first_commitment_fee"></em></td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td><em class="text-primary-100">{{ user.user_name }}</em></td>
                            </tr>
                            <tr>
                                <td>Full name</td>
                                <td><em class="text-primary-100">{{ user.name }}</em></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><em class="text-primary-100">{{ user.email }}</em></td>
                            </tr>
                            <tr>
                                <td>Phone Number</td>
                                <td><em class="text-primary-100" v-html="`${user.phone_code}${user.phone}`"></em></td>
                            </tr>


                            <tr>
                                <td>Balance</td>
                                <td><em class="text-primary-100" v-html="'₦' + balance"></em></td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 class="text-center text-sm font-bold mt-[30px]">Deposit in your personalized account: </h5>
                    <small class="text-xs text-gray-400">₦20 charge for instant crediting</small>


                    <table class="table">
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
                                <td>Providus Bank</td>
                                <td>{{ user.providus_account_name }}</td>
                                <td>{{ user.providus_account_number }}</td>
                            </tr>


                        </tbody>
                    </table>

                    <h5 class="text-sm font-bold my-3 mt-6">After transfer, please reload this page.</h5>

                    <h5 class="text-xl font-bold my-3 mt-6 text-center">OR</h5>

                    <h5 class="text-sm font-bold my-3 mt-6">Pay with registration voucher.</h5>

                    <form @submit.prevent="submitVoucherForm" class="mt-4">
                        <FormField label="" help="Enter your registration voucher code">
                            <FormControl v-model="voucher_form.voucher" type="text" placeholder="Enter voucher code"
                                required />
                        </FormField>

                         <BaseButtons class="mt-[30px]">
                            <BaseButton type="submit" color="success" class="block w-full" label="SUBMIT" />

                        </BaseButtons>
                    </form>
                </div>

                <div v-else class="">
                    <div v-if="page == 2" class="">
                        <!-- <p class="text-gray-400 text-sm font-semibold my-2">Step 2</p> -->
                        <!-- <small class="text-sm font-semibold">Enter your placement details</small> -->

                        <div class="flex justify-start mb-6">
                            <BaseButton @click="openPage(1)" color="warning" label="Go Back" :icon="mdiArrowLeft" />
                        </div>
                        <FormField>
                            <div
                                class="mb-6 px-4 py-3 bg-blue-50 border border-blue-200 text-sm text-blue-700 rounded-lg font-medium shadow-sm">
                                To proceed with placement selection, click to select a placement position.
                            </div>
                        </FormField>

                        <!-- Placement Tables -->
                        <div v-if="form.available_position" class="space-y-4">
                            <div v-if="form.available_position === 'left' || form.available_position === 'right' || form.available_position === 'both'"
                                class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                                <h3 class="text-base font-semibold text-gray-700 mb-4">
                                    Select Your Placement Position
                                </h3>

                                <table class="w-full text-left border border-gray-100 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wide">
                                        <tr>
                                            <th class="px-4 py-2">#</th>
                                            <th class="px-4 py-2">Available Positions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 divide-y divide-gray-100">
                                        <tr v-if="form.available_position === 'left' || form.available_position === 'both'"
                                            @click="selectThisPositionPlacement('left')"
                                            class="hover:bg-blue-50 cursor-pointer transition-all">
                                            <td class="px-4 py-2">1</td>
                                            <td class="px-4 py-2">Left</td>
                                        </tr>
                                        <tr v-if="form.available_position === 'right' || form.available_position === 'both'"
                                            @click="selectThisPositionPlacement('right')"
                                            class="hover:bg-blue-50 cursor-pointer transition-all">
                                            <td class="px-4 py-2">
                                                {{ form.available_position === 'both' ? 2 : 1 }}
                                            </td>
                                            <td class="px-4 py-2">Right</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>




                    </div>

                    <div v-if="page == 1" class="">
                        <CardBox is-form @submit.prevent="submitPageOneForm">
                            <!-- <FormValidationErrors /> -->

                            <!-- <NotificationBarInCard v-if="status" color="info">
                                {{ status }}
                            </NotificationBarInCard> -->

                            <FormField>
                                <div class="mb-4 text-sm font-semibold text-gray-600 ">
                                    To proceed with placement selection, enter your placements username or email
                                    address.
                                </div>
                            </FormField>

                            <FormField label="Username or email" help="Enter placements username or email">
                                <FormControl v-model="form.placement" :icon="mdiAccountChild" type="text"
                                    :error="form.errors.placement" required />
                            </FormField>

                            <BaseDivider />

                            <BaseLevel>
                                <BaseButton type="submit" color="info" label="Submit"
                                    :class="{ 'opacity-25': page_one_loading }" :disabled="page_one_loading" />
                                <BaseButton @click="mainStore.logout" color="danger" label="Logout" :icon="mdiLogout"
                                    method="post" />
                            </BaseLevel>

                            <p class="text-center text-sm my-2 mt-8">No placement, <a class="text-blue-400" href="#"
                                    @click="processNoPlacement">Click here</a></p>
                        </CardBox>
                    </div>
                </div>


                <BaseDivider />


            </CardBox>
        </SectionFullScreen>
    </LayoutGuest>
</template>
