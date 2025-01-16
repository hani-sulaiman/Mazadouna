import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
store.dispatch('auth/initializeAuth');
createApp(App).use(router).use(store).mount('body');
