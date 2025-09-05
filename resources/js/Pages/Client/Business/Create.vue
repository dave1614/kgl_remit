<script setup>
import { ref, reactive, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { useMainStore } from '@/Stores/main.js'
import LoginInput from '@/Components/LoginInput.vue'
import LoginSelect from '@/Components/LoginSelect.vue'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'

const props = defineProps({
    props_states: Array,
    props_cities: Array,
    state: Number,
    city: Number,
    registration: Object,
})

const mainStore = useMainStore()
const states = ref(props.props_states)
const cities = ref(props.props_cities)
const loading_loca_details = ref(false)

// Allowed file types and max size
const FILE_TYPES = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
const MAX_SIZE = 5 * 1024 * 1024 // 5MB

// Track file errors and previews
const fileErrors = reactive({
    certificate: null,
    moa: null,
    cosc: null,
    valid_id: null
})
const filePreviews = reactive({
    certificate: props.registration.certificate && isImage(props.registration.certificate) ? props.registration.certificate : null,
    moa: props.registration.moa && isImage(props.registration.moa) ? props.registration.moa : null,
    cosc: props.registration.cosc && isImage(props.registration.cosc) ? props.registration.cosc : null,
    valid_id: props.registration.valid_id && isImage(props.registration.valid_id) ? props.registration.valid_id : null,
})

// Helper to check if URL is an image
function isImage(url) {
    return /\.(jpg|jpeg|png)$/i.test(url)
}

const loadNewCities = async () => {
    if (loading_loca_details.value) return
    loading_loca_details.value = true

    try {
        const response = await axios.post(route('client.location.get_new_lgas_and_wards', form.state), { show_records: true })
        if (response.data.cities.length > 0) {
            cities.value = response.data.cities
            form.city = cities.value[0].id
        } else {
            mainStore.toast = 'Something went wrong'
        }
    } catch (error) {
        mainStore.toast = 'Something went wrong'
        console.log(error)
    } finally {
        loading_loca_details.value = false
    }
}

const form = useForm({
    business_name: props.registration.business_name,
    state: props.state,
    city: props.city,
    address: props.registration.address,
    postal_code: props.registration.postal_code,
    certificate: null,
    moa: null,
    cosc: null,
    valid_id_type: props.registration.valid_id_type,
    valid_id: null,
})

// File upload with validation and preview
function handleFileUpload(event, field) {
    const file = event.target.files[0]
    if (!file) return

    fileErrors[field] = null

    if (!FILE_TYPES.includes(file.type)) {
        fileErrors[field] = 'Invalid file type. Only PDF, JPG, JPEG, PNG allowed.'
        form[field] = null
        filePreviews[field] = null
        return
    }

    if (file.size > MAX_SIZE) {
        fileErrors[field] = 'File too large. Max size is 5MB.'
        form[field] = null
        filePreviews[field] = null
        return
    }

    form[field] = file

    // Set preview for images only
    if (file.type.startsWith('image/')) {
        const reader = new FileReader()
        reader.onload = e => {
            filePreviews[field] = e.target.result
        }
        reader.readAsDataURL(file)
    } else {
        filePreviews[field] = null
    }
}

const register = () => {
    // Prevent submission if any file error exists
    const hasFileError = Object.values(fileErrors).some(e => e !== null)
    if (hasFileError) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid file(s)',
            text: 'Please fix the highlighted file errors before submitting.'
        })
        return
    }

    if (!form.processing) {
        form.post(route('client.business.store'), {
            preserveScroll: true,
            onError: (errors) => {
                const size = Object.keys(errors).length
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: `There are ${size} form errors. Please fix them`,
                })
            },
        })
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-blue-100 p-4 py-9">
        <div class="bg-white rounded-3xl shadow-2xl flex flex-col lg:flex-row w-full max-w-3xl overflow-hidden">

            <div class="w-full p-8 sm:p-16">
                <div class="flex justify-between">
                    <Link as="button" method="post" :href="route('logout')"
                        class="inline-flex items-center font-bold text-red-500 mb-6">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Logout
                    </Link>
                </div>

                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2 mt-4">Business Registration</h2>
                <p class="text-gray-400 mb-8">Submit your business details for verification before you can start
                    transacting</p>

                <form @submit.prevent="register" class="space-y-5" autocomplete="off">
                    <LoginInput v-model="form.business_name" :error="form.errors.business_name" type="text"
                        placeholder="Business Name" icon="fa-regular fa-building" />

                    <!-- State & City -->
                    <LoginSelect v-model.number="form.state" :options="states" icon="fa-solid fa-globe"
                        :error="form.errors.state" placeholder="Select state" @change="loadNewCities"
                        :loading="loading_loca_details" />
                    <LoginSelect v-model.number="form.city" :options="cities" icon="fa-solid fa-city"
                        :error="form.errors.city" :loading="loading_loca_details" />

                    <LoginInput v-model="form.address" :error="form.errors.address" type="text"
                        placeholder="Street Address" icon="fa-solid fa-location-dot" />
                    <LoginInput v-model="form.postal_code" :error="form.errors.postal_code" type="text"
                        placeholder="Postal Code" icon="fa-solid fa-mail-bulk" />

                    <!-- Document Uploads -->
                    <div class="space-y-3">
                        <!-- Certificate -->
                        <label class="block text-gray-500 font-medium">Certificate of Incorporation / Registration <span
                                class="text-red-500">*</span></label>
                        <div v-if="props.registration.certificate && !filePreviews.certificate">
                            <template v-if="props.registration.certificate.endsWith('.pdf')">
                                <a :href="props.registration.certificate_url" target="_blank"
                                    class="text-blue-500 underline">View uploaded Certificate (PDF)</a>
                            </template>
                            <template v-else>
                                <img :src="props.registration.certificate_url" class="w-32 mt-2 rounded" />
                            </template>
                        </div>
                        <input type="file" @change="handleFileUpload($event, 'certificate')"
                            class="w-full border rounded-lg p-3" />
                        <p v-if="form.errors.certificate" class="text-red-500 text-sm mt-1">{{ form.errors.certificate
                            }}</p>
                        <img v-if="filePreviews.certificate" :src="filePreviews.certificate"
                            class="w-32 mt-2 rounded" />

                        <!-- MOA -->
                        <label class="block text-gray-500 font-medium">Memorandum and Articles of Association <span
                                class="text-red-500">*</span></label>
                        <div v-if="props.registration.moa && !filePreviews.moa">
                            <template v-if="props.registration.moa.endsWith('.pdf')">
                                <a :href="props.registration.moa_url" target="_blank" class="text-blue-500 underline">View
                                    uploaded MOA (PDF)</a>
                            </template>
                            <template v-else>
                                <img :src="props.registration.moa_url" class="w-32 mt-2 rounded" />
                            </template>
                        </div>
                        <input type="file" @change="handleFileUpload($event, 'moa')"
                            class="w-full border rounded-lg p-3" />
                        <p v-if="form.errors.moa" class="text-red-500 text-sm mt-1">{{ form.errors.moa }}</p>
                        <img v-if="filePreviews.moa" :src="filePreviews.moa" class="w-32 mt-2 rounded" />

                        <!-- COSC -->
                        <label class="block text-gray-500 font-medium">Certificate of Share Capital / Statement of
                            Shareholders <span class="text-red-500">*</span></label>
                        <div v-if="props.registration.cosc && !filePreviews.cosc">
                            <template v-if="props.registration.cosc.endsWith('.pdf')">
                                <a :href="props.registration.cosc_url" target="_blank" class="text-blue-500 underline">View
                                    uploaded COSC (PDF)</a>
                            </template>
                            <template v-else>
                                <img :src="props.registration.cosc_url" class="w-32 mt-2 rounded" />
                            </template>
                        </div>
                        <input type="file" @change="handleFileUpload($event, 'cosc')"
                            class="w-full border rounded-lg p-3" />
                        <p v-if="form.errors.cosc" class="text-red-500 text-sm mt-1">{{ form.errors.cosc }}</p>
                        <img v-if="filePreviews.cosc" :src="filePreviews.cosc" class="w-32 mt-2 rounded" />

                        <!-- Valid ID -->
                        <label class="block text-gray-500 font-medium">Valid ID of Business Owner <span
                                class="text-red-500">*</span></label>
                        <select v-model="form.valid_id_type"
                            class="w-full border rounded-lg p-3 focus:ring focus:outline-none">
                            <option value="null">Select Valid ID Type</option>
                            <option>National Identity Card (NIMC)</option>
                            <option>Permanent Voters Card (PVC)</option>
                            <option>International Passport</option>
                            <option>Drivers License</option>
                        </select>
                        <p v-if="form.errors.valid_id_type" class="text-red-500 text-sm mt-1">{{
                            form.errors.valid_id_type }}</p>

                        <div v-if="props.registration.valid_id && !filePreviews.valid_id">
                            <template v-if="props.registration.valid_id.endsWith('.pdf')">
                                <a :href="props.registration.valid_id" target="_blank"
                                    class="text-blue-500 underline">View uploaded Valid ID (PDF)</a>
                            </template>
                            <template v-else>
                                <img :src="props.registration.valid_id" class="w-32 mt-2 rounded" />
                            </template>
                        </div>
                        <input type="file" @change="handleFileUpload($event, 'valid_id')"
                            class="w-full border rounded-lg p-3" />
                        <p v-if="form.errors.valid_id" class="text-red-500 text-sm mt-1">{{ form.errors.valid_id }}</p>
                    </div>



                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button type="submit" :disabled="form.processing"
                            class="w-1/2 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white py-2 px-3 rounded-full font-semibold transition-all duration-300 relative">
                            <span class="inline-block mt-1 font-bold">Submit Registration</span>
                            <img v-if="form.processing" class="inline-block w-7 h-6 mt-1 float-right"
                                :src="FormLoaderLight" alt="">
                            <span v-else
                                class="ml-3 flex items-center justify-center w-8 h-8 p-2 bg-white/15 rounded-full float-right">
                                <font-awesome-icon icon="fa-solid fa-arrow-right" class="text-white text-xs" />
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
