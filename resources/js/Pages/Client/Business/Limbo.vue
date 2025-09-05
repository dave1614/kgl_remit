<script setup>
import { Link } from '@inertiajs/vue3'
import FormLoaderDark from '@/Loaders/form_loader_dark.gif'

const props = defineProps({
    status: String, // expected to be 'pending'
})
</script>

<template>
<div class="min-h-screen flex items-center justify-center bg-blue-100 p-4 py-9">
    <div class="bg-white rounded-3xl shadow-2xl flex flex-col w-full max-w-2xl overflow-hidden p-8 sm:p-16 text-center">

        <h2 class="text-4xl font-bold text-gray-800 mb-4">Registration Pending</h2>
        <p class="text-gray-500 mb-6">
            Your business registration is currently <span class="font-semibold">{{ props.status }}</span>.
            An admin is reviewing your submission. You will be notified once a decision is made.
        </p>

        <div class="flex justify-center mb-6">
            <img class="w-20 h-20" :src="FormLoaderDark" alt="Loading" />
        </div>

        <!-- <p class="text-gray-400 mb-6">
            While you wait, you can review your submitted information or log out.
        </p> -->

        <div class="flex justify-center gap-4">
            <!-- <Link as="button" :href="route('client.business.create')" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-full font-semibold transition-all duration-300">
                Review Registration
            </Link> -->
            <Link as="button" method="post" :href="route('logout')" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-full font-semibold transition-all duration-300">
                Logout
            </Link>
        </div>

    </div>
</div>
</template>
