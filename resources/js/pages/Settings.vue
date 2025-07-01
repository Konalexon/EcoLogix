<template>
    <div class="p-4 lg:p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 :class="['text-xl lg:text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">Ustawienia</h1>
                <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Zarządzaj swoim kontem i preferencjami</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sidebar -->
            <div :class="['rounded-2xl border p-4', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                <nav class="space-y-1">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="['w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-all', activeTab === tab.id ? 'bg-emerald-500/10 text-emerald-400' : (isDark ? 'text-slate-400 hover:bg-white/5 hover:text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900')]">
                        <span class="text-xl">{{ tab.icon }}</span>
                        <span class="font-medium">{{ tab.label }}</span>
                    </button>
                </nav>
            </div>

            <!-- Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile -->
                <div v-if="activeTab === 'profile'" :class="['rounded-2xl border p-6', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                    <h2 :class="['text-lg font-semibold mb-6', isDark ? 'text-white' : 'text-gray-900']">Profil użytkownika</h2>
                    
                    <div class="flex items-center gap-6 mb-6">
                        <div class="relative">
                            <img :src="profile.avatar" class="w-24 h-24 rounded-2xl object-cover" />
                            <button class="absolute -bottom-2 -right-2 p-2 bg-emerald-500 text-white rounded-xl shadow-lg hover:bg-emerald-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </button>
                        </div>
                        <div>
                            <h3 :class="['text-xl font-semibold', isDark ? 'text-white' : 'text-gray-900']">{{ profile.name }}</h3>
                            <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">{{ profile.role }}</p>
                            <p class="text-emerald-400 text-sm mt-1">{{ profile.email }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Imię</label>
                            <input v-model="profile.firstName" :class="inputClass" />
                        </div>
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Nazwisko</label>
                            <input v-model="profile.lastName" :class="inputClass" />
                        </div>
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Email</label>
                            <input v-model="profile.email" type="email" :class="inputClass" />
                        </div>
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Telefon</label>
                            <input v-model="profile.phone" :class="inputClass" />
                        </div>
                    </div>

                    <button @click="saveProfile" class="mt-6 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium hover:from-emerald-400 hover:to-emerald-500 transition-all">
                        Zapisz zmiany
                    </button>
                </div>

                <!-- Notifications -->
                <div v-if="activeTab === 'notifications'" :class="['rounded-2xl border p-6', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                    <h2 :class="['text-lg font-semibold mb-6', isDark ? 'text-white' : 'text-gray-900']">Powiadomienia</h2>
                    
                    <div class="space-y-4">
                        <div v-for="notif in notifications" :key="notif.id" :class="['flex items-center justify-between p-4 rounded-xl', isDark ? 'bg-white/5' : 'bg-gray-50']">
                            <div class="flex items-center gap-4">
                                <span class="text-2xl">{{ notif.icon }}</span>
                                <div>
                                    <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ notif.label }}</p>
                                    <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ notif.description }}</p>
                                </div>
                            </div>
                            <button @click="notif.enabled = !notif.enabled" :class="['relative w-12 h-6 rounded-full transition-colors', notif.enabled ? 'bg-emerald-500' : (isDark ? 'bg-slate-600' : 'bg-gray-300')]">
                                <div :class="['absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-all', notif.enabled ? 'left-7' : 'left-1']"></div>
                            </button>
                        </div>
                    </div>

                    <!-- Sound Settings -->
                    <div class="mt-6 pt-6 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
                        <h3 :class="['font-semibold mb-4', isDark ? 'text-white' : 'text-gray-900']">Dźwięki</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">Dźwięki powiadomień</p>
                                <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Odtwarzaj dźwięk przy nowych powiadomieniach</p>
                            </div>
                            <button @click="soundEnabled = !soundEnabled; testSound()" :class="['relative w-12 h-6 rounded-full transition-colors', soundEnabled ? 'bg-emerald-500' : (isDark ? 'bg-slate-600' : 'bg-gray-300')]">
                                <div :class="['absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-all', soundEnabled ? 'left-7' : 'left-1']"></div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Appearance -->
                <div v-if="activeTab === 'appearance'" :class="['rounded-2xl border p-6', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                    <h2 :class="['text-lg font-semibold mb-6', isDark ? 'text-white' : 'text-gray-900']">Wygląd</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <button @click="themeStore.isDark = true; themeStore.toggle(); themeStore.toggle()" :class="['p-6 rounded-2xl border-2 transition-all', isDark ? 'border-emerald-500 bg-emerald-500/10' : 'border-transparent bg-slate-800']">
                            <div class="w-full h-24 bg-slate-900 rounded-xl mb-4 flex items-center justify-center">
                                <span class="text-3xl">🌙</span>
                            </div>
                            <p :class="['font-medium text-center', isDark ? 'text-white' : 'text-white']">Tryb ciemny</p>
                        </button>
                        <button @click="themeStore.isDark = false; themeStore.toggle(); themeStore.toggle()" :class="['p-6 rounded-2xl border-2 transition-all', !isDark ? 'border-emerald-500 bg-emerald-500/10' : 'border-transparent bg-gray-100']">
                            <div class="w-full h-24 bg-white rounded-xl mb-4 flex items-center justify-center border" :class="isDark ? 'border-white/10' : 'border-gray-200'">
                                <span class="text-3xl">☀️</span>
                            </div>
                            <p :class="['font-medium text-center', isDark ? 'text-white' : 'text-gray-900']">Tryb jasny</p>
                        </button>
                    </div>
                </div>

                <!-- Security -->
                <div v-if="activeTab === 'security'" :class="['rounded-2xl border p-6', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                    <h2 :class="['text-lg font-semibold mb-6', isDark ? 'text-white' : 'text-gray-900']">Bezpieczeństwo</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Obecne hasło</label>
                            <input type="password" :class="inputClass" placeholder="••••••••" />
                        </div>
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Nowe hasło</label>
                            <input type="password" :class="inputClass" placeholder="••••••••" />
                        </div>
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Potwierdź hasło</label>
                            <input type="password" :class="inputClass" placeholder="••••••••" />
                        </div>
                    </div>

                    <button class="mt-6 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium">
                        Zmień hasło
                    </button>

                    <div class="mt-8 pt-6 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
                        <h3 :class="['font-semibold mb-4', isDark ? 'text-white' : 'text-gray-900']">Sesje</h3>
                        <div :class="['p-4 rounded-xl flex items-center justify-between', isDark ? 'bg-white/5' : 'bg-gray-50']">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">💻</span>
                                <div>
                                    <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">Windows • Chrome</p>
                                    <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">Aktywna teraz • Warszawa, PL</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-xs rounded-full font-medium">Bieżąca</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useThemeStore } from '@/stores/theme';

const themeStore = useThemeStore();
const isDark = computed(() => themeStore.isDark);

const activeTab = ref('profile');
const soundEnabled = ref(true);

const inputClass = computed(() => isDark.value 
    ? 'w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500/50'
    : 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-emerald-500'
);

const tabs = [
    { id: 'profile', label: 'Profil', icon: '👤' },
    { id: 'notifications', label: 'Powiadomienia', icon: '🔔' },
    { id: 'appearance', label: 'Wygląd', icon: '🎨' },
    { id: 'security', label: 'Bezpieczeństwo', icon: '🔒' },
];

const profile = ref({
    name: 'Jan Kowalski',
    firstName: 'Jan',
    lastName: 'Kowalski',
    email: 'jan.kowalski@ecologix.pl',
    phone: '+48 600 123 456',
    role: 'Administrator',
    avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face'
});

const notifications = ref([
    { id: 1, label: 'Nowe przesyłki', description: 'Powiadomienia o nowych zamówieniach', icon: '📦', enabled: true },
    { id: 2, label: 'Dostawy', description: 'Aktualizacje statusu dostaw', icon: '🚚', enabled: true },
    { id: 3, label: 'Kierowcy', description: 'Status kierowców i pojazdów', icon: '👥', enabled: false },
    { id: 4, label: 'Alerty', description: 'Ważne ostrzeżenia systemowe', icon: '⚠️', enabled: true },
    { id: 5, label: 'Raporty', description: 'Cotygodniowe podsumowania', icon: '📊', enabled: true },
]);

function saveProfile() {
    profile.value.name = `${profile.value.firstName} ${profile.value.lastName}`;
    alert('Profil zapisany!');
}

function testSound() {
    if (soundEnabled.value) {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH6AgoaFgH17f4OFhYWBfXt7fX+ChYWFgn17fHx+');
        audio.play().catch(() => {});
    }
}
</script>

// update 80 

// update 82 

// update 197 

// update 244 

// update 310 

// update 318 

// update 331 

// update 372 

// u243

// 4h572j7l