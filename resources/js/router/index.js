import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        component: () => import('@/layouts/MainLayout.vue'),
        children: [
            { path: '', name: 'dashboard', component: () => import('@/pages/Dashboard.vue') },
            { path: 'fleet', name: 'fleet', component: () => import('@/pages/Fleet.vue') },
            { path: 'shipments', name: 'shipments', component: () => import('@/pages/Shipments.vue') },
            { path: 'vehicles', name: 'vehicles', component: () => import('@/pages/Vehicles.vue') },
            { path: 'drivers', name: 'drivers', component: () => import('@/pages/Drivers.vue') },
            { path: 'analytics', name: 'analytics', component: () => import('@/pages/Analytics.vue') },
            { path: 'settings', name: 'settings', component: () => import('@/pages/Settings.vue') },
            { path: 'leaderboard', name: 'leaderboard', component: () => import('@/pages/Leaderboard.vue') },
            { path: 'calculator', name: 'calculator', component: () => import('@/pages/Calculator.vue') },
            { path: 'calendar', name: 'calendar', component: () => import('@/pages/Calendar.vue') },
        ]
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/pages/auth/Login.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

// update 177 

// update 271 

// update 282 

// update 381 

// u94

// u363
