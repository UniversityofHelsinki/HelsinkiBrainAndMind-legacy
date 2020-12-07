import { createApp } from 'vue'
import App from './App.vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import '../node_modules/modern-normalize/modern-normalize.css';


const app = createApp(App)
  .use(VueAxios, axios);

app.config.globalProperties.axios = axios;

app.mount('#app');
