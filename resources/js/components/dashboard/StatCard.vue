<template>
    <div class="stat-card glass-card-hover">
        <div class="flex items-center justify-between">
            <span class="stat-label">{{ label }}</span>
            <div :class="[
                'p-2 rounded-lg',
                colorClasses.bg
            ]">
                <component :is="icon" :class="['w-5 h-5', colorClasses.text]" />
            </div>
        </div>
        <div class="stat-value" v-if="!loading">
            {{ formattedValue }}
        </div>
        <div v-else class="h-9 bg-secondary-700 rounded animate-pulse mt-2"></div>
        <div :class="change.startsWith('-') ? 'stat-change-negative' : 'stat-change-positive'">
            {{ change }} od poprzedniego okresu
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: String,
    value: [Number, String],
    change: String,
    icon: Object,
    color: { type: String, default: 'primary' },
    loading: Boolean,
});

const colorClasses = computed(() => ({
    bg: `bg-${props.color}-500/20`,
    text: `text-${props.color}-400`,
}));

const formattedValue = computed(() => {
    if (typeof props.value === 'number') {
        return new Intl.NumberFormat('pl-PL').format(props.value);
    }
    return props.value;
});
</script>

// update 122 

// update 164 

// update 268 

// update 313 

// update 415 
