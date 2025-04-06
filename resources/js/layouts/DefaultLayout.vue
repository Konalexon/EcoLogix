<template>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-secondary-800 border-r border-secondary-700 flex flex-col fixed h-full">
            <!-- Logo -->
            <div class="p-6 border-b border-secondary-700">
                <h1 class="text-2xl font-bold text-gradient">EcoLogix</h1>
                <p class="text-xs text-secondary-400 mt-1">Logistics Management</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 overflow-y-auto">
                <ul class="space-y-1">
                    <li v-for="item in navItems" :key="item.name">
                        <RouterLink
                            :to="item.to"
                            :class="[
                                'nav-link',
                                { 'active': isActive(item.to) }
                            ]"
                        >
                            <component :is="item.icon" class="w-5 h-5" />
                            <span>{{ item.label }}</span>
                            <span
                                v-if="item.badge"
                                class="ml-auto bg-primary-500/20 text-primary-400 text-xs px-2 py-0.5 rounded-full"
                            >
                                {{ item.badge }}
                            </span>
                        </RouterLink>
                    </li>
                </ul>
            </nav>

            <!-- User section -->
            <div class="p-4 border-t border-secondary-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-500/20 flex items-center justify-center text-primary-400 font-semibold">
                        {{ authStore.user?.initials || 'U' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">
                            {{ authStore.user?.full_name || 'User' }}
                        </p>
                        <p class="text-xs text-secondary-400 truncate">
                            {{ authStore.user?.email }}
                        </p>
                    </div>
                    <button
                        @click="handleLogout"
                        class="p-2 text-secondary-400 hover:text-white hover:bg-secondary-700 rounded-lg transition-colors"
                        title="Wyloguj"
                    >
                        <ArrowRightOnRectangleIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 ml-64">
            <!-- Top bar -->
            <header class="h-16 bg-secondary-800/50 backdrop-blur-xl border-b border-secondary-700 flex items-center justify-between px-6 sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-semibold text-white">{{ pageTitle }}</h2>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Search -->
                    <div class="relative">
                        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-secondary-400" />
                        <input
                            type="text"
                            placeholder="Szukaj przesyłek..."
                            class="input pl-10 w-64"
                        />
                    </div>

                    <!-- Notifications -->
                    <button class="relative p-2 text-secondary-400 hover:text-white hover:bg-secondary-700 rounded-lg transition-colors">
                        <BellIcon class="w-5 h-5" />
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>
            </header>

            <!-- Page content -->
            <div class="p-6">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import {
    HomeIcon,
    TruckIcon,
    CubeIcon,
    MapIcon,
    BuildingOfficeIcon,
    UsersIcon,
    CogIcon,
    BellIcon,
    MagnifyingGlassIcon,
    ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const navItems = [
    { name: 'dashboard', to: '/', label: 'Dashboard', icon: HomeIcon },
    { name: 'shipments', to: '/shipments', label: 'Przesyłki', icon: CubeIcon },
    { name: 'fleet', to: '/fleet', label: 'Flota', icon: TruckIcon },
    { name: 'routes', to: '/routes', label: 'Trasy', icon: MapIcon },
    { name: 'warehouses', to: '/warehouses', label: 'Magazyny', icon: BuildingOfficeIcon },
    { name: 'drivers', to: '/drivers', label: 'Kierowcy', icon: UsersIcon },
    { name: 'settings', to: '/settings', label: 'Ustawienia', icon: CogIcon },
];

const pageTitle = computed(() => {
    const titles = {
        dashboard: 'Dashboard',
        shipments: 'Przesyłki',
        'shipment.show': 'Szczegóły przesyłki',
        fleet: 'Mapa Floty',
        routes: 'Trasy',
        'route.show': 'Szczegóły trasy',
        warehouses: 'Magazyny',
        drivers: 'Kierowcy',
        settings: 'Ustawienia',
    };
    return titles[route.name] || 'EcoLogix';
});

function isActive(to) {
    if (to === '/') return route.path === '/';
    return route.path.startsWith(to);
}

async function handleLogout() {
    authStore.logout();
    await router.push('/login');
}
</script>

// update 149 

// update 301 

// u126

// u133

// u334

// u377
