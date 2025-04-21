import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const useDashboardStore = defineStore('dashboard', () => {
    const overview = ref(null);
    const fleetUtilization = ref(null);
    const deliveryTrends = ref({});
    const statusBreakdown = ref({});
    const topCities = ref([]);
    const driverPerformance = ref([]);
    const recentActivity = ref([]);
    const loading = ref(false);

    async function fetchOverview() {
        loading.value = true;
        try {
            const response = await api.get('/v1/dashboard/overview');
            overview.value = response.data.data;
        } finally {
            loading.value = false;
        }
    }

    async function fetchFleetUtilization() {
        const response = await api.get('/v1/dashboard/fleet-utilization');
        fleetUtilization.value = response.data.data;
    }

    async function fetchDeliveryTrends(days = 30) {
        const response = await api.get('/v1/dashboard/delivery-trends', { params: { days } });
        deliveryTrends.value = response.data.data;
    }

    async function fetchStatusBreakdown() {
        const response = await api.get('/v1/dashboard/status-breakdown');
        statusBreakdown.value = response.data.data;
    }

    async function fetchTopCities(limit = 10) {
        const response = await api.get('/v1/dashboard/top-cities', { params: { limit } });
        topCities.value = response.data.data;
    }

    async function fetchDriverPerformance() {
        const response = await api.get('/v1/dashboard/driver-performance');
        driverPerformance.value = response.data.data;
    }

    async function fetchRecentActivity(limit = 20) {
        const response = await api.get('/v1/dashboard/recent-activity', { params: { limit } });
        recentActivity.value = response.data.data;
    }

    async function fetchAll() {
        await Promise.all([
            fetchOverview(),
            fetchFleetUtilization(),
            fetchDeliveryTrends(),
            fetchStatusBreakdown(),
            fetchRecentActivity(),
        ]);
    }

    return {
        overview,
        fleetUtilization,
        deliveryTrends,
        statusBreakdown,
        topCities,
        driverPerformance,
        recentActivity,
        loading,
        fetchOverview,
        fetchFleetUtilization,
        fetchDeliveryTrends,
        fetchStatusBreakdown,
        fetchTopCities,
        fetchDriverPerformance,
        fetchRecentActivity,
        fetchAll,
    };
});

// update 211 

// update 253 

// u267
