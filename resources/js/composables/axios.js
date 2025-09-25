import { ref } from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'

export async function useAxios(url, loadingMsg, props = {}, method = 'post') {
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

    // 🟢 Filter out null/undefined/empty string values
    const cleanProps = {}
    Object.entries(props).forEach(([key, val]) => {
      if (val !== null && val !== undefined && val !== '') {
        cleanProps[key] = val
      }
    })

    // Build URL with clean params
    let fullUrl = url
    if (Object.keys(cleanProps).length > 0) {
      const queryString = new URLSearchParams(cleanProps).toString()
      fullUrl += (url.includes('?') ? '&' : '?') + queryString
    }

    response.value = await axios({
      url: fullUrl,
      method,
    })

    swalInstance.close()
    return { response, loading }

  } catch (err) {
    Swal.close()

    if (err.response?.status === 419) {
      document.location.reload()
    }

    throw err
  } finally {
    loading.value = false
  }
}
