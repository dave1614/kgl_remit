<template>
    <div
        class="w-full lg:w-1/2 bg-gradient-to-br from-blue-600 to-purple-700 p-10 flex flex-col justify-between relative overflow-hidden mt-10 lg:mt-0 rounded-b-3xl lg:rounded-r-3xl">

        <!-- Floating Icons -->
        <div
            class="absolute top-10 left-16 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow animate-float hover:scale-110 hover:-translate-y-2 hover:rotate-3 transition-all duration-500">
            <i class="fas fa-globe-americas text-blue-500 text-2xl"></i>
        </div>

        <div
            class="absolute top-28 right-16 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow animate-float animation-delay-2000 hover:scale-110 hover:-translate-y-2 hover:rotate-2 transition-all duration-500">
            <i class="fas fa-wallet text-green-500 text-xl"></i>
        </div>

        <div
            class="absolute top-52 left-24 w-16 h-16 bg-white rounded-full flex items-center justify-center shadow animate-float animation-delay-4000 hover:scale-110 hover:-translate-y-2 hover:-rotate-2 transition-all duration-500">
            <i class="fas fa-paper-plane text-purple-500 text-2xl"></i>
        </div>

        <div
            class="absolute bottom-24 right-12 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow animate-float animation-delay-6000 hover:scale-110 hover:-translate-y-2 hover:rotate-3 transition-all duration-500">
            <i class="fas fa-lock text-red-500 text-2xl"></i>
        </div>

        <div
            class="absolute top-40 right-32 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow animate-float animation-delay-3000 hover:scale-110 hover:-translate-y-2 hover:rotate-1 transition-all duration-500">
            <i class="fas fa-hand-holding-usd text-yellow-500 text-xl"></i>
        </div>

        <div
            class="absolute top-64 left-10 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow animate-float animation-delay-5000 hover:scale-110 hover:-translate-y-2 hover:-rotate-1 transition-all duration-500">
            <i class="fas fa-credit-card text-pink-500 text-xl"></i>
        </div>

        <div
            class="absolute bottom-32 left-24 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow animate-float animation-delay-7000 hover:scale-110 hover:-translate-y-2 hover:rotate-2 transition-all duration-500">
            <i class="fas fa-dollar-sign text-green-400 text-2xl"></i>
        </div>

        <!-- Info Boxes -->
        <div class="bg-white rounded-xl p-6 shadow-md w-3/4 mb-5">
            <p class="text-sm text-gray-400">Total Transfers</p>
            <h3 class="font-bold text-2xl mt-2">₦1,234,567</h3>
            <div class="mt-2 h-2 bg-gray-200 rounded-full relative">
                <div class="absolute left-0 top-0 h-2 w-1/4 bg-green-400 rounded-full"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md w-3/4">
            <div class="flex items-center space-x-2">
                <i class="fas fa-shield-alt text-blue-500"></i>
                <p class="font-semibold text-gray-800">Secure & Trusted</p>
            </div>
            <p class="text-gray-400 text-sm mt-2">All transactions are encrypted and monitored for your safety.</p>
        </div>

        <!-- <div class="bg-white rounded-xl p-6 shadow-md w-3/4 mt-4">
            <div class="flex items-center space-x-2">
                <i class="fas fa-bolt text-yellow-500"></i>
                <p class="font-semibold text-gray-800">Fast Transfers</p>
            </div>
            <p class="text-gray-400 text-sm mt-2">Send money internationally in seconds without delays.</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-md w-3/4 mt-4">
            <div class="flex items-center space-x-2">
                <i class="fas fa-coins text-green-500"></i>
                <p class="font-semibold text-gray-800">Low Fees</p>
            </div>
            <p class="text-gray-400 text-sm mt-2">Keep more of your money with minimal transfer fees.</p>
        </div> -->

        <div class="bg-white rounded-xl p-6 shadow-md w-3/4 mt-4">
            <div class="flex items-center space-x-2">
                <i class="fas fa-headset text-purple-500"></i>
                <p class="font-semibold text-gray-800">24/7 Support</p>
            </div>
            <p class="text-gray-400 text-sm mt-2">We’re always here to assist you, day or night.</p>
        </div>
    </div>

</template>

<script>
export default {
    inheritAttrs: false,
    props: {

    },
    methods: {

    },
}
</script>

<style>
@keyframes float {

    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 4s ease-in-out infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-3000 {
    animation-delay: 3s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animation-delay-5000 {
    animation-delay: 5s;
}

.animation-delay-6000 {
    animation-delay: 6s;
}

.animation-delay-7000 {
    animation-delay: 7s;
}
</style>
