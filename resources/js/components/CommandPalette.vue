<template>
    <!-- Command Palette Overlay -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-start justify-center pt-[15vh]" @click.self="close">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                
                <div :class="['relative w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden', isDark ? 'bg-slate-800' : 'bg-white']">
                    <!-- Search Input -->
                    <div :class="['flex items-center gap-3 p-4 border-b', isDark ? 'border-white/10' : 'border-gray-200']">
                        <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input ref="inputRef" v-model="query" @keydown.down.prevent="navigateDown" @keydown.up.prevent="navigateUp" @keydown.enter="selectCurrent" @keydown.esc="close" type="text" placeholder="Szukaj stron, poleceń, przesyłek..." :class="['flex-1 bg-transparent border-none outline-none text-lg', isDark ? 'text-white placeholder-slate-500' : 'text-gray-900 placeholder-gray-400']" />
                        <kbd :class="['px-2 py-1 rounded text-xs font-mono', isDark ? 'bg-white/10 text-slate-400' : 'bg-gray-100 text-gray-500']">ESC</kbd>
                    </div>

                    <!-- Results -->
                    <div class="max-h-[50vh] overflow-y-auto p-2">
                        <!-- Pages -->
                        <div v-if="filteredPages.length" class="mb-4">
                            <p :class="['text-xs font-medium uppercase tracking-wider px-3 py-2', isDark ? 'text-slate-500' : 'text-gray-500']">Strony</p>
                            <button v-for="(item, i) in filteredPages" :key="item.path" @click="goTo(item.path)" @mouseenter="selectedIndex = i" :class="['w-full flex items-center gap-3 p-3 rounded-xl text-left transition-colors', selectedIndex === i ? (isDark ? 'bg-emerald-500/20' : 'bg-emerald-50') : (isDark ? 'hover:bg-white/5' : 'hover:bg-gray-50')]">
                                <span class="text-xl">{{ item.icon }}</span>
                                <div class="flex-1">
                                    <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ item.name }}</p>
                                    <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ item.description }}</p>
                                </div>
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </div>

                        <!-- Commands -->
                        <div v-if="filteredCommands.length" class="mb-4">
                            <p :class="['text-xs font-medium uppercase tracking-wider px-3 py-2', isDark ? 'text-slate-500' : 'text-gray-500']">Polecenia</p>
                            <button v-for="(cmd, i) in filteredCommands" :key="cmd.id" @click="executeCommand(cmd)" @mouseenter="selectedIndex = filteredPages.length + i" :class="['w-full flex items-center gap-3 p-3 rounded-xl text-left transition-colors', selectedIndex === filteredPages.length + i ? (isDark ? 'bg-emerald-500/20' : 'bg-emerald-50') : (isDark ? 'hover:bg-white/5' : 'hover:bg-gray-50')]">
                                <span class="text-xl">{{ cmd.icon }}</span>
                                <div class="flex-1">
                                    <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ cmd.name }}</p>
                                </div>
                                <kbd v-if="cmd.shortcut" :class="['px-2 py-1 rounded text-xs font-mono', isDark ? 'bg-white/10 text-slate-400' : 'bg-gray-100 text-gray-500']">{{ cmd.shortcut }}</kbd>
                            </button>
                        </div>

                        <!-- Recent shipments -->
                        <div v-if="query.length > 1 && filteredShipments.length">
                            <p :class="['text-xs font-medium uppercase tracking-wider px-3 py-2', isDark ? 'text-slate-500' : 'text-gray-500']">Przesyłki</p>
                            <button v-for="ship in filteredShipments" :key="ship.id" @click="goTo('/shipments')" :class="['w-full flex items-center gap-3 p-3 rounded-xl text-left transition-colors', isDark ? 'hover:bg-white/5' : 'hover:bg-gray-50']">
                                <span class="text-xl">📦</span>
                                <div class="flex-1">
                                    <p :class="['font-medium font-mono', isDark ? 'text-white' : 'text-gray-900']">{{ ship.number }}</p>
                                    <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ ship.from }} → {{ ship.to }}</p>
                                </div>
                                <span :class="['px-2 py-1 rounded-full text-xs', ship.status === 'delivered' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-blue-500/20 text-blue-400']">{{ ship.statusLabel }}</span>
                            </button>
                        </div>

                        <!-- No results -->
                        <div v-if="query && !filteredPages.length && !filteredCommands.length && !filteredShipments.length" class="text-center py-8">
                            <span class="text-4xl mb-4 block">🔍</span>
                            <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Brak wyników dla "{{ query }}"</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div :class="['flex items-center justify-between p-3 border-t text-xs', isDark ? 'border-white/10 text-slate-500' : 'border-gray-200 text-gray-500']">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-white/10">↑↓</kbd> nawiguj</span>
                            <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-white/10">↵</kbd> wybierz</span>
                            <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 rounded bg-white/10">esc</kbd> zamknij</span>
                        </div>
                        <span>EcoLogix Command Palette</span>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useThemeStore } from '@/stores/theme';

const router = useRouter();
const themeStore = useThemeStore();
const isDark = computed(() => themeStore.isDark);

const isOpen = ref(false);
const query = ref('');
const selectedIndex = ref(0);
const inputRef = ref(null);

const pages = [
    { path: '/', name: 'Dashboard', description: 'Główny panel', icon: '📊' },
    { path: '/fleet', name: 'Mapa floty', description: 'Lokalizacja pojazdów', icon: '🗺️' },
    { path: '/shipments', name: 'Przesyłki', description: 'Zarządzanie przesyłkami', icon: '📦' },
    { path: '/vehicles', name: 'Pojazdy', description: 'Zarządzanie flotą', icon: '🚚' },
    { path: '/drivers', name: 'Kierowcy', description: 'Zespół kierowców', icon: '👥' },
    { path: '/analytics', name: 'Analityka', description: 'Raporty i statystyki', icon: '📈' },
    { path: '/leaderboard', name: 'Ranking', description: 'Top kierowcy', icon: '🏆' },
    { path: '/calendar', name: 'Kalendarz', description: 'Harmonogram dostaw', icon: '📅' },
    { path: '/calculator', name: 'Kalkulator', description: 'Oblicz koszt dostawy', icon: '💰' },
    { path: '/settings', name: 'Ustawienia', description: 'Konfiguracja konta', icon: '⚙️' },
];

const commands = [
    { id: 'theme', name: 'Zmień motyw', icon: '🎨', shortcut: 'Ctrl+D', action: () => themeStore.toggle() },
    { id: 'new-shipment', name: 'Nowa przesyłka', icon: '➕', action: () => { router.push('/shipments'); close(); } },
    { id: 'export', name: 'Eksportuj raport', icon: '📄', action: () => alert('Eksportowanie...') },
];

const shipments = [
    { id: 1, number: 'ECO24X8KL9', from: 'Warszawa', to: 'Kraków', status: 'transit', statusLabel: 'W trasie' },
    { id: 2, number: 'ECO24Y2ML3', from: 'Gdańsk', to: 'Poznań', status: 'delivered', statusLabel: 'Dostarczone' },
    { id: 3, number: 'ECO24Z5NP7', from: 'Wrocław', to: 'Łódź', status: 'pending', statusLabel: 'Oczekuje' },
];

const filteredPages = computed(() => {
    if (!query.value) return pages.slice(0, 5);
    return pages.filter(p => p.name.toLowerCase().includes(query.value.toLowerCase()) || p.description.toLowerCase().includes(query.value.toLowerCase()));
});

const filteredCommands = computed(() => {
    if (!query.value) return commands;
    return commands.filter(c => c.name.toLowerCase().includes(query.value.toLowerCase()));
});

const filteredShipments = computed(() => {
    if (!query.value) return [];
    return shipments.filter(s => s.number.toLowerCase().includes(query.value.toLowerCase()));
});

function open() {
    isOpen.value = true;
    query.value = '';
    selectedIndex.value = 0;
    nextTick(() => inputRef.value?.focus());
}

function close() {
    isOpen.value = false;
}

function goTo(path) {
    router.push(path);
    close();
}

function executeCommand(cmd) {
    cmd.action?.();
    close();
}

function navigateDown() {
    const total = filteredPages.value.length + filteredCommands.value.length;
    selectedIndex.value = (selectedIndex.value + 1) % total;
}

function navigateUp() {
    const total = filteredPages.value.length + filteredCommands.value.length;
    selectedIndex.value = (selectedIndex.value - 1 + total) % total;
}

function selectCurrent() {
    if (selectedIndex.value < filteredPages.value.length) {
        goTo(filteredPages.value[selectedIndex.value].path);
    } else {
        const cmdIndex = selectedIndex.value - filteredPages.value.length;
        if (filteredCommands.value[cmdIndex]) {
            executeCommand(filteredCommands.value[cmdIndex]);
        }
    }
}

function handleKeydown(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        isOpen.value ? close() : open();
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});

defineExpose({ open, close, isOpen });
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>

// update 128 

// update 183 

// update 224 

// u186

// u227
