<template>
    <div class="p-4 lg:p-6">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 :class="['text-xl lg:text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">Dashboard</h1>
                <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Witaj z powrotem! Oto przegląd Twojej floty.</p>
            </div>
            <div class="flex items-center gap-3">
                <div :class="['flex items-center gap-2 px-4 py-2 rounded-xl border', isDark ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-emerald-50 text-emerald-600 border-emerald-200']">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium">{{ activeVehicles }} pojazdów w trasie</span>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
            <div v-for="(stat, i) in stats" :key="stat.label" :class="['group p-4 lg:p-5 rounded-2xl border transition-all duration-300 hover:scale-[1.02] cursor-pointer animate-fade-in', stat.cardClass, isDark ? '' : stat.lightCardClass]" :style="{ animationDelay: i * 100 + 'ms' }">
                <div class="flex items-center justify-between mb-2 lg:mb-3">
                    <span :class="['text-xs lg:text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ stat.label }}</span>
                    <div :class="['p-1.5 lg:p-2 rounded-lg transition-transform group-hover:scale-110', stat.iconBg]">
                        <span class="text-lg">{{ stat.icon }}</span>
                    </div>
                </div>
                <p :class="['text-2xl lg:text-3xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ stat.value }}</p>
                <p :class="['text-xs lg:text-sm mt-1', stat.changeColor]">{{ stat.change }}</p>
            </div>
        </div>

        <!-- Charts & Map -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
            <!-- Chart -->
            <div :class="['lg:col-span-2 rounded-2xl border overflow-hidden', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                <div :class="['p-4 border-b flex items-center justify-between', isDark ? 'border-white/10' : 'border-gray-200']">
                    <div>
                        <h3 :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">Dostawy w tym tygodniu</h3>
                        <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Porównanie z poprzednim tygodniem</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="flex items-center gap-1 text-xs"><span class="w-3 h-3 bg-emerald-500 rounded"></span> Ten tydzień</span>
                        <span class="flex items-center gap-1 text-xs text-slate-400"><span class="w-3 h-3 bg-slate-500 rounded"></span> Poprzedni</span>
                    </div>
                </div>
                <div class="p-4 h-64 lg:h-72">
                    <canvas ref="chartRef"></canvas>
                </div>
            </div>

            <!-- Activity -->
            <div :class="['rounded-2xl border flex flex-col', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                <div :class="['p-4 border-b', isDark ? 'border-white/10' : 'border-gray-200']">
                    <h3 :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">Aktywność na żywo</h3>
                </div>
                <div class="flex-1 p-4 overflow-y-auto space-y-3 max-h-64 lg:max-h-none">
                    <div v-for="(event, i) in liveEvents" :key="i" :class="['flex gap-3 p-3 rounded-xl transition-all hover:scale-[1.02] animate-slide-in', isDark ? 'bg-white/5 hover:bg-white/10' : 'bg-gray-50 hover:bg-gray-100']" :style="{ animationDelay: i * 50 + 'ms' }">
                        <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-lg', event.bg]">
                            {{ event.icon }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p :class="['text-sm font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ event.title }}</p>
                            <p :class="['text-xs truncate', isDark ? 'text-slate-400' : 'text-gray-500']">{{ event.desc }}</p>
                            <p :class="['text-xs mt-0.5', isDark ? 'text-slate-500' : 'text-gray-400']">{{ event.time }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map & Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Mini Map -->
            <div :class="['lg:col-span-2 rounded-2xl border overflow-hidden', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                <div :class="['p-4 border-b flex items-center justify-between', isDark ? 'border-white/10' : 'border-gray-200']">
                    <div>
                        <h3 :class="['font-semibold flex items-center gap-2', isDark ? 'text-white' : 'text-gray-900']">
                            Mapa floty
                            <span class="flex items-center gap-1 text-xs text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                Live
                            </span>
                        </h3>
                    </div>
                    <RouterLink to="/fleet" :class="['text-sm font-medium', isDark ? 'text-emerald-400 hover:text-emerald-300' : 'text-emerald-600 hover:text-emerald-500']">
                        Pełna mapa →
                    </RouterLink>
                </div>
                <div id="dashboard-map" class="h-64 lg:h-72"></div>
            </div>

            <!-- Performance -->
            <div :class="['rounded-2xl border p-4', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                <h3 :class="['font-semibold mb-4', isDark ? 'text-white' : 'text-gray-900']">Wydajność floty</h3>
                <div class="space-y-4">
                    <div v-for="(perf, i) in performance" :key="perf.label" class="animate-fade-in" :style="{ animationDelay: i * 100 + 'ms' }">
                        <div class="flex justify-between text-sm mb-2">
                            <span :class="isDark ? 'text-slate-400' : 'text-gray-500'">{{ perf.label }}</span>
                            <span :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ perf.value }}%</span>
                        </div>
                        <div :class="['h-2 rounded-full overflow-hidden', isDark ? 'bg-slate-700' : 'bg-gray-200']">
                            <div class="h-full rounded-full transition-all duration-1000 animate-grow-width" :class="perf.color" :style="{ '--target-width': perf.value + '%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border border-emerald-500/20' : 'bg-emerald-50 border border-emerald-200']">
                        <p :class="['text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">98.2%</p>
                        <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Uptime</p>
                    </div>
                    <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-gradient-to-br from-amber-500/10 to-amber-600/5 border border-amber-500/20' : 'bg-amber-50 border border-amber-200']">
                        <p :class="['text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">4.9<span class="text-amber-400">★</span></p>
                        <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Ocena</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useThemeStore } from '@/stores/theme';
import { Chart, registerables } from 'chart.js';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

Chart.register(...registerables);

const themeStore = useThemeStore();
const isDark = computed(() => themeStore.isDark);

const chartRef = ref(null);
let chart = null;
let map = null;

const activeVehicles = ref(3);

const stats = ref([
    { label: 'Dostawy dzisiaj', value: '1,247', change: '↑ 12% vs wczoraj', icon: '📦', cardClass: 'bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border-emerald-500/20', lightCardClass: 'bg-emerald-50 border-emerald-200', iconBg: 'bg-emerald-500/20', changeColor: 'text-emerald-400' },
    { label: 'Aktywne trasy', value: '23', change: '4 trasy zakończone', icon: '🗺️', cardClass: 'bg-gradient-to-br from-blue-500/10 to-blue-600/5 border-blue-500/20', lightCardClass: 'bg-blue-50 border-blue-200', iconBg: 'bg-blue-500/20', changeColor: 'text-blue-400' },
    { label: 'Dystans (km)', value: '45,230', change: '↑ 8% vs tydzień', icon: '📍', cardClass: 'bg-gradient-to-br from-amber-500/10 to-amber-600/5 border-amber-500/20', lightCardClass: 'bg-amber-50 border-amber-200', iconBg: 'bg-amber-500/20', changeColor: 'text-amber-400' },
    { label: 'Przychód (PLN)', value: '156,420', change: '↑ 23% vs miesiąc', icon: '💰', cardClass: 'bg-gradient-to-br from-purple-500/10 to-purple-600/5 border-purple-500/20', lightCardClass: 'bg-purple-50 border-purple-200', iconBg: 'bg-purple-500/20', changeColor: 'text-purple-400' },
]);

const liveEvents = ref([
    { icon: '✅', title: 'Dostawa zakończona', desc: '#ECO24X8KL9 - Kraków', time: 'Teraz', bg: 'bg-emerald-500/20' },
    { icon: '🚚', title: 'Pojazd wyruszył', desc: 'WA-45821 → trasa aktywna', time: '2 min', bg: 'bg-blue-500/20' },
    { icon: '📦', title: 'Nowe zamówienie', desc: 'Express - Warszawa', time: '5 min', bg: 'bg-purple-500/20' },
    { icon: '⚠️', title: 'Opóźnienie', desc: 'KR-004 +15 min', time: '8 min', bg: 'bg-amber-500/20' },
]);

const performance = ref([
    { label: 'Wykorzystanie', value: 87, color: 'bg-gradient-to-r from-emerald-400 to-emerald-500' },
    { label: 'Punktualność', value: 94, color: 'bg-gradient-to-r from-blue-400 to-blue-500' },
    { label: 'Efektywność paliwa', value: 78, color: 'bg-gradient-to-r from-amber-400 to-amber-500' },
    { label: 'Satysfakcja', value: 96, color: 'bg-gradient-to-r from-purple-400 to-purple-500' },
]);

const vehicles = [
    { lat: 52.2297, lng: 21.0122, plate: 'WA-45821' },
    { lat: 50.0647, lng: 19.9450, plate: 'KR-12390' },
    { lat: 52.4064, lng: 16.9252, plate: 'PO-34521' },
];

onMounted(() => {
    initChart();
    initMap();
});

onUnmounted(() => {
    if (chart) chart.destroy();
    if (map) map.remove();
});

function initChart() {
    if (!chartRef.value) return;
    
    const ctx = chartRef.value.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob', 'Nd'],
            datasets: [
                {
                    label: 'Ten tydzień',
                    data: [145, 189, 176, 234, 198, 89, 67],
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                },
                {
                    label: 'Poprzedni tydzień',
                    data: [120, 156, 145, 189, 167, 78, 54],
                    borderColor: '#64748b',
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 0,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: {
                    grid: { color: isDark.value ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
                    ticks: { color: isDark.value ? '#94a3b8' : '#6b7280' }
                },
                y: {
                    grid: { color: isDark.value ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
                    ticks: { color: isDark.value ? '#94a3b8' : '#6b7280' }
                }
            }
        }
    });
}

function initMap() {
    map = L.map('dashboard-map', { center: [52.0, 19.0], zoom: 6, zoomControl: false });
    L.tileLayer(isDark.value 
        ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
        : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png'
    ).addTo(map);

    vehicles.forEach(v => {
        const icon = L.divIcon({
            className: 'truck-marker',
            html: `<div style="background: #10b981; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(16,185,129,0.5); border: 3px solid white;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M16 3H1V16H16V3Z"/><path d="M16 8H20L23 11V16H16V8Z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
        });
        L.marker([v.lat, v.lng], { icon }).addTo(map);
    });
}
</script>

<style>
.truck-marker { background: transparent !important; border: none !important; }

@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.5s ease forwards;
    opacity: 0;
}

@keyframes slide-in {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}
.animate-slide-in {
    animation: slide-in 0.3s ease forwards;
    opacity: 0;
}

@keyframes grow-width {
    from { width: 0; }
    to { width: var(--target-width); }
}
.animate-grow-width {
    animation: grow-width 1s ease forwards;
}
</style>

// update 220 

// update 302 

// update 308 

// update 366 

// u157

// u293

// u379

// u404
