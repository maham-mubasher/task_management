import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../pages/HomePage.vue';

const routes = [
    { path: '/', name: 'Home', component: HomePage },
    { path: '/:pathMatch(.*)*', redirect: () => window.location.pathname }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
