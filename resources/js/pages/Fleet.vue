<template>
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Mapa Floty</h1>
                <p class="text-slate-400">Śledzenie wszystkich pojazdów w czasie rzeczywistym</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex bg-white/5 rounded-xl p-1">
                    <button :class="['px-4 py-2 rounded-lg text-sm font-medium transition-all', filter === 'all' ? 'bg-emerald-500 text-white' : 'text-slate-400 hover:text-white']" @click="filter = 'all'">Wszystkie ({{ vehicles.length }})</button>
                    <button :class="['px-4 py-2 rounded-lg text-sm font-medium transition-all', filter === 'active' ? 'bg-emerald-500 text-white' : 'text-slate-400 hover:text-white']" @click="filter = 'active'">W trasie ({{ activeCount }})</button>
                    <button :class="['px-4 py-2 rounded-lg text-sm font-medium transition-all', filter === 'idle' ? 'bg-emerald-500 text-white' : 'text-slate-400 hover:text-white']" @click="filter = 'idle'">Dostępne ({{ idleCount }})</button>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="grid grid-cols-4 gap-6">
            <div class="col-span-3 bg-slate-800/50 backdrop-blur rounded-2xl border border-white/5 overflow-hidden">
                <div id="fleet-full-map" class="h-[calc(100vh-180px)]"></div>
            </div>

            <!-- Vehicle List -->
            <div class="bg-slate-800/50 backdrop-blur rounded-2xl border border-white/5 flex flex-col max-h-[calc(100vh-180px)]">
                <div class="p-4 border-b border-white/5">
                    <input v-model="search" type="text" placeholder="Szukaj pojazdu..." class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500/50" />
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <div v-for="v in filteredVehicles" :key="v.id" @click="focusVehicle(v)" class="p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-transparent hover:border-emerald-500/30 transition-all cursor-pointer group">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="v.status === 'active' ? 'bg-emerald-500/20' : 'bg-slate-500/20'">
                                <svg class="w-5 h-5" :class="v.status === 'active' ? 'text-emerald-400' : 'text-slate-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-white group-hover:text-emerald-400 transition-colors">{{ v.plate }}</p>
                                <p class="text-xs text-slate-400">{{ v.type }}</p>
                            </div>
                            <div class="w-2 h-2 rounded-full" :class="v.status === 'active' ? 'bg-emerald-400 animate-pulse' : 'bg-slate-500'"></div>
                        </div>
                        <div class="text-xs text-slate-400 space-y-1">
                            <p>👤 {{ v.driver }}</p>
                            <p>📍 {{ v.location }}</p>
                            <p v-if="v.status === 'active'" class="text-emerald-400">🚚 {{ v.speed }} km/h • {{ v.eta }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const filter = ref('all');
const search = ref('');

const vehicles = ref([
    { id: 1, plate: 'WA-45821', type: 'Ciężarówka TIR', driver: 'Tomasz Nowak', location: 'Autostrada A2, km 234', speed: 89, eta: 'ETA 14:30', status: 'active', lat: 52.2297, lng: 21.0122 },
    { id: 2, plate: 'KR-12390', type: 'Ciężarówka TIR', driver: 'Anna Wiśniewska', location: 'Kraków, ul. Armii Krajowej', speed: 92, eta: 'ETA 15:45', status: 'active', lat: 50.0647, lng: 19.9450 },
    { id: 3, plate: 'GD-78234', type: 'Van dostawczy', driver: 'Piotr Kowalczyk', location: 'Baza Gdańsk', speed: 0, eta: '', status: 'idle', lat: 54.3520, lng: 18.6466 },
    { id: 4, plate: 'PO-34521', type: 'Ciężarówka 20t', driver: 'Marek Zieliński', location: 'Droga S5, Poznań', speed: 78, eta: 'ETA 13:15', status: 'active', lat: 52.4064, lng: 16.9252 },
    { id: 5, plate: 'WR-99123', type: 'Van dostawczy', driver: 'Karol Mazur', location: 'Wrocław, Centrum', speed: 45, eta: 'ETA 12:00', status: 'active', lat: 51.1079, lng: 17.0385 },
    { id: 6, plate: 'LU-55432', type: 'Ciężarówka 12t', driver: 'Jan Wolski', location: 'Baza Lublin', speed: 0, eta: '', status: 'idle', lat: 51.2465, lng: 22.5684 },
]);

const activeCount = computed(() => vehicles.value.filter(v => v.status === 'active').length);
const idleCount = computed(() => vehicles.value.filter(v => v.status === 'idle').length);

const filteredVehicles = computed(() => {
    let result = vehicles.value;
    if (filter.value !== 'all') result = result.filter(v => v.status === filter.value);
    if (search.value) result = result.filter(v => v.plate.toLowerCase().includes(search.value.toLowerCase()) || v.driver.toLowerCase().includes(search.value.toLowerCase()));
    return result;
});

let map = null;
let markers = {};

onMounted(() => {
    map = L.map('fleet-full-map', { center: [52.0, 19.0], zoom: 6, zoomControl: true });
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);

    vehicles.value.forEach(v => addMarker(v));
});

onUnmounted(() => { if (map) map.remove(); });

function addMarker(v) {
    const color = v.status === 'active' ? '#10b981' : '#64748b';
    const icon = L.divIcon({
        className: 'fleet-marker',
        html: `<div style="position:relative;">
            <div style="background: ${color}; width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px ${color}80; border: 3px solid white;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M16 3H1V16H16V3Z"/><path d="M16 8H20L23 11V16H16V8Z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div style="position:absolute;top:52px;left:50%;transform:translateX(-50%);background:#1e293b;padding:2px 8px;border-radius:6px;white-space:nowrap;font-size:11px;color:white;font-weight:600;">${v.plate}</div>
        </div>`,
        iconSize: [48, 70],
        iconAnchor: [24, 35],
    });
    markers[v.id] = L.marker([v.lat, v.lng], { icon }).addTo(map)
        .bindPopup(`<div style="min-width:180px;"><b style="font-size:14px;">${v.plate}</b><br><span style="color:#94a3b8;">${v.type}</span><br>👤 ${v.driver}<br>📍 ${v.location}<br>${v.status === 'active' ? `<span style="color:#10b981;">🚚 ${v.speed} km/h</span>` : '<span style="color:#64748b;">Zaparkowany</span>'}</div>`);
}

function focusVehicle(v) {
    map.flyTo([v.lat, v.lng], 12);
    markers[v.id].openPopup();
}
</script>

<style>
.fleet-marker { background: transparent !important; border: none !important; }
.leaflet-popup-content-wrapper { background: #1e293b; color: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
.leaflet-popup-tip { background: #1e293b; }
</style>

// update 92 

// update 95 

// update 219 

// update 295 

// update 303 

// u82

// u125

// u242

// u372
