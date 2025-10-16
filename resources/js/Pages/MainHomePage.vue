<template>
  <div class="min-h-screen font-sans text-slate-900 bg-white">
    <!-- Top navigation -->
    <header class="fixed w-full z-40 bg-white/60 backdrop-blur-sm border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white font-bold">KGL</div>
            <div>
              <a href="/" class="text-lg font-semibold text-slate-900">KGL Remit</a>
              <div class="text-xs text-slate-500 -mt-1">Fast • Secure • Borderless</div>
            </div>
          </div>

          <nav class="hidden md:flex items-center space-x-6 text-sm">
            <a href="#features" class="hover:text-blue-600">Features</a>
            <a href="#how" class="hover:text-blue-600">How it works</a>
            <a href="#rates" class="hover:text-blue-600">Rates</a>
            <a href="#faq" class="hover:text-blue-600">FAQ</a>
            <a href="#contact" class="hover:text-blue-600">Contact</a>
          </nav>

          <div class="flex items-center space-x-3">
            <Link class="hidden sm:inline-flex items-center px-4 py-2 border rounded-lg text-sm text-slate-700 border-slate-200 hover:bg-slate-50" href="/login">Log in</Link>
            <Link class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700" href="/register">Create account</Link>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero -->
    <section class="pt-24 pb-12 bg-gradient-to-b from-blue-50 to-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
          <div class="md:col-span-6">
            <h1 class="text-3xl md:text-5xl font-extrabold leading-tight text-slate-900">
              Fast, secure, and borderless money transfers.
            </h1>
            <p class="mt-4 text-slate-600 text-lg max-w-xl">
              Send and receive money across Nigeria and the world. Competitive exchange rates, multi-currency support, bank & wallet payouts and 24/7 tracking.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
              <Link href="/send" class="inline-flex items-center gap-3 px-5 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                <i class="fas fa-paper-plane"></i>
                Send Money
              </Link>

              <a href="#rates" class="inline-flex items-center gap-2 px-4 py-3 border rounded-lg text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-chart-line text-blue-600"></i>
                Live Rates
              </a>

              <Link href="/track" class="inline-flex items-center gap-2 px-4 py-3 text-sm rounded-lg border border-slate-200 hover:bg-slate-50">
                <i class="fa-solid fa-location-dot text-slate-700"></i>
                Track Transfer
              </Link>
            </div>

            <div class="mt-6 flex items-center gap-6">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow">
                  <i class="fa-solid fa-shield-check text-blue-600 text-xl"></i>
                </div>
                <div>
                  <div class="text-sm text-slate-600">Secure transfers</div>
                  <div class="font-semibold text-slate-800">Encryption & compliance</div>
                </div>
              </div>

              <div class="hidden sm:flex items-center gap-3">
                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow">
                  <i class="fa-solid fa-clock text-blue-600 text-xl"></i>
                </div>
                <div>
                  <div class="text-sm text-slate-600">Fast</div>
                  <div class="font-semibold text-slate-800">Near-instant settlements</div>
                </div>
              </div>
            </div>
          </div>

          <div class="md:col-span-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-xs text-slate-500">You send</div>
                  <div class="text-lg font-semibold">USD</div>
                </div>

                <div class="text-right">
                  <div class="text-xs text-slate-500">Recipient gets</div>
                  <div class="text-lg font-semibold">NGN</div>
                </div>
              </div>

              <div class="mt-4">
                <div class="grid grid-cols-2 gap-3">
                  <input v-model="sampleAmount" type="number" min="0" class="col-span-2 p-3 border rounded-lg" placeholder="Amount (e.g. 1000)" />
                  <div class="p-3 border rounded-lg">Rate: <span class="font-semibold">{{ sampleRate }}</span></div>
                  <div class="p-3 border rounded-lg">Est. Received: <span class="font-semibold">{{ formattedEst }}</span></div>
                </div>

                <div class="mt-4 flex gap-3">
                  <Link href="/send" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-lg">Start transfer</Link>
                  <button @click="calculateSample" class="px-4 py-3 border rounded-lg text-sm text-slate-700">Calculate</button>
                </div>
              </div>

              <div class="mt-4 text-xs text-slate-500">
                <i class="fa-regular fa-circle-question"></i> Indicative rates. Final rate confirmed on review.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Trusted by -->
    <section class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-sm text-slate-500">Trusted by</h3>
        <div class="mt-6 flex items-center justify-center gap-10 flex-wrap">
          <img v-for="(p, i) in partners" :key="i" :src="p.logo" :alt="p.name" class="h-8 opacity-80" />
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section id="how" class="py-16 bg-blue-50">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-slate-900 text-center">How KGL Remit works</h2>
        <p class="text-center text-slate-600 mt-2 max-w-2xl mx-auto">Send money in 3 simple steps</p>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-xl p-6 shadow">
            <div class="w-12 h-12 rounded-lg bg-blue-600 text-white flex items-center justify-center"><i class="fa-solid fa-user-plus"></i></div>
            <h4 class="mt-4 font-semibold">Create account</h4>
            <p class="mt-2 text-sm text-slate-600">Sign up in minutes with basic KYC to start moving money.</p>
          </div>

          <div class="bg-white rounded-xl p-6 shadow">
            <div class="w-12 h-12 rounded-lg bg-blue-600 text-white flex items-center justify-center"><i class="fa-solid fa-wallet"></i></div>
            <h4 class="mt-4 font-semibold">Fund wallet</h4>
            <p class="mt-2 text-sm text-slate-600">Fund via bank transfer, card or supported payment gateways.</p>
          </div>

          <div class="bg-white rounded-xl p-6 shadow">
            <div class="w-12 h-12 rounded-lg bg-blue-600 text-white flex items-center justify-center"><i class="fa-solid fa-paper-plane"></i></div>
            <h4 class="mt-4 font-semibold">Send & confirm</h4>
            <p class="mt-2 text-sm text-slate-600">Choose payout method and send. Track status in real-time.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-slate-900 text-center">Powerful features built for business & individuals</h2>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="p-6 bg-gradient-to-b from-white to-blue-50 rounded-xl border border-slate-100">
            <i class="fa-solid fa-earth-americas text-blue-600 text-2xl"></i>
            <h4 class="mt-4 font-semibold">Multi-currency</h4>
            <p class="mt-2 text-slate-600 text-sm">Support for multiple currency flows and conversions with robust liquidity partners.</p>
          </div>

          <div class="p-6 bg-gradient-to-b from-white to-blue-50 rounded-xl border border-slate-100">
            <i class="fa-solid fa-lock text-blue-600 text-2xl"></i>
            <h4 class="mt-4 font-semibold">Regulatory compliance</h4>
            <p class="mt-2 text-slate-600 text-sm">KYC & AML checks to ensure safe operations and trust.</p>
          </div>

          <div class="p-6 bg-gradient-to-b from-white to-blue-50 rounded-xl border border-slate-100">
            <i class="fa-solid fa-comments text-blue-600 text-2xl"></i>
            <h4 class="mt-4 font-semibold">24/7 support</h4>
            <p class="mt-2 text-slate-600 text-sm">Dedicated support team to help you every step of the way.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Live rates (placeholder) -->
    <section id="rates" class="py-16 bg-blue-50">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-start justify-between">
          <div>
            <h3 class="text-xl font-bold">Live Exchange Rates (Indicative)</h3>
            <p class="mt-2 text-slate-600">Rates update periodically. Final rate confirmed on review.</p>
          </div>

          <div class="flex items-center gap-3">
            <button @click="refreshRates" :disabled="ratesLoading" class="px-3 py-2 rounded-lg border hover:bg-white">
              <i class="fa-solid fa-sync"></i>
            </button>
            <div class="text-sm text-slate-500">Last updated: {{ lastUpdatedText }}</div>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
          <div v-for="(r, i) in exchangeRates" :key="i" class="p-4 bg-white rounded-xl shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm text-slate-500">{{ r.pair }}</div>
                <div class="text-lg font-semibold">{{ r.rate }}</div>
              </div>
              <div class="text-sm text-slate-400">{{ r.charge }}</div>
            </div>
            <div class="mt-3 text-xs text-slate-500">Indicative</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-2xl font-bold">What customers say</h3>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="(t, idx) in testimonials" :key="idx" class="bg-white rounded-xl p-6 shadow">
            <p class="text-slate-700">“{{ t.text }}”</p>
            <div class="mt-4 flex items-center gap-3">
              <img :src="t.avatar" alt="" class="w-10 h-10 rounded-full object-cover" />
              <div class="text-sm">
                <div class="font-semibold">{{ t.name }}</div>
                <div class="text-xs text-slate-500">{{ t.title }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- App promo -->
    <section class="py-16 bg-gradient-to-b from-blue-600 to-blue-700 text-white">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        <div>
          <h3 class="text-3xl font-bold">Send money on the go</h3>
          <p class="mt-3 text-slate-100">Download the KGL Remit app to send and manage transfers from anywhere.</p>

          <div class="mt-6 flex gap-3">
            <a class="inline-flex items-center gap-3 bg-black/80 px-4 py-3 rounded-lg" href="#">
              <i class="fab fa-apple text-2xl"></i>
              <span class="text-left">
                <div class="text-xs">Download on the</div>
                <div class="font-semibold">App Store</div>
              </span>
            </a>

            <a class="inline-flex items-center gap-3 bg-black/80 px-4 py-3 rounded-lg" href="#">
              <i class="fab fa-google-play text-2xl"></i>
              <span class="text-left">
                <div class="text-xs">Get it on</div>
                <div class="font-semibold">Google Play</div>
              </span>
            </a>
          </div>
        </div>

        <div class="flex justify-center">
          <img src="/images/phone-mockup.png" alt="app" class="w-64 rounded-2xl shadow-2xl" />
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-16">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-2xl font-bold text-center">Frequently asked questions</h3>
        <div class="mt-8 space-y-3">
          <div v-for="(q, i) in faqs" :key="i" class="bg-white rounded-lg shadow">
            <button class="w-full p-4 text-left flex items-center justify-between" @click="toggleFaq(i)">
              <div>
                <div class="font-semibold text-slate-800">{{ q.q }}</div>
                <div class="text-sm text-slate-500">{{ q.summary }}</div>
              </div>
              <div class="ml-4">
                <i :class="['fa-solid', expandedFaq === i ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
              </div>
            </button>
            <div v-show="expandedFaq === i" class="px-4 pb-4 text-sm text-slate-600">
              {{ q.a }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="py-12 bg-blue-50">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h4 class="text-lg font-semibold">Get updates & rate alerts</h4>
        <p class="text-slate-600 mt-2">Subscribe to monthly updates about rates, promos and product news.</p>

        <form @submit.prevent="subscribe" class="mt-4 flex flex-col sm:flex-row gap-3 justify-center">
          <input v-model="newsletterEmail" type="email" required placeholder="you@company.com" class="px-4 py-3 rounded-lg border w-full sm:w-auto" />
          <button :disabled="subscribing" class="px-6 py-3 bg-blue-600 text-white rounded-lg">Subscribe</button>
        </form>

        <p v-if="newsletterMsg" class="mt-3 text-sm text-green-600">{{ newsletterMsg }}</p>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <div class="text-white font-bold text-lg">KGL Remit</div>
          <p class="mt-3 text-sm">Fast, secure and compliant remittance solutions for businesses and individuals.</p>
        </div>

        <div>
          <div class="font-semibold">Company</div>
          <ul class="mt-3 space-y-2 text-sm">
            <li><a href="#" class="hover:text-white">About</a></li>
            <li><a href="#" class="hover:text-white">Careers</a></li>
            <li><a href="#" class="hover:text-white">Blog</a></li>
          </ul>
        </div>

        <div>
          <div class="font-semibold">Products</div>
          <ul class="mt-3 space-y-2 text-sm">
            <li><a href="#" class="hover:text-white">Send Money</a></li>
            <li><a href="#" class="hover:text-white">Business Payments</a></li>
            <li><a href="#" class="hover:text-white">API & Integrations</a></li>
          </ul>
        </div>

        <div>
          <div class="font-semibold">Contact</div>
          <div class="mt-3 text-sm">
            <div>Email: support@kglremit.com</div>
            <div class="mt-1">Phone: +234 700 KGL-REMIT</div>
            <div class="mt-3 flex items-center gap-3">
              <a class="hover:text-white"><i class="fab fa-twitter"></i></a>
              <a class="hover:text-white"><i class="fab fa-linkedin"></i></a>
              <a class="hover:text-white"><i class="fab fa-facebook"></i></a>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-slate-800 text-center py-4">
        <div class="text-sm">© {{ new Date().getFullYear() }} KGL Remit. All rights reserved.</div>
      </div>
    </footer>

    <!-- Floating contact -->
    <a href="#contact" class="fixed right-6 bottom-6 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 hidden sm:flex items-center gap-2">
      <i class="fa-solid fa-headset"></i>
      <span class="text-sm">Support</span>
    </a>
  </div>
</template>

<script setup>
/**
 * Home.vue - KGL Remit homepage (Vue 3 + Inertia 1 + Tailwind 3)
 *
 * Notes:
 *  - Make sure Font Awesome CSS is included in the main layout <head>.
 *  - Replace placeholder images (/images/...) with your real assets.
 *  - exchangeRates is a placeholder list to be replaced with real API data.
 */

import { ref, reactive, computed } from 'vue'
import { Link } from '@inertiajs/vue3'

// sample partners (replace logos)
const partners = [
  { name: 'Paystack', logo: '/images/partner-paystack.png' },
  { name: 'Flutterwave', logo: '/images/partner-flutterwave.png' },
  { name: 'Providus', logo: '/images/partner-providus.png' },
  { name: 'UnionBank', logo: '/images/partner-union.png' },
]

// sample testimonials (replace)
const testimonials = [
  { name: 'Chinedu Okafor', title: 'SME Owner', avatar: '/images/avatar1.jpg', text: 'KGL Remit made payroll simple for our remote workers.' },
  { name: 'Aisha Bello', title: 'Freelancer', avatar: '/images/avatar2.jpg', text: 'Fast and reliable transfers, great exchange rates.' },
  { name: 'John Smith', title: 'Importer', avatar: '/images/avatar3.jpg', text: 'Great customer support and transparent fees.' }
]

// sample exchange rates (placeholder)
const exchangeRates = ref([
  { pair: 'USD → NGN', rate: '1:1,570', charge: '0.8%' },
  { pair: 'GBP → NGN', rate: '1:2,010', charge: '0.9%' },
  { pair: 'EUR → NGN', rate: '1:1,650', charge: '0.85%' },
  { pair: 'USD → GHS', rate: '1:12.34', charge: '0.9%' },
])

const ratesLoading = ref(false)
const lastUpdated = ref(new Date())

function refreshRates() {
  ratesLoading.value = true
  // simulate refresh (replace with real API call)
  setTimeout(() => {
    // simulate small change
    exchangeRates.value = exchangeRates.value.map(r => ({ ...r }))
    lastUpdated.value = new Date()
    ratesLoading.value = false
  }, 900)
}

const lastUpdatedText = computed(() => {
  return lastUpdated.value ? lastUpdated.value.toLocaleString() : '—'
})

// sample small calculator widget data
const sampleAmount = ref(100)
const sampleRate = ref('1:1,570')

const formattedEst = computed(() => {
  // very simple formatting placeholder
  const num = Number(sampleAmount.value || 0)
  // parse rate '1:1570' => 1570
  const r = Number(sampleRate.value.split(':')[1]?.replace(/,/g, '') || 1)
  return isFinite(num * r) ? (num * r).toLocaleString() : '—'
})

function calculateSample() {
  // this would call actual calculate endpoint later
  // small UI feedback:
  sampleRate.value = sampleRate.value // no-op
}

// FAQs
const faqs = ref([
  { q: 'How long does a transfer take?', summary: 'Typical transfers are near-instant to 24 hours', a: 'Most transfers complete within minutes to a few hours depending on payout method and bank.', id: 1 },
  { q: 'What are your fees?', summary: 'Transparent & low fees', a: 'Fees depend on currency pair and payout method. You can preview fees during transfer creation.', id: 2 },
  { q: 'Is KGL Remit regulated?', summary: 'Yes — we follow KYC/AML rules', a: 'We are compliant with local regulations and perform KYC checks where required.', id: 3 },
])
const expandedFaq = ref(null)
function toggleFaq(i) {
  expandedFaq.value = expandedFaq.value === i ? null : i
}

// Newsletter
const newsletterEmail = ref('')
const newsletterMsg = ref('')
const subscribing = ref(false)
function subscribe() {
  subscribing.value = true
  setTimeout(() => {
    newsletterMsg.value = 'Thanks — you are subscribed!'
    newsletterEmail.value = ''
    subscribing.value = false
  }, 900)
}
</script>

<style scoped>
/* small tweaks for better visuals */
a[aria-current] {
  font-weight: 600;
}
</style>
