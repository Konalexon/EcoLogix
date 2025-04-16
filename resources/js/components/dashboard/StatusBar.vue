<template>
    <div class="flex items-center gap-3">
        <div class="flex-1">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm text-secondary-300 capitalize">{{ statusLabel }}</span>
                <span class="text-sm font-medium text-white">{{ count }}</span>
            </div>
            <div class="h-2 bg-secondary-700 rounded-full overflow-hidden">
                <div
                    :class="['h-full rounded-full transition-all duration-500', statusColor]"
                    :style="{ width: `${percentage}%` }"
                ></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: String,
    count: Number,
    total: Number,
});

const percentage = computed(() => {
    if (!props.total) return 0;
    return Math.round((props.count / props.total) * 100);
});

const statusLabels = {
    pending: 'Oczekujące',
    confirmed: 'Potwierdzone',
    picked_up: 'Odebrane',
    warehouse_sorting: 'Sortowanie',
    in_transit: 'W trasie',
    out_for_delivery: 'Doręczanie',
    delivered: 'Dostarczone',
    failed_delivery: 'Nieudane',
    returned: 'Zwrócone',
    cancelled: 'Anulowane',
};

const statusColors = {
    pending: 'bg-gray-500',
    confirmed: 'bg-blue-500',
    picked_up: 'bg-indigo-500',
    warehouse_sorting: 'bg-purple-500',
    in_transit: 'bg-cyan-500',
    out_for_delivery: 'bg-amber-500',
    delivered: 'bg-emerald-500',
    failed_delivery: 'bg-red-500',
    returned: 'bg-orange-500',
    cancelled: 'bg-gray-600',
};

const statusLabel = computed(() => statusLabels[props.status] || props.status);
const statusColor = computed(() => statusColors[props.status] || 'bg-gray-500');
</script>

// update 60 

// update 266 

// update 392 

// u107

// u180

// u187

// u209

// u217

// u223

// u302

// u330

// ymtysf9r