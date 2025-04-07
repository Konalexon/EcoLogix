<template>
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Analityka</h1>
                <p class="text-slate-400">Szczegółowe raporty i statystyki</p>
            </div>
            <div class="flex gap-3">
                <select class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white">
                    <option>Ostatnie 7 dni</option>
                    <option>Ostatnie 30 dni</option>
                    <option>Ten rok</option>
                </select>
                <button class="px-4 py-2 bg-emerald-500/20 text-emerald-400 rounded-xl hover:bg-emerald-500/30 transition-colors">
                    Eksportuj PDF
                </button>
            </div>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-6 gap-4 mb-6">
            <div v-for="kpi in kpis" :key="kpi.label" class="p-4 rounded-xl bg-white/5 border border-white/10 text-center">
                <p class="text-2xl font-bold text-white">{{ kpi.value }}</p>
                <p class="text-xs text-slate-400">{{ kpi.label }}</p>
                <p class="text-xs mt-1" :class="kpi.change.startsWith('+') ? 'text-emerald-400' : 'text-red-400'">{{ kpi.change }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <!-- Chart placeholder 1 -->
            <div class="bg-slate-800/50 backdrop-blur rounded-2xl border border-white/5 p-6">
                <h3 class="font-semibold text-white mb-4">Dostawy w czasie</h3>
                <div class="h-64 flex items-end justify-between gap-2">
                    <div v-for="(h, i) in chartData" :key="i" class="flex-1 bg-gradient-to-t from-emerald-500 to-emerald-400 rounded-t-lg transition-all hover:from-emerald-400 hover:to-emerald-300" :style="{ height: h + '%' }"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-slate-500">
                    <span>Pon</span><span>Wt</span><span>Śr</span><span>Czw</span><span>Pt</span><span>Sob</span><span>Nd</span>
                </div>
            </div>

            <!-- Chart placeholder 2 -->
            <div class="bg-slate-800/50 backdrop-blur rounded-2xl border border-white/5 p-6">
                <h3 class="font-semibold text-white mb-4">Wydajność kierowców</h3>
                <div class="space-y-4">
                    <div v-for="driver in topDrivers" :key="driver.name">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-white">{{ driver.name }}</span>
                            <span class="text-slate-400">{{ driver.deliveries }} dostaw</span>
                        </div>
                        <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full" :class="driver.color" :style="{ width: driver.percent + '%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <!-- Top routes -->
            <div class="bg-slate-800/50 backdrop-blur rounded-2xl border border-white/5 p-6">
                <h3 class="font-semibold text-white mb-4">Najpopularniejsze trasy</h3>
                <div class="space-y-3">
                    <div v-for="(route, i) in topRoutes" :key="i" class="flex items-center gap-3 p-3 rounded-xl bg-white/5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold text-sm">{{ i + 1 }}</div>
                        <div class="flex-1">
                            <p class="text-sm text-white">{{ route.from }} → {{ route.to }}</p>
                            <p class="text-xs text-slate-400">{{ route.count }} przesyłek</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fuel consumption -->
            <div class="bg-slate-800/50 backdrop-blur rounded-2xl border border-white/5 p-6">
                <h3 class="font-semibold text-white mb-4">Zużycie paliwa</h3>
                <div class="flex items-center justify-center h-48">
                    <div class="relative w-40 h-40">
                        <svg class="w-full h-full -rotate-90">
                            <circle cx="80" cy="80" r="70" stroke="#334155" stroke-width="12" fill="none" />
                            <circle cx="80" cy="80" r="70" stroke="url(#fuelGradient)" stroke-width="12" fill="none" stroke-linecap="round" :stroke-dasharray="440" :stroke-dashoffset="440 - (440 * 0.72)" />
                            <defs>
                                <linearGradient id="fuelGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#10b981" />
                                    <stop offset="100%" stop-color="#3b82f6" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <p class="text-3xl font-bold text-white">72%</p>
                            <p class="text-xs text-slate-400">Efektywność</p>
                        </div>
                    </div>
                </div>
                <div class="text-center text-sm text-slate-400">Średnio 28L/100km</div>
            </div>

            <!-- Revenue -->
            <div class="bg-slate-800/50 backdrop-blur rounded-2xl border border-white/5 p-6">
                <h3 class="font-semibold text-white mb-4">Przychody</h3>
                <div class="text-center mb-6">
                    <p class="text-4xl font-bold text-white">456,780 <span class="text-lg text-slate-400">PLN</span></p>
                    <p class="text-emerald-400 text-sm">↑ 23% vs poprzedni miesiąc</p>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Express</span>
                        <span class="text-white">234,500 PLN</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Standard</span>
                        <span class="text-white">178,280 PLN</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Economy</span>
                        <span class="text-white">44,000 PLN</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const kpis = ref([
    { label: 'Dostawy', value: '3,247', change: '+12%' },
    { label: 'Przychód', value: '456K', change: '+23%' },
    { label: 'Śr. czas dostawy', value: '4.2h', change: '-8%' },
    { label: 'Punktualność', value: '94%', change: '+2%' },
    { label: 'Paliwo (L)', value: '12,340', change: '-5%' },
    { label: 'Klienci', value: '892', change: '+15%' },
]);

const chartData = ref([65, 80, 72, 90, 85, 45, 30]);

const topDrivers = ref([
    { name: 'Tomasz Nowak', deliveries: 156, percent: 100, color: 'bg-gradient-to-r from-emerald-400 to-emerald-500' },
    { name: 'Anna Wiśniewska', deliveries: 142, percent: 91, color: 'bg-gradient-to-r from-blue-400 to-blue-500' },
    { name: 'Piotr Kowalczyk', deliveries: 128, percent: 82, color: 'bg-gradient-to-r from-purple-400 to-purple-500' },
    { name: 'Marek Zieliński', deliveries: 115, percent: 74, color: 'bg-gradient-to-r from-amber-400 to-amber-500' },
]);

const topRoutes = ref([
    { from: 'Warszawa', to: 'Kraków', count: 234 },
    { from: 'Gdańsk', to: 'Poznań', count: 189 },
    { from: 'Wrocław', to: 'Łódź', count: 156 },
    { from: 'Katowice', to: 'Warszawa', count: 142 },
]);
</script>

// update 131 

// update 223 

// update 284 

// update 301 

// u154

// u213

// u215
