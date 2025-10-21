// src/main.js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import VueLazyLoad from 'vue3-lazyload'

// FontAwesome imports
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {
  faSearch,
  faShoppingCart,
  faUser,
  faBars,
  faTimes,
  faHome,
  faShop,
  faInfoCircle,
  faBlog,
  faPhone,
  faBriefcase,
  faTruck,
  faDashboard,
  faBox,
  faMapMarkerAlt,
  faSignOut,
  faEye,
  faHeart,
  faTachometerAlt,
  faSignOutAlt,
  faEyeSlash,
  faPlus,
  faMinus,
  faTrash,
  faCheck,
  faArrowRight,
  faArrowLeft,
  faSpinner,
  faStar,
  faFilter,
  faChevronDown,
  faChevronUp,
  faChevronLeft,
  faChevronRight,
  faEnvelope,
  faLock,
  faUserCircle,
  faEdit,
  faSave,
  faCancel,
  faUpload,
  faDownload,
  faFileAlt,
  faClipboard,
  faQuestionCircle,
  faExclamationTriangle,
  faCheckCircle,
  faTimesCircle,
  faInfoCircle as faInfo,
  faCarSide,
  faWrench,
  faSeedling,
  faShip,
  faCube,
  faTruckRampBox,
  faMotorcycle,
} from '@fortawesome/free-solid-svg-icons'

import {
  faGoogle,
  faApple,
  faFacebook,
  faTwitter,
  faInstagram,
  faLinkedin,
} from '@fortawesome/free-brands-svg-icons'

import App from './App.vue'
import router from './router'
import './assets/styles/main.css'

// Add icons to library
library.add(
  faSearch,
  faShoppingCart,
  faHeart,
  faTachometerAlt,
  faSignOutAlt,
  faUser,
  faBars,
  faTimes,
  faHome,
  faShop,
  faInfoCircle,
  faBlog,
  faPhone,
  faBriefcase,
  faTruck,
  faDashboard,
  faBox,
  faMapMarkerAlt,
  faSignOut,
  faEye,
  faEyeSlash,
  faPlus,
  faMinus,
  faTrash,
  faCheck,
  faArrowRight,
  faArrowLeft,
  faSpinner,
  faStar,
  faFilter,
  faChevronDown,
  faChevronUp,
  faChevronLeft,
  faChevronRight,
  faEnvelope,
  faLock,
  faUserCircle,
  faEdit,
  faSave,
  faCancel,
  faUpload,
  faDownload,
  faFileAlt,
  faClipboard,
  faQuestionCircle,
  faExclamationTriangle,
  faCheckCircle,
  faTimesCircle,
  faInfo,
  faGoogle,
  faApple,
  faFacebook,
  faTwitter,
  faInstagram,
  faLinkedin,
  faCarSide,
  faWrench,
  faSeedling,
  faShip,
  faCube,
  faTruckRampBox,
  faMotorcycle,
)

const app = createApp(App)

// Register FontAwesome component globally
app.component('font-awesome-icon', FontAwesomeIcon)

app.use(createPinia())
app.use(router)
app.use(Toast, {
  position: 'top-right',
  timeout: 3000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false,
})

app.use(VueLazyLoad, {
  // Optional: default placeholder (you can use a spinner, blurred image, etc.)
  loading: '../public/images/oil-droplet.gif',
  error: '../public/images/oil-droplet.jpg'
})

app.mount('#app')
