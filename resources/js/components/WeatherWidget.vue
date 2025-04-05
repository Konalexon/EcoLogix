<template>
    <div :class="['rounded-2xl border p-4', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
        <div class="flex items-center justify-between mb-3">
            <h3 :class="['font-semibold text-sm', isDark ? 'text-white' : 'text-gray-900']">Pogoda na trasach</h3>
            <button @click="refresh" :class="['p-1.5 rounded-lg transition-colors', isDark ? 'hover:bg-white/10 text-slate-400' : 'hover:bg-gray-100 text-gray-500', loading ? 'animate-spin' : '']">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </button>
        </div>

        <div class="space-y-2">
            <div v-for="city in weatherData" :key="city.name" :class="['flex items-center gap-3 p-2 rounded-xl transition-colors', isDark ? 'hover:bg-white/5' : 'hover:bg-gray-50']">
                <span class="text-2xl">{{ city.icon }}</span>
                <div class="flex-1 min-w-0">
                    <p :class="['font-medium text-sm truncate', isDark ? 'text-white' : 'text-gray-900']">{{ city.name }}</p>
                    <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">{{ city.condition }}</p>
                </div>
                <div class="text-right">
                    <p :class="['font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ city.temp }}°</p>
                    <p v-if="city.alert" class="text-xs text-amber-400">{{ city.alert }}</p>
                </div>
            </div>
        </div>

        <!-- Road conditions -->
        <div class="mt-4 pt-3 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
            <p :class="['text-xs font-medium uppercase tracking-wider mb-2', isDark ? 'text-slate-500' : 'text-gray-500']">Warunki drogowe</p>
            <div class="flex gap-2">
                <span v-for="condition in roadConditions" :key="condition.route" :class="['px-2 py-1 rounded-lg text-xs font-medium', condition.status === 'good' ? 'bg-emerald-500/20 text-emerald-400' : condition.status === 'moderate' ? 'bg-amber-500/20 text-amber-400' : 'bg-red-500/20 text-red-400']">
                    {{ condition.route }}: {{ condition.label }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useThemeStore } from '@/stores/theme';

const themeStore = useThemeStore();
const isDark = computed(() => themeStore.isDark);

const loading = ref(false);

const weatherData = ref([
    { name: 'Warszawa', temp: 2, icon: '⛅', condition: 'Częściowo pochmurno', alert: null },
    { name: 'Kraków', temp: -1, icon: '🌨️', condition: 'Lekki śnieg', alert: 'Ślisko!' },
    { name: 'Gdańsk', temp: 4, icon: '🌧️', condition: 'Deszczowo', alert: null },
    { name: 'Poznań', temp: 3, icon: '☁️', condition: 'Pochmurno', alert: null },
    { name: 'Wrocław', temp: 1, icon: '🌫️', condition: 'Mgła', alert: 'Słaba widoczność' },
]);

const roadConditions = ref([
    { route: 'A2', status: 'good', label: 'OK' },
    { route: 'A4', status: 'moderate', label: 'Korki' },
    { route: 'A1', status: 'good', label: 'OK' },
]);

function refresh() {
    loading.value = true;
    setTimeout(() => {
        // Simulate weather update
        weatherData.value.forEach(city => {
            city.temp += Math.floor(Math.random() * 3) - 1;
        });
        loading.value = false;
    }, 1000);
}
</script>

// update 110 

// update 129 

// update 136 

// update 143 

// update 210 

// update 332 

// update 359 

// u84

// u226

// u230

// u276
