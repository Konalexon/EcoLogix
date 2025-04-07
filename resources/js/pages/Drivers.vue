<template>
    <div class="p-4 lg:p-6">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 :class="['text-xl lg:text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">Kierowcy</h1>
                <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Zarządzaj zespołem {{ drivers.length }} kierowców</p>
            </div>
            <button @click="showAddModal = true" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:scale-105 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Dodaj kierowcę
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
            <div :class="['p-4 lg:p-5 rounded-2xl border', isDark ? 'bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border-emerald-500/20' : 'bg-emerald-50 border-emerald-200']">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                    <span :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Online</span>
                </div>
                <p :class="['text-2xl lg:text-3xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ drivers.filter(d => d.status === 'online').length }}</p>
            </div>
            <div :class="['p-4 lg:p-5 rounded-2xl border', isDark ? 'bg-gradient-to-br from-blue-500/10 to-blue-600/5 border-blue-500/20' : 'bg-blue-50 border-blue-200']">
                <p :class="['text-sm mb-1', isDark ? 'text-slate-400' : 'text-gray-500']">W trasie</p>
                <p :class="['text-2xl lg:text-3xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ drivers.filter(d => d.onRoute).length }}</p>
            </div>
            <div :class="['p-4 lg:p-5 rounded-2xl border', isDark ? 'bg-gradient-to-br from-amber-500/10 to-amber-600/5 border-amber-500/20' : 'bg-amber-50 border-amber-200']">
                <p :class="['text-sm mb-1', isDark ? 'text-slate-400' : 'text-gray-500']">Średnia ocena</p>
                <p :class="['text-2xl lg:text-3xl font-bold', isDark ? 'text-white' : 'text-gray-900']">4.8 <span class="text-amber-400 text-lg">★</span></p>
            </div>
            <div :class="['p-4 lg:p-5 rounded-2xl border', isDark ? 'bg-gradient-to-br from-purple-500/10 to-purple-600/5 border-purple-500/20' : 'bg-purple-50 border-purple-200']">
                <p :class="['text-sm mb-1', isDark ? 'text-slate-400' : 'text-gray-500']">Dostawy dzisiaj</p>
                <p :class="['text-2xl lg:text-3xl font-bold', isDark ? 'text-white' : 'text-gray-900']">127</p>
            </div>
        </div>

        <!-- Drivers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
            <div v-for="driver in drivers" :key="driver.id" :class="['rounded-2xl border overflow-hidden transition-all hover:scale-[1.02] group', isDark ? 'bg-slate-800/50 border-white/5 hover:border-emerald-500/30' : 'bg-white border-gray-200 hover:border-emerald-300']">
                <!-- Header -->
                <div class="relative h-28 overflow-hidden">
                    <img :src="driver.bgImage" class="w-full h-full object-cover opacity-60 group-hover:scale-110 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold backdrop-blur" :class="driver.status === 'online' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-400'">
                            {{ driver.status === 'online' ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4 lg:p-5 -mt-12 relative">
                    <div class="flex items-end gap-4 mb-4">
                        <div class="relative">
                            <img :src="driver.avatar" class="w-16 h-16 rounded-xl border-4 object-cover" :class="isDark ? 'border-slate-800' : 'border-white'" />
                            <div v-if="driver.status === 'online'" class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 rounded-full border-2" :class="isDark ? 'border-slate-800' : 'border-white'"></div>
                        </div>
                        <div>
                            <h3 :class="['font-semibold text-lg', isDark ? 'text-white' : 'text-gray-900']">{{ driver.name }}</h3>
                            <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ driver.role }}</p>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div :class="['p-2 lg:p-3 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                            <p :class="['text-sm lg:text-lg font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ driver.deliveries }}</p>
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Dostawy</p>
                        </div>
                        <div :class="['p-2 lg:p-3 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                            <p :class="['text-sm lg:text-lg font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ driver.rating }}<span class="text-amber-400 text-xs">★</span></p>
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Ocena</p>
                        </div>
                        <div :class="['p-2 lg:p-3 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                            <p :class="['text-sm lg:text-lg font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ driver.experience }}</p>
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Lat</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button @click="openProfile(driver)" :class="['flex-1 py-2.5 rounded-xl text-sm font-medium transition-all', isDark ? 'bg-white/5 text-white hover:bg-white/10' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">
                            Profil
                        </button>
                        <button @click="openContact(driver)" class="flex-1 py-2.5 bg-emerald-500/20 text-emerald-400 rounded-xl text-sm font-medium hover:bg-emerald-500/30 transition-all">
                            Kontakt
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showProfileModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showProfileModal = false"></div>
                    <div :class="['relative w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl', isDark ? 'bg-slate-800' : 'bg-white']">
                        <!-- Header -->
                        <div class="relative h-40">
                            <img :src="selectedDriver?.bgImage" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                            <button @click="showProfileModal = false" class="absolute top-4 right-4 p-2 rounded-full bg-black/50 text-white hover:bg-black/70">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Profile -->
                        <div class="p-6 -mt-16 relative">
                            <div class="flex items-end gap-4 mb-6">
                                <img :src="selectedDriver?.avatar" class="w-24 h-24 rounded-2xl border-4 object-cover" :class="isDark ? 'border-slate-800' : 'border-white'" />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h2 :class="['text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ selectedDriver?.name }}</h2>
                                        <span v-if="selectedDriver?.status === 'online'" class="px-2 py-0.5 rounded-full text-xs bg-emerald-500/20 text-emerald-400">Online</span>
                                    </div>
                                    <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">{{ selectedDriver?.role }}</p>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-4 gap-4 mb-6">
                                <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <p class="text-2xl font-bold text-emerald-400">{{ selectedDriver?.deliveries }}</p>
                                    <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Dostawy</p>
                                </div>
                                <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <p :class="['text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ selectedDriver?.distance }}</p>
                                    <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">km łącznie</p>
                                </div>
                                <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <p :class="['text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ selectedDriver?.rating }}<span class="text-amber-400">★</span></p>
                                    <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Ocena</p>
                                </div>
                                <div :class="['p-4 rounded-xl text-center', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <p class="text-2xl font-bold text-blue-400">{{ selectedDriver?.onTime }}%</p>
                                    <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Punktualność</p>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h3 :class="['font-semibold mb-3', isDark ? 'text-white' : 'text-gray-900']">Informacje</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Data urodzenia</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedDriver?.birthDate }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Doświadczenie</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedDriver?.experience }} lat</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Zatrudniony od</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedDriver?.hiredDate }}</span></div>
                                        <div class="flex justify-between"><span :class="isDark ? 'text-slate-400' : 'text-gray-500'">Kategorie prawa jazdy</span><span :class="isDark ? 'text-white' : 'text-gray-900'">{{ selectedDriver?.licenseCategories }}</span></div>
                                    </div>
                                </div>
                                <div>
                                    <h3 :class="['font-semibold mb-3', isDark ? 'text-white' : 'text-gray-900']">Pojazd</h3>
                                    <div v-if="selectedDriver?.vehicle" :class="['p-4 rounded-xl', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                        <p :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">{{ selectedDriver?.vehicle }}</p>
                                        <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ selectedDriver?.vehicleType }}</p>
                                    </div>
                                    <p v-else :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Brak przypisanego pojazdu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Contact Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showContactModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showContactModal = false"></div>
                    <div :class="['relative w-full max-w-md rounded-3xl overflow-hidden shadow-2xl', isDark ? 'bg-slate-800' : 'bg-white']">
                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-6">
                                <img :src="selectedDriver?.avatar" class="w-16 h-16 rounded-xl object-cover" />
                                <div>
                                    <h2 :class="['text-xl font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ selectedDriver?.name }}</h2>
                                    <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">{{ selectedDriver?.role }}</p>
                                </div>
                                <button @click="showContactModal = false" class="ml-auto p-2 rounded-lg" :class="isDark ? 'hover:bg-white/10 text-slate-400' : 'hover:bg-gray-100 text-gray-500'">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <div class="space-y-3">
                                <a :href="'tel:' + selectedDriver?.phone" :class="['flex items-center gap-4 p-4 rounded-xl transition-colors', isDark ? 'bg-white/5 hover:bg-white/10' : 'bg-gray-50 hover:bg-gray-100']">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    </div>
                                    <div>
                                        <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ selectedDriver?.phone }}</p>
                                        <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Telefon komórkowy</p>
                                    </div>
                                </a>

                                <a :href="'mailto:' + selectedDriver?.email" :class="['flex items-center gap-4 p-4 rounded-xl transition-colors', isDark ? 'bg-white/5 hover:bg-white/10' : 'bg-gray-50 hover:bg-gray-100']">
                                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ selectedDriver?.email }}</p>
                                        <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Email</p>
                                    </div>
                                </a>

                                <div :class="['flex items-center gap-4 p-4 rounded-xl', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <div>
                                        <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ selectedDriver?.address }}</p>
                                        <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Adres zamieszkania</p>
                                    </div>
                                </div>

                                <div :class="['flex items-center gap-4 p-4 rounded-xl', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    </div>
                                    <div>
                                        <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ selectedDriver?.emergencyContact }}</p>
                                        <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Kontakt alarmowy</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Add Driver Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>
                    <div :class="['relative w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl', isDark ? 'bg-slate-800' : 'bg-white']">
                        <div :class="['p-6 border-b', isDark ? 'border-white/10' : 'border-gray-200']">
                            <h2 :class="['text-xl font-semibold', isDark ? 'text-white' : 'text-gray-900']">Dodaj nowego kierowcę</h2>
                        </div>
                        <form @submit.prevent="addDriver" class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Imię</label>
                                    <input v-model="newDriver.firstName" required :class="inputClass" placeholder="Jan" />
                                </div>
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Nazwisko</label>
                                    <input v-model="newDriver.lastName" required :class="inputClass" placeholder="Kowalski" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Telefon</label>
                                    <input v-model="newDriver.phone" required :class="inputClass" placeholder="+48 600 123 456" />
                                </div>
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Email</label>
                                    <input v-model="newDriver.email" type="email" required :class="inputClass" placeholder="jan@example.com" />
                                </div>
                            </div>
                            <div>
                                <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Stanowisko</label>
                                <select v-model="newDriver.role" :class="inputClass">
                                    <option>Kierowca TIR</option>
                                    <option>Kierowca międzynarodowy</option>
                                    <option>Kierowca Van</option>
                                    <option>Kierowca ciężarówki</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Kategorie prawa jazdy</label>
                                    <input v-model="newDriver.license" :class="inputClass" placeholder="B, C, C+E" />
                                </div>
                                <div>
                                    <label :class="['block text-sm font-medium mb-1', isDark ? 'text-slate-300' : 'text-gray-700']">Lata doświadczenia</label>
                                    <input v-model="newDriver.experience" type="number" :class="inputClass" placeholder="5" />
                                </div>
                            </div>
                            <div class="flex gap-3 pt-4">
                                <button type="button" @click="showAddModal = false" :class="['flex-1 py-3 rounded-xl font-medium', isDark ? 'bg-white/5 text-white hover:bg-white/10' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">Anuluj</button>
                                <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium hover:from-emerald-400 hover:to-emerald-500">Dodaj kierowcę</button>
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

const showProfileModal = ref(false);
const showContactModal = ref(false);
const showAddModal = ref(false);
const selectedDriver = ref(null);

const inputClass = computed(() => isDark.value 
    ? 'w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500/50'
    : 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-emerald-500'
);

const drivers = ref([
    { id: 1, name: 'Tomasz Nowak', role: 'Kierowca TIR', status: 'online', onRoute: true, deliveries: 1247, distance: '456,000', rating: 4.9, onTime: 96, experience: 8, phone: '+48 600 123 456', email: 'tomasz.nowak@ecologix.pl', address: 'ul. Marszałkowska 45, Warszawa', emergencyContact: '+48 601 234 567 (żona)', birthDate: '15.03.1985', hiredDate: '01.06.2016', licenseCategories: 'B, C, C+E', vehicle: 'WA-45821', vehicleType: 'Scania R450', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face', bgImage: 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=400&h=200&fit=crop' },
    { id: 2, name: 'Anna Wiśniewska', role: 'Kierowca międzynarodowy', status: 'online', onRoute: true, deliveries: 892, distance: '312,000', rating: 4.8, onTime: 94, experience: 5, phone: '+48 601 234 567', email: 'anna.wisniewska@ecologix.pl', address: 'ul. Długa 12, Kraków', emergencyContact: '+48 602 345 678 (mąż)', birthDate: '22.07.1990', hiredDate: '15.03.2019', licenseCategories: 'B, C, C+E, ADR', vehicle: 'KR-12390', vehicleType: 'Volvo FH16', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&crop=face', bgImage: 'https://images.unsplash.com/photo-1586191582066-3b5eb72cd5e4?w=400&h=200&fit=crop' },
    { id: 3, name: 'Piotr Kowalczyk', role: 'Kierowca Van', status: 'offline', onRoute: false, deliveries: 2341, distance: '189,000', rating: 4.7, onTime: 92, experience: 12, phone: '+48 602 345 678', email: 'piotr.kowalczyk@ecologix.pl', address: 'ul. Morska 78, Gdańsk', emergencyContact: '+48 603 456 789 (brat)', birthDate: '08.11.1978', hiredDate: '20.01.2012', licenseCategories: 'B, C', vehicle: null, vehicleType: null, avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face', bgImage: 'https://images.unsplash.com/photo-1519003722824-194d4455a60c?w=400&h=200&fit=crop' },
    { id: 4, name: 'Marek Zieliński', role: 'Kierowca ciężarówki', status: 'online', onRoute: true, deliveries: 567, distance: '98,000', rating: 4.9, onTime: 98, experience: 3, phone: '+48 603 456 789', email: 'marek.zielinski@ecologix.pl', address: 'ul. Poznańska 23, Poznań', emergencyContact: '+48 604 567 890 (mama)', birthDate: '30.05.1995', hiredDate: '01.09.2021', licenseCategories: 'B, C, C+E', vehicle: 'PO-34521', vehicleType: 'DAF XF', avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face', bgImage: 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=400&h=200&fit=crop' },
    { id: 5, name: 'Karol Mazur', role: 'Kierowca Van', status: 'online', onRoute: false, deliveries: 423, distance: '67,000', rating: 4.6, onTime: 90, experience: 2, phone: '+48 604 567 890', email: 'karol.mazur@ecologix.pl', address: 'ul. Wrocławska 56, Wrocław', emergencyContact: '+48 605 678 901 (ojciec)', birthDate: '12.09.1998', hiredDate: '15.06.2022', licenseCategories: 'B', vehicle: null, vehicleType: null, avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&h=200&fit=crop&crop=face', bgImage: 'https://images.unsplash.com/photo-1586191582066-3b5eb72cd5e4?w=400&h=200&fit=crop' },
]);

const newDriver = ref({ firstName: '', lastName: '', phone: '', email: '', role: 'Kierowca TIR', license: '', experience: '' });

function openProfile(driver) {
    selectedDriver.value = driver;
    showProfileModal.value = true;
}

function openContact(driver) {
    selectedDriver.value = driver;
    showContactModal.value = true;
}

function addDriver() {
    drivers.value.push({
        id: Date.now(),
        name: `${newDriver.value.firstName} ${newDriver.value.lastName}`,
        role: newDriver.value.role,
        status: 'offline',
        onRoute: false,
        deliveries: 0,
        distance: '0',
        rating: 5.0,
        onTime: 100,
        experience: parseInt(newDriver.value.experience) || 0,
        phone: newDriver.value.phone,
        email: newDriver.value.email,
        address: 'Do uzupełnienia',
        emergencyContact: 'Do uzupełnienia',
        birthDate: 'Do uzupełnienia',
        hiredDate: new Date().toLocaleDateString('pl-PL'),
        licenseCategories: newDriver.value.license,
        vehicle: null,
        vehicleType: null,
        avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face',
        bgImage: 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=400&h=200&fit=crop'
    });
    showAddModal.value = false;
    newDriver.value = { firstName: '', lastName: '', phone: '', email: '', role: 'Kierowca TIR', license: '', experience: '' };
}
</script>

<style>
.modal-enter-active, .modal-leave-active { transition: opacity 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>

// update 74 

// update 163 

// update 226 

// update 269 

// update 410 

// u167

// u175

// u355

// u407

// r0jolsx