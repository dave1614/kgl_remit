import '../css/main.css'

import { createPinia } from 'pinia'
import { useDarkModeStore } from '@/Stores/darkMode.js'
import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'
import swal from 'sweetalert2';
import AOS from 'aos'
import 'aos/dist/aos.css'
import 'mediaelement/standalone';
import EmojiPicker from 'vue3-emoji-picker'
import 'vue3-emoji-picker/css'
import axios from 'axios';
window.axios = axios;

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import icons and add them to the Library */
import { faUserSecret, faThumbsUp, faUser, faLock, faArrowRight, faMobileScreen, faFileInvoiceDollar, faGlobe, faMap, faCity, faLocationDot, faMailBulk } from '@fortawesome/free-solid-svg-icons'
import { faUser as faUserRegular, faEnvelope, faCircleUser, faBuilding } from '@fortawesome/free-regular-svg-icons'
import { faFacebook } from '@fortawesome/free-brands-svg-icons'

library.add(faUserSecret, faThumbsUp, faFacebook, faUser, faUserRegular, faEnvelope, faLock, faCircleUser, faArrowRight, faMobileScreen, faFileInvoiceDollar, faBuilding, faGlobe, faMap, faCity, faLocationDot, faMailBulk)




window.Swal = swal;

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Weconnect'

const pinia = createPinia()

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .use(AOS.init())
      .use(ZiggyVue, Ziggy)
      .component('EmojiPicker', EmojiPicker)
      .component('font-awesome-icon', FontAwesomeIcon)
      .mount(el)
  },
  progress: {
    color: '#4B5563'
  }
})

// const darkModeStore = useDarkModeStore(pinia)

// if (
//   (!localStorage['darkMode'] && window.matchMedia('(prefers-color-scheme: dark)').matches) ||
//   localStorage['darkMode'] === '1'
// ) {
//   darkModeStore.set(true)
// }
