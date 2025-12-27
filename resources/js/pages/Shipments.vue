<template>
    <div class="p-4 lg:p-6">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 :class="['text-xl lg:text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">Przesyłki</h1>
                <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Zarządzaj wszystkimi przesyłkami</p>
            </div>
            <button @click="showAddModal = true" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:scale-105 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Nowa przesyłka
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
            <button v-for="stat in stats" :key="stat.label" @click="activeFilter = stat.filter" :class="['p-4 rounded-xl border text-left transition-all hover:scale-[1.02]', activeFilter === stat.filter ? (isDark ? 'bg-emerald-500/20 border-emerald-500/30' : 'bg-emerald-50 border-emerald-300') : (isDark ? 'bg-white/5 border-white/10 hover:border-white/20' : 'bg-white border-gray-200 hover:border-gray-300')]">
                <p :class="['text-xl lg:text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ stat.value }}</p>
                <p :class="['text-xs lg:text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ stat.label }}</p>
            </button>
        </div>

        <!-- Filters -->
        <div :class="['flex flex-col lg:flex-row items-stretch lg:items-center gap-4 mb-6 p-4 rounded-2xl border', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
            <div class="flex-1 relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5" :class="isDark ? 'text-slate-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input v-model="search" type="text" placeholder="Szukaj po numerze, odbiorcy..." :class="['w-full pl-12 pr-4 py-2.5 rounded-xl border transition-all', isDark ? 'bg-white/5 border-white/10 text-white placeholder-slate-500 focus:border-emerald-500/50' : 'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-400 focus:border-emerald-500']" />
            </div>
            <select v-model="priorityFilter" :class="['px-4 py-2.5 rounded-xl border', isDark ? 'bg-white/5 border-white/10 text-white' : 'bg-gray-50 border-gray-200 text-gray-900']">
                <option value="all">Wszystkie priorytety</option>
                <option value="express">Express</option>
                <option value="standard">Standard</option>
                <option value="economy">Economy</option>
            </select>
            <select v-model="dateFilter" :class="['px-4 py-2.5 rounded-xl border', isDark ? 'bg-white/5 border-white/10 text-white' : 'bg-gray-50 border-gray-200 text-gray-900']">
                <option value="7">Ostatnie 7 dni</option>
                <option value="30">Ostatnie 30 dni</option>
                <option value="90">Ostatnie 90 dni</option>
            </select>
        </div>

        <!-- Shipments Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <div v-for="s in filteredShipments" :key="s.id" @click="openDetails(s)" :class="['rounded-2xl border overflow-hidden cursor-pointer transition-all hover:scale-[1.01]', isDark ? 'bg-slate-800/50 border-white/5 hover:border-emerald-500/30' : 'bg-white border-gray-200 hover:border-emerald-300']">
                <div class="p-4 lg:p-5">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div :class="['w-12 h-12 rounded-xl flex items-center justify-center', statusBg(s.status)]">
                                <span class="text-xl">{{ statusIcon(s.status) }}</span>
                            </div>
                            <div>
                                <p class="font-mono font-bold text-emerald-400 text-lg">{{ s.number }}</p>
                                <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ s.createdAt }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span :class="['px-3 py-1 rounded-full text-xs font-semibold', priorityClass(s.priority)]">{{ s.priority }}</span>
                            <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusClass(s.status)]">{{ s.statusLabel }}</span>
                        </div>
                    </div>

                    <!-- Route -->
                    <div :class="['flex items-center gap-3 p-3 rounded-xl mb-4', isDark ? 'bg-white/5' : 'bg-gray-50']">
                        <div class="flex-1 text-center">
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Z</p>
                            <p :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">{{ s.from }}</p>
                        </div>
                        <div class="flex items-center gap-1 text-emerald-400">
                            <div class="w-8 h-0.5 bg-emerald-400/30"></div>
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            <div class="w-8 h-0.5 bg-emerald-400/30"></div>
                        </div>
                        <div class="flex-1 text-center">
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Do</p>
                            <p :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">{{ s.to }}</p>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Odbiorca</p>
                            <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ s.recipient }}</p>
                        </div>
                        <div>
                            <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Waga</p>
                            <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ s.weight }} kg</p>
                        </div>
                        <div>
                            <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">ETA</p>
                            <p :class="['font-medium', s.status === 'delivered' ? 'text-emerald-400' : (isDark ? 'text-white' : 'text-gray-900')]">{{ s.eta }}</p>
                        </div>
                    </div>
                </div>

                <!-- Progress bar for transit -->
                <div v-if="s.status === 'transit'" class="px-5 pb-4">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Postęp dostawy</span>
                        <span class="text-emerald-400 font-medium">{{ s.progress }}%</span>
                    </div>
                    <div :class="['h-2 rounded-full overflow-hidden', isDark ? 'bg-slate-700' : 'bg-gray-200']">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full transition-all" :style="{ width: s.progress + '%' }"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipment Details Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showDetailsModal = false"></div>
                    <div :class="['relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-3xl shadow-2xl', isDark ? 'bg-slate-800' : 'bg-white']">
                        <!-- Header -->
                        <div :class="['sticky top-0 p-6 border-b backdrop-blur-xl z-10', isDark ? 'bg-slate-800/90 border-white/10' : 'bg-white/90 border-gray-200']">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center', statusBg(selectedShipment?.status)]">
                                        <span class="text-2xl">{{ statusIcon(selectedShipment?.status) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-mono font-bold text-emerald-400 text-2xl">{{ selectedShipment?.number }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusClass(selectedShipment?.status)]">{{ selectedShipment?.statusLabel }}</span>
                                            <span :class="['px-3 py-1 rounded-full text-xs font-semibold', priorityClass(selectedShipment?.priority)]">{{ selectedShipment?.priority }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button @click="showDetailsModal = false" :class="['p-2 rounded-xl transition-colors', isDark ? 'hover:bg-white/10 text-slate-400' : 'hover:bg-gray-100 text-gray-500']">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Route visualization -->
                            <div :class="['p-5 rounded-2xl border', isDark ? 'bg-white/5 border-white/10' : 'bg-gray-50 border-gray-200']">
                                <div class="flex items-center justify-between">
                                    <div class="text-center">
                                        <div :class="['w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2', isDark ? 'bg-blue-500/20' : 'bg-blue-100']">
                                            <span class="text-xl">📍</span>
                                        </div>
                                        <p :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">{{ selectedShipment?.from }}</p>
                                        <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Nadawca</p>
                                    </div>
                                    <div class="flex-1 px-4">
                                        <div class="relative">
                                            <div :class="['h-1 rounded-full', isDark ? 'bg-slate-700' : 'bg-gray-200']"></div>
                                            <div class="absolute top-0 left-0 h-1 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full transition-all" :style="{ width: (selectedShipment?.progress || 0) + '%' }"></div>
                                            <div class="absolute top-1/2 -translate-y-1/2 bg-emerald-500 w-4 h-4 rounded-full border-2 border-white shadow-lg transition-all" :style="{ left: (selectedShipment?.progress || 0) + '%', marginLeft: '-8px' }">
                                                <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs text-emerald-400 font-medium whitespace-nowrap">{{ selectedShipment?.progress }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div :class="['w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2', selectedShipment?.status === 'delivered' ? (isDark ? 'bg-emerald-500/20' : 'bg-emerald-100') : (isDark ? 'bg-slate-700' : 'bg-gray-200')]">
                                            <span class="text-xl">🏠</span>
                                        </div>
                                        <p :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">{{ selectedShipment?.to }}</p>
                                        <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Odbiorca</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div>
                                <h3 :class="['font-semibold mb-4', isDark ? 'text-white' : 'text-gray-900']">Historia przesyłki</h3>
                                <div class="space-y-4">
                                    <div v-for="(event, i) in selectedShipment?.timeline" :key="i" class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div :class="['w-10 h-10 rounded-full flex items-center justify-center shrink-0', event.completed ? 'bg-emerald-500' : (isDark ? 'bg-slate-700' : 'bg-gray-200')]">
                                                <svg v-if="event.completed" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                <div v-else :class="['w-3 h-3 rounded-full', isDark ? 'bg-slate-500' : 'bg-gray-400']"></div>
                                            </div>
                                            <div v-if="i < selectedShipment?.timeline.length - 1" :class="['w-0.5 flex-1 my-1', event.completed ? 'bg-emerald-500' : (isDark ? 'bg-slate-700' : 'bg-gray-200')]"></div>
                                        </div>
                                        <div class="pb-4">
                                            <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ event.title }}</p>
                                            <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ event.description }}</p>
                                            <p :class="['text-xs mt-1', isDark ? 'text-slate-500' : 'text-gray-400']">{{ event.time }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h3 :class="['font-semibold mb-3', isDark ? 'text-white' : 'text-gray-900']">Szczegóły przesyłki</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Waga</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedShipment?.weight }} kg</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Wymiary</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedShipment?.dimensions }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Zawartość</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedShipment?.contents }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Ubezpieczenie</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedShipment?.insurance }}</span></div>
                                    </div>
                                </div>
                                <div>
                                    <h3 :class="['font-semibold mb-3', isDark ? 'text-white' : 'text-gray-900']">Odbiorca</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Nazwa</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedShipment?.recipient }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Telefon</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedShipment?.phone }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Adres</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedShipment?.address }}</span></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Driver info -->
                            <div v-if="selectedShipment?.driver" :class="['p-4 rounded-xl flex items-center gap-4', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                <img :src="selectedShipment?.driverAvatar" class="w-14 h-14 rounded-xl object-cover" />
                                <div class="flex-1">
                                    <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ selectedShipment?.driver }}</p>
                                    <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Kierowca • {{ selectedShipment?.vehicle }}</p>
                                </div>
                                <a :href="'tel:' + selectedShipment?.driverPhone" class="p-3 bg-emerald-500/20 text-emerald-400 rounded-xl hover:bg-emerald-500/30 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Add Shipment Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>
                    <div :class="['relative w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl', isDark ? 'bg-slate-800' : 'bg-white']">
                        <div :class="['p-6 border-b', isDark ? 'border-white/10' : 'border-gray-200']">
                            <h2 :class="['text-xl font-semibold', isDark ? 'text-white' : 'text-gray-900']">Nowa przesyłka</h2>
                        </div>
                        <form @submit.prevent="addShipment" class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Miasto nadania</label>
                                    <input v-model="newShipment.from" required :class="inputClass" placeholder="Warszawa" />
                                </div>
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Miasto dostawy</label>
                                    <input v-model="newShipment.to" required :class="inputClass" placeholder="Kraków" />
                                </div>
                            </div>
                            <div>
                                <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Odbiorca</label>
                                <input v-model="newShipment.recipient" required :class="inputClass" placeholder="Jan Kowalski" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Telefon</label>
                                    <input v-model="newShipment.phone" :class="inputClass" placeholder="+48 600 123 456" />
                                </div>
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Waga (kg)</label>
                                    <input v-model="newShipment.weight" type="number" :class="inputClass" placeholder="15" />
                                </div>
                            </div>
                            <div>
                                <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Priorytet</label>
                                <select v-model="newShipment.priority" :class="inputClass">
                                    <option>Express</option>
                                    <option>Standard</option>
                                    <option>Economy</option>
                                </select>
                            </div>
                            <div class="flex gap-3 pt-4">
                                <button type="button" @click="showAddModal = false" :class="['flex-1 py-3 rounded-xl font-medium', isDark ? 'bg-white/5 text-white hover:bg-white/10' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">Anuluj</button>
                                <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium">Utwórz przesyłkę</button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useThemeStore } from '@/stores/theme';

const themeStore = useThemeStore();
const isDark = computed(() => themeStore.isDark);

const search = ref('');
const activeFilter = ref('all');
const priorityFilter = ref('all');
const dateFilter = ref('7');
const showDetailsModal = ref(false);
const showAddModal = ref(false);
const selectedShipment = ref(null);

const inputClass = computed(() => isDark.value 
    ? 'w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500/50'
    : 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-emerald-500'
);

const stats = ref([
    { label: 'Wszystkie', value: 248, filter: 'all' },
    { label: 'W realizacji', value: 45, filter: 'transit' },
    { label: 'Dostarczone', value: 189, filter: 'delivered' },
    { label: 'Oczekujące', value: 12, filter: 'pending' },
    { label: 'Opóźnione', value: 2, filter: 'delayed' },
]);

const shipments = ref([
    { id: 1, number: 'ECO24X8KL9', recipient: 'Jan Kowalski', phone: '+48 600 123 456', address: 'ul. Marszałkowska 45/12, Kraków', from: 'Warszawa', to: 'Kraków', status: 'transit', statusLabel: 'W trasie', priority: 'Express', weight: 15.5, dimensions: '60x40x30 cm', contents: 'Elektronika', insurance: '5,000 PLN', eta: 'Dziś, 14:30', progress: 65, createdAt: '29.12.2024, 08:00', driver: 'Tomasz Nowak', driverAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face', driverPhone: '+48 600 123 456', vehicle: 'WA-45821', timeline: [
        { title: 'Przesyłka nadana', description: 'Odebrano przesyłkę z magazynu Warszawa', time: '29.12.2024, 08:00', completed: true },
        { title: 'W drodze', description: 'Przesyłka opuściła sortownię', time: '29.12.2024, 09:30', completed: true },
        { title: 'Transport', description: 'W trasie do Krakowa', time: '29.12.2024, 10:15', completed: true },
        { title: 'Dostarczenie', description: 'Planowana dostawa', time: '29.12.2024, 14:30', completed: false },
    ]},
    { id: 2, number: 'ECO24Y2ML3', recipient: 'Anna Nowak', phone: '+48 601 234 567', address: 'ul. Długa 78, Poznań', from: 'Gdańsk', to: 'Poznań', status: 'delivered', statusLabel: 'Dostarczone', priority: 'Standard', weight: 8.2, dimensions: '40x30x20 cm', contents: 'Odzież', insurance: '1,000 PLN', eta: 'Dostarczono', progress: 100, createdAt: '28.12.2024, 14:00', driver: 'Piotr Kowalczyk', driverAvatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face', driverPhone: '+48 602 345 678', vehicle: 'GD-78234', timeline: [
        { title: 'Przesyłka nadana', description: 'Odebrano z magazynu Gdańsk', time: '28.12.2024, 14:00', completed: true },
        { title: 'W drodze', description: 'Przesyłka opuściła sortownię', time: '28.12.2024, 15:30', completed: true },
        { title: 'Transport', description: 'W trasie do Poznania', time: '28.12.2024, 16:00', completed: true },
        { title: 'Dostarczono', description: 'Odebrane przez: Anna Nowak', time: '28.12.2024, 19:45', completed: true },
    ]},
    { id: 3, number: 'ECO24Z5NP7', recipient: 'Piotr Wiśniewski', phone: '+48 602 345 678', address: 'ul. Piotrkowska 123, Łódź', from: 'Wrocław', to: 'Łódź', status: 'pending', statusLabel: 'Oczekuje', priority: 'Economy', weight: 22.0, dimensions: '80x60x40 cm', contents: 'Meble', insurance: '2,500 PLN', eta: 'Jutro, 10:00', progress: 0, createdAt: '29.12.2024, 16:00', driver: null, driverAvatar: null, driverPhone: null, vehicle: null, timeline: [
        { title: 'Przesyłka zarejestrowana', description: 'Oczekuje na odbiór z magazynu', time: '29.12.2024, 16:00', completed: true },
        { title: 'Odbiór', description: 'Zaplanowany odbiór', time: '30.12.2024, 08:00', completed: false },
        { title: 'Transport', description: 'Planowany transport', time: '30.12.2024, 09:00', completed: false },
        { title: 'Dostarczenie', description: 'Planowana dostawa', time: '30.12.2024, 10:00', completed: false },
    ]},
    { id: 4, number: 'ECO24A1QR2', recipient: 'Maria Kamińska', phone: '+48 603 456 789', address: 'ul. Wojska Polskiego 56, Szczecin', from: 'Poznań', to: 'Szczecin', status: 'transit', statusLabel: 'W trasie', priority: 'Express', weight: 5.8, dimensions: '30x20x15 cm', contents: 'Dokumenty', insurance: '500 PLN', eta: 'Dziś, 16:00', progress: 40, createdAt: '29.12.2024, 10:00', driver: 'Marek Zieliński', driverAvatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face', driverPhone: '+48 603 456 789', vehicle: 'PO-34521', timeline: [
        { title: 'Przesyłka nadana', description: 'Odebrano z biura Poznań', time: '29.12.2024, 10:00', completed: true },
        { title: 'Transport', description: 'W trasie do Szczecina', time: '29.12.2024, 11:00', completed: true },
        { title: 'Dostarczenie', description: 'Planowana dostawa', time: '29.12.2024, 16:00', completed: false },
    ]},
    { id: 5, number: 'ECO24B3ST4', recipient: 'Tomasz Lewandowski', phone: '+48 604 567 890', address: 'ul. Mikołowska 89, Katowice', from: 'Kraków', to: 'Katowice', status: 'delayed', statusLabel: 'Opóźniona', priority: 'Standard', weight: 12.3, dimensions: '50x40x35 cm', contents: 'Sprzęt sportowy', insurance: '1,500 PLN', eta: '+2h opóźnienia', progress: 55, createdAt: '29.12.2024, 07:00', driver: 'Anna Wiśniewska', driverAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face', driverPhone: '+48 601 234 567', vehicle: 'KR-12390', timeline: [
        { title: 'Przesyłka nadana', description: 'Odebrano z magazynu Kraków', time: '29.12.2024, 07:00', completed: true },
        { title: 'Transport', description: 'W trasie do Katowic', time: '29.12.2024, 08:30', completed: true },
        { title: '⚠️ Opóźnienie', description: 'Korek na A4 - opóźnienie ~2h', time: '29.12.2024, 10:00', completed: true },
        { title: 'Dostarczenie', description: 'Nowy ETA: 14:00', time: 'Oczekiwane', completed: false },
    ]},
]);

const newShipment = ref({ from: '', to: '', recipient: '', phone: '', weight: '', priority: 'Standard' });

const filteredShipments = computed(() => {
    let result = shipments.value;
    if (activeFilter.value !== 'all') result = result.filter(s => s.status === activeFilter.value);
    if (priorityFilter.value !== 'all') result = result.filter(s => s.priority.toLowerCase() === priorityFilter.value);
    if (search.value) result = result.filter(s => s.number.toLowerCase().includes(search.value.toLowerCase()) || s.recipient.toLowerCase().includes(search.value.toLowerCase()));
    return result;
});

function statusClass(status) {
    return { 'transit': 'bg-blue-500/20 text-blue-400', 'delivered': 'bg-emerald-500/20 text-emerald-400', 'pending': 'bg-amber-500/20 text-amber-400', 'delayed': 'bg-red-500/20 text-red-400' }[status] || 'bg-slate-500/20 text-slate-400';
}

function statusBg(status) {
    return { 'transit': 'bg-blue-500/20', 'delivered': 'bg-emerald-500/20', 'pending': 'bg-amber-500/20', 'delayed': 'bg-red-500/20' }[status] || 'bg-slate-500/20';
}

function statusIcon(status) {
    return { 'transit': '🚚', 'delivered': '✅', 'pending': '📦', 'delayed': '⚠️' }[status] || '📦';
}

function priorityClass(priority) {
    return { 'Express': 'bg-purple-500/20 text-purple-400', 'Standard': 'bg-slate-500/20 text-slate-400', 'Economy': 'bg-teal-500/20 text-teal-400' }[priority] || 'bg-slate-500/20 text-slate-400';
}

function openDetails(s) {
    selectedShipment.value = s;
    showDetailsModal.value = true;
}

function addShipment() {
    const num = 'ECO' + Math.random().toString(36).substr(2, 8).toUpperCase();
    shipments.value.unshift({
        id: Date.now(),
        number: num,
        recipient: newShipment.value.recipient,
        phone: newShipment.value.phone,
        address: 'Do uzupełnienia',
        from: newShipment.value.from,
        to: newShipment.value.to,
        status: 'pending',
        statusLabel: 'Oczekuje',
        priority: newShipment.value.priority,
        weight: parseFloat(newShipment.value.weight) || 1,
        dimensions: 'Do uzupełnienia',
        contents: 'Do uzupełnienia',
        insurance: '500 PLN',
        eta: 'Do ustalenia',
        progress: 0,
        createdAt: new Date().toLocaleString('pl-PL'),
        driver: null,
        driverAvatar: null,
        driverPhone: null,
        vehicle: null,
        timeline: [
            { title: 'Przesyłka zarejestrowana', description: 'Utworzono w systemie', time: new Date().toLocaleString('pl-PL'), completed: true },
        ]
    });
    stats.value[0].value++;
    stats.value[3].value++;
    showAddModal.value = false;
    newShipment.value = { from: '', to: '', recipient: '', phone: '', weight: '', priority: 'Standard' };
}
</script>

<style>
.modal-enter-active, .modal-leave-active { transition: opacity 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>

// update 234 

// update 306 

// update 376 

// update 403 

// u224

// u238
