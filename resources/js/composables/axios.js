import { ref } from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'

export async function useAxios(url, loadingMsg, props = {}, real = false, method = 'post') {
    const loading = ref(false)
    const response = ref(null)

    try {
        loading.value = true

        // Show loading Swal
        const swalInstance = Swal.fire({
            title: 'Please wait',
            html: loadingMsg,
            icon: 'info',
            showConfirmButton: false,
            allowEscapeKey: false,
            allowOutsideClick: false,
        })

        const config = real ? { params: props } : { data: props }

        response.value = await axios({
            url,
            method,
            ...config,
        })

        swalInstance.close()
        return { response, loading }

    } catch (err) {
        Swal.close()

        // Handle CSRF error
        if (err.response?.status === 419) {
            document.location.reload()
        }

        // **Important:** rethrow the error so front-end can catch it
        throw err
    } finally {
        loading.value = false
    }
}
