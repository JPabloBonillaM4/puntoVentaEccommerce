import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
// Vue
import { createApp } from 'vue';
// COMPONENTS
import loginComponent from './components/login/index.vue';
import headerComponent from './components/admin/layouts/header.vue';
import sidebarComponent from './components/admin/layouts/sidebar.vue';
import footerComponent from './components/admin/layouts/footer.vue';
import dashboardComponent from './components/admin/dashboard/index.vue';
// Init vue
const app = createApp({
    components : {
        "login-component" : loginComponent,
        "header-component" : headerComponent,
        "sidebar-component" : sidebarComponent,
        "footer-component" : footerComponent,
        "dashboard-component" : dashboardComponent,
    }
});
// Mount
app.mount('#app');