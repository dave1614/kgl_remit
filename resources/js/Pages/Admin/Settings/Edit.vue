<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
import SectionMain from '@/Components/SectionMain.vue'
import { useMainStore } from '@/Stores/main'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseButton from '@/Components/BaseButton.vue'
import { mdiCog } from '@mdi/js'

const props = defineProps({
    settings: Object
})

const mainStore = useMainStore()

const form = useForm({
    invoice_expiry_minutes: props.settings.invoice_expiry_minutes ?? '',
    bank_name: props.settings.bank_name ?? '',
    account_number: props.settings.account_number ?? '',
    account_name: props.settings.account_name ?? '',
})

// method to handle submit with swal
const submitSettings = () => {
    console.log("jeeuuu")
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will update system settings for all users.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save changes'
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(route('admin.settings.update'), {
                onSuccess: () => {
                    // Swal.fire('Saved!', 'Settings have been updated.', 'success')
                    mainStore.toast = "Settings saved successfully"
                }, onError: () => {
                    Swal.fire('Error', 'Please fix the highlighted errors and try again.', 'error')
                }
            })
        }
    })
}
</script>

<template>
    <LayoutAuthenticated>

        <Head title="Settings" />

        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiCog" title="System Settings" main />
            <CardBox isForm @submit.prevent="submitSettings">
                <div class="space-y-4">
                    <FormField label="Invoice Expiry (minutes)">
                        <FormControl v-model="form.invoice_expiry_minutes" :error="form.errors.invoice_expiry_minutes"
                            type="number" />
                    </FormField>

                    <FormField label="Bank Name">
                        <FormControl v-model="form.bank_name" :error="form.errors.bank_name" type="text" />
                    </FormField>

                    <FormField label="Account Number">
                        <FormControl v-model="form.account_number" :error="form.errors.account_number" type="text" />
                    </FormField>

                    <FormField label="Account Name">
                        <FormControl v-model="form.account_name" :error="form.errors.account_name" type="text" />
                    </FormField>

                    <BaseButton type="submit" :disabled="form.processing" color="success" label="Save Settings"
                        class="w-full" />
                </div>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
