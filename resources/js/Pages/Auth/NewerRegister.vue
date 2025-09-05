<script setup>
import { useForm, usePage, Head, Link, router } from '@inertiajs/vue3'
import LoginInput from '@/Components/LoginInput.vue'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'
import LoginRightGraphics from '@/Components/LoginRightGraphics.vue'


const form = useForm({
    name: '',
    user_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
})


const register = () => {

    if (!form.processing) {
        form.post(route('register'), {
            preserveScroll: true,
            onSuccess: (page) => {

                var response = page.props.flash.data;
                if (response.success) {

                }
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong.',
                    })
                }
            }, onError: (errors) => {
                var size = Object.keys(errors).length;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: `There are ${size} form errors. Please fix them`,
                })
            },
        });
    }
}
</script>

<style scoped>
/* Optional: smooth transition for right-side graphics */
</style>
<template>
    <div class="min-h-screen flex items-center justify-center bg-blue-100 p-4 py-9">
        <div class="bg-white rounded-3xl shadow-2xl flex flex-col lg:flex-row w-full max-w-5xl overflow-hidden">

            <!-- Left side: Form -->
            <div class="w-full lg:w-1/2 p-8 sm:p-16">
                <div class="flex justify-between">
                    <Link href="/" class="inline-flex items-center text-gray-500 mb-6">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                    </Link>

                    <p>Already registered? &nbsp;<Link :href="route('login')" class="font-bold text-blue-500 hover:text-blue-700">Sign in</Link></p>

                </div>

                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2 mt-4">Sign Up</h2>
                <p class="text-gray-400 mb-8">Your Secure Link to Global Payments</p>

                <form @submit.prevent="register" class="space-y-5" autocomplete="off">
                    <!-- Name -->
                    <LoginInput v-model="form.name" :error="form.errors.name" type="text" id="name"
                        placeholder="Full Name" class="" icon="fa-regular fa-user" />

                    <!-- User Name -->
                    <LoginInput v-model="form.user_name" :error="form.errors.user_name" type="text" id="user_name"
                        placeholder="User Name" class="" icon="fa-regular fa-circle-user" />

                    <!-- Email -->

                    <LoginInput v-model="form.email" :error="form.errors.email" type="email" id="email"
                        placeholder="Email Address" class="" icon="fa-regular fa-envelope" />

                    <!-- Phone -->

                    <LoginInput v-model="form.phone" :error="form.errors.phone" type="number" id="phone"
                        placeholder="Phone Number" class="" icon="fa-solid fa-mobile-screen" />

                    <!-- Password -->


                    <LoginInput v-model="form.password" :error="form.errors.password" type="password" id="password"
                        placeholder="Password" class="" icon="fa-solid fa-lock" />


                    <!-- Confirm Password -->


                    <LoginInput v-model="form.password_confirmation" :error="form.errors.password_confirmation"
                        type="password" id="password_confirmation" placeholder="Confirm Password" class=""
                        icon="fa-solid fa-lock" />

                    <div class="h-[20px]"></div>

                    <!-- Submit -->
                    <button type="submit" :disabled="form.processing" class="w-1/2 bg-gradient-to-r from-purple-500 to-blue-500
         hover:from-purple-600 hover:to-blue-600 text-white py-2  px-3 rounded-full font-semibold
         transition-all duration-300 relative">

                        <span class="inline-block mt-1 font-bold">
                            Sign Up
                        </span>
                        <!-- Transparent circle with outline for arrow -->

                        <img v-if="form.processing" class="inline-block w-7 h-6 mt-1 float-right"
                                        :src="FormLoaderLight" alt="">
                        <span
                            v-else
                            class="ml-3 flex items-center justify-center w-8 h-8 p-2 bg-white/15 rounded-full float-right">
                            <font-awesome-icon icon="fa-solid fa-arrow-right" class="text-white text-xs" />
                        </span>
                    </button>


                    <!-- Social login -->
                    <!-- <div class="flex justify-center space-x-4 mt-4">
                        <button class="p-2 rounded-full border hover:bg-gray-100 transition">
                            <img src="/images/facebook-icon.png" class="w-5 h-5" />
                        </button>
                        <button class="p-2 rounded-full border hover:bg-gray-100 transition">
                            <img src="/images/google-icon.png" class="w-5 h-5" />
                        </button>
                    </div> -->
                </form>

                <!-- <p class="text-center text-gray-400 mt-6">
                    Already have an account?
                    <Link href="/login" class="text-blue-500 hover:underline">Sign in</Link>
                </p> -->
            </div>

            <!-- Right side: Graphics -->
            <LoginRightGraphics />

        </div>
    </div>
</template>
