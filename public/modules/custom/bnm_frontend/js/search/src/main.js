import { createApp } from 'vue'
import App from './App.vue'
import '../node_modules/modern-normalize/modern-normalize.css';
import axios from 'axios';
import VueAxios from "vue-axios";

const environment = document.querySelector("#app").getAttribute('data-environment');

axios.defaults.baseURL = environment;

const app = createApp(App);
app.use(VueAxios, axios);
app.config.globalProperties.$environment = environment;
app.mount('#app');
