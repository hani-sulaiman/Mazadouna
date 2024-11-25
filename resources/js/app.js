import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import apiClient from './axios';

// Initialize authentication state
store.dispatch('auth/initializeAuth');

// Create and mount the app
createApp(App).use(router).use(store).mount('body');

setInterval(function() {
    apiClient.get('/run-scheduled-task')
        .then(response => {
            console.log('Response:', response.data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}, 60000);