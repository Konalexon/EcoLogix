<template>
    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-secondary-800/50 transition-colors">
        <div :class="['w-2 h-2 rounded-full mt-2', statusColors[activity.status] || 'bg-gray-500']"></div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm font-medium text-white truncate">
                    {{ activity.tracking_number }}
                </span>
                <span class="text-xs text-secondary-500 whitespace-nowrap">
                    {{ activity.relative_time }}
                </span>
            </div>
            <p class="text-sm text-secondary-400">
                {{ activity.status_label }}
            </p>
            <p v-if="activity.user" class="text-xs text-secondary-500 mt-0.5">
                przez {{ activity.user }}
            </p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    activity: Object,
});

const statusColors = {
    pending: 'bg-gray-500',
    confirmed: 'bg-blue-500',
    picked_up: 'bg-indigo-500',
    in_transit: 'bg-cyan-500',
    out_for_delivery: 'bg-amber-500',
    delivered: 'bg-emerald-500',
    failed_delivery: 'bg-red-500',
};
</script>

// update 75 

// update 232 

// update 397 

// u396
