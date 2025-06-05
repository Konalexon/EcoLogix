import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/services/api';

export const useShipmentsStore = defineStore('shipments', () => {
    const shipments = ref([]);
    const currentShipment = ref(null);
    const loading = ref(false);
    const pagination = ref({ total: 0, page: 1, perPage: 20 });
    const filters = ref({});

    const pendingCount = computed(() =>
        shipments.value.filter(s => s.status.value === 'pending').length
    );

    const delayedCount = computed(() =>
        shipments.value.filter(s => s.delivery?.is_delayed).length
    );

    async function fetchShipments(params = {}) {
        loading.value = true;
        try {
            const response = await api.get('/v1/shipments', {
                params: { ...filters.value, ...params }
            });
            shipments.value = response.data.data;
            pagination.value = {
                total: response.data.meta.total,
                page: response.data.meta.current_page,
                perPage: response.data.meta.per_page,
            };
        } finally {
            loading.value = false;
        }
    }

    async function fetchShipment(id) {
        loading.value = true;
        try {
            const response = await api.get(`/v1/shipments/${id}`);
            currentShipment.value = response.data.data;
            return currentShipment.value;
        } finally {
            loading.value = false;
        }
    }

    async function createShipment(data) {
        const response = await api.post('/v1/shipments', data);
        shipments.value.unshift(response.data.data);
        return response.data.data;
    }

    async function updateStatus(id, status, metadata = {}) {
        const response = await api.patch(`/v1/shipments/${id}/status`, { status, metadata });
        const index = shipments.value.findIndex(s => s.id === id);
        if (index !== -1) {
            shipments.value[index] = response.data.data;
        }
        if (currentShipment.value?.id === id) {
            currentShipment.value = response.data.data;
        }
        return response.data.data;
    }

    async function bulkUpdateStatus(ids, status) {
        const response = await api.post('/v1/shipments/bulk-status', { ids, status });
        await fetchShipments();
        return response.data.data;
    }

    async function fetchStatistics() {
        const response = await api.get('/v1/shipments/statistics');
        return response.data.data;
    }

    function setFilters(newFilters) {
        filters.value = { ...filters.value, ...newFilters };
    }

    function clearFilters() {
        filters.value = {};
    }

    return {
        shipments,
        currentShipment,
        loading,
        pagination,
        filters,
        pendingCount,
        delayedCount,
        fetchShipments,
        fetchShipment,
        createShipment,
        updateStatus,
        bulkUpdateStatus,
        fetchStatistics,
        setFilters,
        clearFilters,
    };
});

// update 101 

// u143

// u257

// bwchlqpq
// vjomfwa4