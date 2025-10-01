<script setup>
import { ref, onMounted } from 'vue'
import { useMainStore } from '@/Stores/main'
import { useAxios } from '@/composables/axios.js'
import { Head } from '@inertiajs/vue3'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import BaseDivider from '@/Components/BaseDivider.vue'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import FormCheckRadioGroup from '@/Components/FormCheckRadioGroup.vue'



import { mdiAccountGroup } from '@mdi/js'

const main_props = defineProps({
    user_name: {}
})

const mainStore = useMainStore()

const statusOptions = ref({ verified: 'verified', unverified: 'unverified', business_registered: 'business_registered', business_registration_pending: 'business_registration_pending', all: 'all' });

const users = ref({})
const props = ref({
    page: 1,
    length: 10,
    status: statusOptions.value.all,
    user_name: main_props.user_name,
    full_name: null,
    email: null,
    phone: null,

    date: null,
    email_verified_at: null,
    business_registered_at: null,
    start_date: null,
    end_date: null,

})

const loadUsers = async (url = null) => {
    if (url) {
        const urlParams = new URLSearchParams(url)
        props.value.page = urlParams.get('page')
    }
    const { response, error } = await useAxios(route('admin.users.all'), 'Loading users...', props.value)
    if (response?.value?.data) users.value = response.value.data
    else if (error?.value) mainStore.toast = 'Failed to load users'
}

const clearFilters = () => {
    props.value = {
        page: 1,
        length: 10,
        status: statusOptions.value.all,
        user_name: null,
        full_name: null,
        email: null,
        phone: null,
        date: null,
        email_verified_at: null,
        business_registered_at: null,
        start_date: null,
        end_date: null,
    }
}

onMounted(() => loadUsers())
</script>

<template>
    <LayoutAuthenticated>

        <Head title="All Users" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccountGroup" title="All Users" main />
            <CardBox>
                <!-- Filters -->
                <CardBox isForm @submit.prevent="loadUsers">
                    <div class="sm:grid sm:grid-cols-12 sm:gap-6">
                        <FormField class="sm:col-span-4" label="Per Page">
                            <FormControl v-model="props.length" :options="[10, 20, 50, 100]" />
                        </FormField>

                        <FormField class="sm:col-span-4 capitalize" label="Status">
                            <FormCheckRadioGroup v-model="props.status" name="status" :options="statusOptions" type="radio" />
                        </FormField>
                        <FormField class="sm:col-span-4" label="Username">
                            <FormControl v-model="props.user_name" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Full Name">
                            <FormControl v-model="props.full_name" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Email">
                            <FormControl v-model="props.email" type="text" />
                        </FormField>
                        <FormField class="sm:col-span-3" label="Phone">
                            <FormControl v-model="props.phone" type="text" />
                        </FormField>

                        <FormField class="sm:col-span-3" label="Registration Date">
                            <FormControl v-model="props.date" type="date" />
                        </FormField>

                        <FormField class="sm:col-span-3" label="Email Verification Date">
                            <FormControl v-model="props.email_verified_at" type="date" />
                        </FormField>

                        <FormField class="sm:col-span-3" label="Business Registration Date">
                            <FormControl v-model="props.business_registered_at" type="date" />
                        </FormField>

                        <FormField class="sm:col-span-3" label="Start Date">
                            <FormControl v-model="props.start_date" type="date" />
                        </FormField>

                        <FormField class="sm:col-span-3" label="End Date">
                            <FormControl v-model="props.end_date" type="date" />
                        </FormField>


                    </div>


                    <BaseButtons class="mt-5 mb-2">
                        <BaseButton type="submit" color="info" label="Filter" />
                        <BaseButton type="reset" color="info" outline label="Clear" @click="clearFilters" />
                    </BaseButtons>
                    <BaseDivider />
                </CardBox>

                <!-- Table -->
                <CardBox has-table>
                    <div v-if="users.data && users.data.length > 0">
                        <table class="table table-striped w-full text-gray-800 dark:text-slate-200">
                            <thead class="bg-gray-100 dark:bg-slate-700">
                                <tr>
                                    <th>#</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <!-- <th>Country</th> -->
                                    <th>Registration Date</th>
                                    <th>Email Verified</th>
                                    <th>Email Verified Date</th>
                                    <th>Business Registered</th>
                                    <th>Business Registered Date</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, i) in users.data" :key="row.id">
                                    <td>{{ i + 1 + ((users.current_page - 1) * props.length) }}</td>
                                    <td>{{ row.user_name }}</td>
                                    <td>{{ row.name }}</td>
                                    <td>{{ row.email }}</td>
                                    <td>{{ row.phone }}</td>
                                    <!-- <td>{{ row.country?.name }}</td> -->
                                    <td>{{ mainStore.formatDate(row.created_at) }}</td>
                                    <td>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                            :class="row.email_verified_at ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'">
                                            {{ row.email_verified_at ? 'Verified' : 'Unverified' }}
                                        </span>
                                    </td>
                                    <td>{{ mainStore.formatDate(row.email_verified_at) }}</td>
                                    <td>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                            :class="row.business_registered ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'">
                                            {{ row.business_registered ? 'Registered' : 'Not Registered' }}
                                        </span>
                                    </td>
                                    <td>{{ mainStore.formatDate(row.business_registered_at) }}</td>

                                </tr>
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div class="mt-3 text-gray-700 dark:text-slate-400">
                            <BaseButtons>
                                <BaseButton v-for="page in users.links" :key="page.label" :label="page.label"
                                    :active="page.active" small @click="loadUsers(page.url)" :disabled="!page.url" />
                            </BaseButtons>
                            <small>Page {{ users.current_page }} of {{ users.last_page }}</small><br>
                            <small>{{ users.total }} total records</small>
                        </div>
                    </div>
                </CardBox>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
