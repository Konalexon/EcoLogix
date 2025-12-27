<template>
    <div class="p-4 lg:p-6">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 :class="['text-xl lg:text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">Pojazdy</h1>
                <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Zarządzaj flotą {{ vehicles.length }} pojazdów</p>
            </div>
            <button @click="showAddModal = true" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:scale-105 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Dodaj pojazd
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
            <div :class="['p-4 lg:p-5 rounded-2xl border', isDark ? 'bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border-emerald-500/20' : 'bg-emerald-50 border-emerald-200']">
                <p :class="['text-sm mb-1', isDark ? 'text-slate-400' : 'text-gray-500']">W trasie</p>
                <p :class="['text-2xl lg:text-3xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ vehicles.filter(v => v.status === 'active').length }}</p>
            </div>
            <div :class="['p-4 lg:p-5 rounded-2xl border', isDark ? 'bg-gradient-to-br from-blue-500/10 to-blue-600/5 border-blue-500/20' : 'bg-blue-50 border-blue-200']">
                <p :class="['text-sm mb-1', isDark ? 'text-slate-400' : 'text-gray-500']">Dostępne</p>
                <p :class="['text-2xl lg:text-3xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ vehicles.filter(v => v.status === 'idle').length }}</p>
            </div>
            <div :class="['p-4 lg:p-5 rounded-2xl border', isDark ? 'bg-gradient-to-br from-amber-500/10 to-amber-600/5 border-amber-500/20' : 'bg-amber-50 border-amber-200']">
                <p :class="['text-sm mb-1', isDark ? 'text-slate-400' : 'text-gray-500']">Serwis</p>
                <p :class="['text-2xl lg:text-3xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ vehicles.filter(v => v.status === 'service').length }}</p>
            </div>
            <div :class="['p-4 lg:p-5 rounded-2xl border', isDark ? 'bg-gradient-to-br from-purple-500/10 to-purple-600/5 border-purple-500/20' : 'bg-purple-50 border-purple-200']">
                <p :class="['text-sm mb-1', isDark ? 'text-slate-400' : 'text-gray-500']">Łączny dystans</p>
                <p :class="['text-2xl lg:text-3xl font-bold', isDark ? 'text-white' : 'text-gray-900']">1.2M km</p>
            </div>
        </div>

        <!-- Vehicles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
            <div v-for="v in vehicles" :key="v.id" :class="['rounded-2xl border overflow-hidden transition-all hover:scale-[1.02] group', isDark ? 'bg-slate-800/50 border-white/5 hover:border-emerald-500/30' : 'bg-white border-gray-200 hover:border-emerald-300']">
                <div class="relative h-40 overflow-hidden">
                    <img :src="v.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute top-3 left-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold backdrop-blur" :class="statusClass(v.status)">{{ v.statusLabel }}</span>
                    </div>
                    <div class="absolute top-3 right-3">
                        <span class="px-2 py-1 rounded-lg text-xs font-mono bg-black/50 text-white backdrop-blur">{{ v.year }}</span>
                    </div>
                    <div class="absolute bottom-3 left-3 right-3">
                        <p class="text-xl font-bold text-white">{{ v.plate }}</p>
                        <p class="text-sm text-white/80">{{ v.type }} • {{ v.brand }}</p>
                    </div>
                </div>
                <div class="p-4 lg:p-5">
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div :class="['p-2 lg:p-3 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                            <p :class="['text-sm lg:text-lg font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ v.mileage }}</p>
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">km</p>
                        </div>
                        <div :class="['p-2 lg:p-3 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                            <p :class="['text-sm lg:text-lg font-bold', v.fuel > 30 ? 'text-emerald-400' : 'text-red-400']">{{ v.fuel }}%</p>
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Paliwo</p>
                        </div>
                        <div :class="['p-2 lg:p-3 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                            <p :class="['text-sm lg:text-lg font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ v.capacity }}</p>
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">ton</p>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 text-sm mb-4">
                        <div class="flex items-center justify-between">
                            <span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Kierowca</span>
                            <span :class="isDark ? 'text-white' : 'text-gray-900'">{{ v.driver || '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Następny serwis</span>
                            <span :class="isDark ? 'text-white' : 'text-gray-900'">{{ v.nextService }}</span>
                        </div>
                        <div v-if="v.status === 'active'" class="flex items-center justify-between">
                            <span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Prędkość</span>
                            <span class="text-emerald-400 font-medium">{{ v.speed }} km/h</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button @click="openDetails(v)" :class="['flex-1 py-2.5 rounded-xl text-sm font-medium transition-all', isDark ? 'bg-white/5 text-white hover:bg-white/10' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">
                            Szczegóły
                        </button>
                        <button v-if="v.status === 'active'" @click="openLocation(v)" class="flex-1 py-2.5 bg-emerald-500/20 text-emerald-400 rounded-xl text-sm font-medium hover:bg-emerald-500/30 transition-all flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Lokalizacja
                        </button>
                        <button v-else :class="['flex-1 py-2.5 rounded-xl text-sm font-medium cursor-not-allowed', isDark ? 'bg-white/5 text-slate-500' : 'bg-gray-100 text-gray-400']" disabled>
                            Niedostępne
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Details Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showDetailsModal = false"></div>
                    <div :class="['relative w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl', isDark ? 'bg-slate-800' : 'bg-white']">
                        <!-- Header Image -->
                        <div class="relative h-48">
                            <img :src="selectedVehicle?.image" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                            <button @click="showDetailsModal = false" class="absolute top-4 right-4 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <div class="absolute bottom-4 left-6">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold mb-2 inline-block" :class="statusClass(selectedVehicle?.status)">{{ selectedVehicle?.statusLabel }}</span>
                                <h2 class="text-3xl font-bold text-white">{{ selectedVehicle?.plate }}</h2>
                                <p class="text-white/80">{{ selectedVehicle?.brand }} {{ selectedVehicle?.type }}</p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <!-- Stats Grid -->
                            <div class="grid grid-cols-4 gap-4 mb-6">
                                <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <p class="text-2xl font-bold text-emerald-400">{{ selectedVehicle?.mileage }}</p>
                                    <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Przebieg (km)</p>
                                </div>
                                <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <p class="text-2xl font-bold" :class="selectedVehicle?.fuel > 30 ? 'text-emerald-400' : 'text-red-400'">{{ selectedVehicle?.fuel }}%</p>
                                    <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Paliwo</p>
                                </div>
                                <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <p :class="['text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ selectedVehicle?.capacity }}</p>
                                    <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Ładowność (t)</p>
                                </div>
                                <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <p :class="['text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ selectedVehicle?.year }}</p>
                                    <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Rok prod.</p>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h3 :class="['font-semibold mb-3', isDark ? 'text-white' : 'text-gray-900']">Informacje</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">VIN</span><span :class="isDark ? 'text-white' : 'text-gray-900'" class="font-mono">{{ selectedVehicle?.vin }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Silnik</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedVehicle?.engine }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Moc</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedVehicle?.power }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Paliwo</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedVehicle?.fuelType }}</span></div>
                                    </div>
                                </div>
                                <div>
                                    <h3 :class="['font-semibold mb-3', isDark ? 'text-white' : 'text-gray-900']">Serwis</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Ostatni przegląd</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedVehicle?.lastService }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Następny</span><span class="text-emerald-400">{{ selectedVehicle?.nextService }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Ubezpieczenie</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedVehicle?.insurance }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Przegląd tech.</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedVehicle?.inspection }}</span></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Driver -->
                            <div v-if="selectedVehicle?.driver" :class="['mt-6 p-4 rounded-xl flex items-center gap-4', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                <img :src="selectedVehicle?.driverAvatar" class="w-12 h-12 rounded-full object-cover" />
                                <div class="flex-1">
                                    <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ selectedVehicle?.driver }}</p>
                                    <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Przypisany kierowca</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-400">Aktywny</span>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Location Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showLocationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showLocationModal = false"></div>
                    <div :class="['relative w-full max-w-4xl rounded-3xl overflow-hidden shadow-2xl', isDark ? 'bg-slate-800' : 'bg-white']">
                        <div :class="['p-4 border-b flex items-center justify-between', isDark ? 'border-white/10' : 'border-gray-200']">
                            <div>
                                <h2 :class="['text-lg font-semibold', isDark ? 'text-white' : 'text-gray-900']">Lokalizacja: {{ selectedVehicle?.plate }}</h2>
                                <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ selectedVehicle?.driver }} • {{ selectedVehicle?.speed }} km/h</p>
                            </div>
                            <button @click="showLocationModal = false" :class="['p-2 rounded-lg transition-colors', isDark ? 'hover:bg-white/10 text-slate-400' : 'hover:bg-gray-100 text-gray-500']">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div id="location-map" class="h-96"></div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Add Vehicle Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>
                    <div :class="['relative w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl', isDark ? 'bg-slate-800' : 'bg-white']">
                        <div :class="['p-6 border-b', isDark ? 'border-white/10' : 'border-gray-200']">
                            <h2 :class="['text-xl font-semibold', isDark ? 'text-white' : 'text-gray-900']">Dodaj nowy pojazd</h2>
                        </div>
                        <form @submit.prevent="addVehicle" class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Tablica rejestracyjna</label>
                                    <input v-model="newVehicle.plate" required :class="inputClass" placeholder="WA-12345" />
                                </div>
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Marka</label>
                                    <input v-model="newVehicle.brand" required :class="inputClass" placeholder="Scania" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Model</label>
                                    <input v-model="newVehicle.type" required :class="inputClass" placeholder="R450" />
                                </div>
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Rok produkcji</label>
                                    <input v-model="newVehicle.year" type="number" required :class="inputClass" placeholder="2023" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Ładowność (tony)</label>
                                    <input v-model="newVehicle.capacity" type="number" required :class="inputClass" placeholder="24" />
                                </div>
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">VIN</label>
                                    <input v-model="newVehicle.vin" :class="inputClass" placeholder="WVWZZZ3CZWE123456" />
                                </div>
                            </div>
                            <div class="flex gap-3 pt-4">
                                <button type="button" @click="showAddModal = false" :class="['flex-1 py-3 rounded-xl font-medium transition-colors', isDark ? 'bg-white/5 text-white hover:bg-white/10' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">Anuluj</button>
                                <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium hover:from-emerald-400 hover:to-emerald-500 transition-all">Dodaj pojazd</button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { useThemeStore } from '@/stores/theme';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const themeStore = useThemeStore();
const isDark = computed(() => themeStore.isDark);

const showDetailsModal = ref(false);
const showLocationModal = ref(false);
const showAddModal = ref(false);
const selectedVehicle = ref(null);
let locationMap = null;

const inputClass = computed(() => isDark.value 
    ? 'w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500/50'
    : 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-emerald-500'
);

const vehicles = ref([
    { id: 1, plate: 'WA-45821', type: 'R450', brand: 'Scania', year: 2022, status: 'active', statusLabel: 'W trasie', mileage: '245,000', fuel: 78, capacity: 24, driver: 'Tomasz Nowak', driverAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face', speed: 89, nextService: 'Za 5,000 km', lastService: '15.11.2024', vin: 'YS2R4X20009876543', engine: '12.7L 6-cyl Diesel', power: '450 KM', fuelType: 'Diesel', insurance: '15.06.2025', inspection: '20.03.2025', lat: 52.2297, lng: 21.0122, image: 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=400&h=250&fit=crop' },
    { id: 2, plate: 'KR-12390', type: 'FH16', brand: 'Volvo', year: 2021, status: 'active', statusLabel: 'W trasie', mileage: '189,500', fuel: 45, capacity: 26, driver: 'Anna Wiśniewska', driverAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face', speed: 92, nextService: 'Za 12,000 km', lastService: '01.12.2024', vin: 'YV2RT40A9RB123456', engine: '16.1L 6-cyl Diesel', power: '550 KM', fuelType: 'Diesel', insurance: '22.08.2025', inspection: '15.05.2025', lat: 50.0647, lng: 19.9450, image: 'https://images.unsplash.com/photo-1586191582066-3b5eb72cd5e4?w=400&h=250&fit=crop' },
    { id: 3, plate: 'GD-78234', type: 'Sprinter', brand: 'Mercedes', year: 2023, status: 'idle', statusLabel: 'Dostępny', mileage: '92,300', fuel: 100, capacity: 3.5, driver: null, driverAvatar: null, speed: 0, nextService: 'Za 8,000 km', lastService: '20.12.2024', vin: 'WDB9066331S654321', engine: '2.1L 4-cyl Diesel', power: '163 KM', fuelType: 'Diesel', insurance: '10.04.2025', inspection: '28.02.2025', lat: 54.3520, lng: 18.6466, image: 'https://images.unsplash.com/photo-1519003722824-194d4455a60c?w=400&h=250&fit=crop' },
    { id: 4, plate: 'PO-34521', type: 'XF', brand: 'DAF', year: 2020, status: 'active', statusLabel: 'W trasie', mileage: '312,000', fuel: 62, capacity: 24, driver: 'Marek Zieliński', driverAvatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face', speed: 78, nextService: 'Za 3,000 km', lastService: '05.12.2024', vin: 'XLRTE47MS0E987654', engine: '12.9L 6-cyl Diesel', power: '480 KM', fuelType: 'Diesel', insurance: '30.09.2025', inspection: '12.06.2025', lat: 52.4064, lng: 16.9252, image: 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=400&h=250&fit=crop' },
    { id: 5, plate: 'WR-99123', type: 'Daily', brand: 'Iveco', year: 2022, status: 'service', statusLabel: 'Serwis', mileage: '156,800', fuel: 30, capacity: 7, driver: null, driverAvatar: null, speed: 0, nextService: 'W trakcie', lastService: '28.12.2024', vin: 'ZCFC135B405123456', engine: '3.0L 4-cyl Diesel', power: '180 KM', fuelType: 'Diesel', insurance: '18.07.2025', inspection: '05.04.2025', lat: 51.1079, lng: 17.0385, image: 'https://images.unsplash.com/photo-1519003722824-194d4455a60c?w=400&h=250&fit=crop' },
]);

const newVehicle = ref({ plate: '', brand: '', type: '', year: '', capacity: '', vin: '' });

function statusClass(status) {
    return {
        'active': 'bg-emerald-500/20 text-emerald-400',
        'idle': 'bg-blue-500/20 text-blue-400',
        'service': 'bg-amber-500/20 text-amber-400',
    }[status] || 'bg-slate-500/20 text-slate-400';
}

function openDetails(v) {
    selectedVehicle.value = v;
    showDetailsModal.value = true;
}

function openLocation(v) {
    selectedVehicle.value = v;
    showLocationModal.value = true;
    nextTick(() => initLocationMap(v));
}

function initLocationMap(v) {
    if (locationMap) locationMap.remove();
    locationMap = L.map('location-map', { center: [v.lat, v.lng], zoom: 12 });
    L.tileLayer(isDark.value ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(locationMap);
    
    const icon = L.divIcon({
        className: 'truck-marker',
        html: `<div style="background: #10b981; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 30px rgba(16,185,129,0.6); border: 4px solid white; animation: pulse 2s infinite;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M16 3H1V16H16V3Z"/><path d="M16 8H20L23 11V16H16V8Z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>`,
        iconSize: [50, 50], iconAnchor: [25, 25],
    });
    L.marker([v.lat, v.lng], { icon }).addTo(locationMap).bindPopup(`<b>${v.plate}</b><br>${v.driver}<br><span style="color:#10b981">${v.speed} km/h</span>`).openPopup();
}

function addVehicle() {
    vehicles.value.push({
        id: Date.now(),
        ...newVehicle.value,
        status: 'idle',
        statusLabel: 'Dostępny',
        mileage: '0',
        fuel: 100,
        driver: null,
        speed: 0,
        nextService: 'Za 10,000 km',
        lastService: new Date().toLocaleDateString('pl-PL'),
        image: 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=400&h=250&fit=crop'
    });
    showAddModal.value = false;
    newVehicle.value = { plate: '', brand: '', type: '', year: '', capacity: '', vin: '' };
}

watch(showLocationModal, (val) => { if (!val && locationMap) { locationMap.remove(); locationMap = null; } });
</script>

<style>
.modal-enter-active, .modal-leave-active { transition: opacity 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-active .relative, .modal-leave-active .relative { transition: transform 0.3s ease; }
.modal-enter-from .relative { transform: scale(0.95); }
.truck-marker { background: transparent !important; border: none !important; }
@keyframes pulse { 0%, 100% { box-shadow: 0 0 20px rgba(16,185,129,0.4); } 50% { box-shadow: 0 0 40px rgba(16,185,129,0.8); } }
</style>

// update 68 

// update 123 

// update 307 

// update 315 

// update 413 

// u114

// u118

// u163

// u282
