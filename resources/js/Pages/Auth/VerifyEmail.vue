<script setup>
import { useForm, Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import LayoutGuest from "@/Layouts/LayoutGuest.vue";
import SectionFullScreen from "@/Components/SectionFullScreen.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import BaseButton from "@/Components/BaseButton.vue";
import FormValidationErrors from "@/Components/FormValidationErrors.vue";
import NotificationBarInCard from "@/Components/NotificationBarInCard.vue";
import BaseLevel from "@/Components/BaseLevel.vue";

const props = defineProps({
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({});

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);

const submit = () => {
    form.post(route("verification.send"));
};
</script>

<template>
    <LayoutGuest>

        <Head title="Email Verification" />

        <SectionFullScreen v-slot="{ cardClass }" bg="purplePink">
            <div class="flex flex-col justify-center items-center">
                <!-- Logo -->
                <!-- <img
                    src="/images/logo.png"
                    alt="App Logo"
                    class="h-20 w-auto mb-6"
                /> -->
                <h4 class="text-4xl font-bold text-gray-700 mb-8">KGL Remit</h4>

                <!-- Card -->
                <CardBox :class="cardClass" is-form @submit.prevent="submit">
                    <FormValidationErrors />

                    <NotificationBarInCard v-if="verificationLinkSent" color="info">
                        A new verification link has been sent to the email address you
                        provided during registration.
                    </NotificationBarInCard>

                    <FormField>
                        <div class="mb-4 text-sm text-gray-600">
                            Thank you for signing up. Please check your inbox and verify your email address by clicking the
                            Verify button in the message we sent. If you don’t see the email, click Resend to receive a new
                            one.
                        </div>
                    </FormField>

                    <BaseDivider />

                    <BaseLevel>
                        <BaseButton type="submit" color="info" label="Resend Verification Email"
                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing" />
                        <Link :href="route('logout')" method="post" as="button">
                        Logout
                        </Link>
                    </BaseLevel>
                </CardBox>
            </div>
        </SectionFullScreen>
    </LayoutGuest>
</template>
