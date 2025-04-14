<template>
    <div class="min-h-screen bg-secondary-900 py-8 px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gradient">EcoLogix</h1>
                <p class="text-secondary-400 mt-2">Śledzenie przesyłki</p>
            </div>

            <!-- Search box if no tracking -->
            <div v-if="!uuid" class="glass-card p-6 mb-8">
                <form @submit.prevent="search">
                    <label class="input-label">Numer przesyłki</label>
                    <div class="flex gap-2">
                        <input
                            v-model="queryInput"
                            type="text"
                            placeholder="ECO24XXXXXXXX"
                            class="input flex-1 font-mono"
                        />
                        <button type="submit" class="btn-primary">Szukaj</button>
                    </div>
                </form>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center py-12">
                <div class="spinner w-12 h-12 border-4 border-primary-500 mx-auto"></div>
            </div>

            <!-- Error -->
            <div v-else-if="error" class="glass-card p-6 text-center">
                <p class="text-red-400 mb-4">{{ error }}</p>
                <button @click="error = null" class="btn-secondary">Spróbuj ponownie</button>
            </div>

            <!-- Tracking data -->
            <div v-else-if="shipment" class="space-y-6 animate-in">
                <!-- Status card -->
                <div class="glass-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="font-mono text-xl text-primary-400">{{ shipment.tracking_number }}</span>
                        <span :class="['badge text-base', `badge-${getStatusColor(shipment.status.value)}`]">
                            {{ shipment.status.label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-secondary-400">Z:</span>
                            <p class="text-white">{{ shipment.sender.city }}</p>
                        </div>
                        <div>
                            <span class="text-secondary-400">Do:</span>
                            <p class="text-white">{{ shipment.recipient.city }}</p>
                        </div>
                    </div>

                    <div v-if="shipment.delivery.is_delayed" class="mt-4 p-3 bg-red-500/20 rounded-lg text-red-400 text-sm">
                        ⚠️ Przesyłka jest opóźniona
                    </div>
                </div>

                <!-- Timeline -->
                <div class="glass-card p-6">
                    <h3 class="font-semibold text-white mb-4">Historia przesyłki</h3>
                    <div class="space-y-4">
                        <div
                            v-for="(event, index) in shipment.timeline"
                            :key="index"
                            class="flex gap-4"
                        >
                            <div class="flex flex-col items-center">
                                <div :class="['w-3 h-3 rounded-full', index === 0 ? 'bg-primary-500' : 'bg-secondary-600']"></div>
                                <div v-if="index < shipment.timeline.length - 1" class="w-0.5 flex-1 bg-secondary-700 mt-1"></div>
                            </div>
                            <div class="flex-1 pb-4">
                                <p class="font-medium text-white">{{ event.title }}</p>
                                <p v-if="event.location" class="text-sm text-secondary-400">{{ event.location }}</p>
                                <p class="text-xs text-secondary-500 mt-1">{{ formatDate(event.timestamp) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';
import { format } from 'date-fns';
import { pl } from 'date-fns/locale';

const route = useRoute();
const router = useRouter();

const uuid = computed(() => route.params.uuid);
const shipment = ref(null);
const loading = ref(false);
const error = ref(null);
const queryInput = ref('');

onMounted(async () => {
    if (uuid.value) {
        await fetchTracking();
    }
});

async function fetchTracking() {
    loading.value = true;
    error.value = null;

    try {
        const response = await api.get(`/tracking/${uuid.value}`);
        shipment.value = response.data.data;
    } catch (e) {
        if (e.response?.status === 404) {
            error.value = 'Nie znaleziono przesyłki o podanym numerze';
        } else {
            error.value = 'Wystąpił błąd podczas pobierania danych';
        }
    } finally {
        loading.value = false;
    }
}

function search() {
    if (queryInput.value) {
        router.push({ name: 'tracking.public', params: { uuid: queryInput.value } });
    }
}

function formatDate(dateString) {
    if (!dateString) return '';
    return format(new Date(dateString), 'dd MMM yyyy, HH:mm', { locale: pl });
}

function getStatusColor(status) {
    const colors = {
        pending: 'neutral',
        in_transit: 'info',
        out_for_delivery: 'warning',
        delivered: 'success',
        failed_delivery: 'danger',
    };
    return colors[status] || 'neutral';
}
</script>

// update 161 

// update 204 

// update 230 

// update 348 

// update 363 

// update 413 

// u245

// u346
