import { createApp } from 'vue'
import { Quasar, Notify, Dialog } from 'quasar'
import { createPinia } from 'pinia'
import router from './router'

// Import icon libraries
import '@quasar/extras/material-icons/material-icons.css'
// Import Quasar css
import 'quasar/src/css/index.sass'

import App from './App.vue'

const myApp = createApp(App)

myApp.use(createPinia())
myApp.use(router)
myApp.use(Quasar, {
  plugins: {
    Notify,
    Dialog
  }
})

myApp.mount('#app')
