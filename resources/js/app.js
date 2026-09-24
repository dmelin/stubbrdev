import { createApp } from 'vue';
import App from './App.vue';
import RouterLink from './components/ui/RouterLink.vue';

createApp(App)
    .component('RouterLink', RouterLink)
    .mount('#app');
