<template>
    <div class="p-4 lg:p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 :class="['text-xl lg:text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">Ranking kierowców</h1>
                <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Top performerzy tego miesiąca</p>
            </div>
            <select :class="['px-4 py-2 rounded-xl border', isDark ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-gray-200 text-gray-900']">
                <option>Ten miesiąc</option>
                <option>Ten tydzień</option>
                <option>Ten rok</option>
            </select>
        </div>

        <!-- Top 3 Podium -->
        <div class="flex justify-center items-end gap-4 mb-8">
            <!-- 2nd Place -->
            <div class="text-center">
                <div class="relative">
                    <img :src="topDrivers[1]?.avatar" class="w-20 h-20 rounded-2xl object-cover mx-auto border-4 border-slate-400 shadow-lg" />
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-8 h-8 bg-gradient-to-br from-slate-300 to-slate-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg">2</div>
                </div>
                <p :class="['font-semibold mt-4', isDark ? 'text-white' : 'text-gray-900']">{{ topDrivers[1]?.name }}</p>
                <p class="text-slate-400 text-sm">{{ topDrivers[1]?.points }} pkt</p>
                <div :class="['h-24 w-24 rounded-t-2xl mt-2 flex items-center justify-center', isDark ? 'bg-slate-700' : 'bg-slate-200']">
                    <span class="text-4xl">🥈</span>
                </div>
            </div>

            <!-- 1st Place -->
            <div class="text-center -mt-8">
                <div class="relative">
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 text-4xl animate-bounce">👑</div>
                    <img :src="topDrivers[0]?.avatar" class="w-28 h-28 rounded-2xl object-cover mx-auto border-4 border-yellow-400 shadow-xl" />
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-10 h-10 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">1</div>
                </div>
                <p :class="['font-bold text-lg mt-4', isDark ? 'text-white' : 'text-gray-900']">{{ topDrivers[0]?.name }}</p>
                <p class="text-emerald-400 font-semibold">{{ topDrivers[0]?.points }} pkt</p>
                <div class="h-32 w-28 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-t-2xl mt-2 flex items-center justify-center shadow-lg">
                    <span class="text-5xl">🏆</span>
                </div>
            </div>

            <!-- 3rd Place -->
            <div class="text-center">
                <div class="relative">
                    <img :src="topDrivers[2]?.avatar" class="w-20 h-20 rounded-2xl object-cover mx-auto border-4 border-amber-700 shadow-lg" />
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-8 h-8 bg-gradient-to-br from-amber-600 to-amber-800 rounded-full flex items-center justify-center text-white font-bold shadow-lg">3</div>
                </div>
                <p :class="['font-semibold mt-4', isDark ? 'text-white' : 'text-gray-900']">{{ topDrivers[2]?.name }}</p>
                <p class="text-slate-400 text-sm">{{ topDrivers[2]?.points }} pkt</p>
                <div :class="['h-16 w-24 rounded-t-2xl mt-2 flex items-center justify-center', isDark ? 'bg-amber-900/50' : 'bg-amber-100']">
                    <span class="text-3xl">🥉</span>
                </div>
            </div>
        </div>

        <!-- Full Leaderboard -->
        <div :class="['rounded-2xl border overflow-hidden', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
            <div :class="['p-4 border-b', isDark ? 'border-white/10' : 'border-gray-200']">
                <h3 :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">Pełny ranking</h3>
            </div>
            <div class="divide-y" :class="isDark ? 'divide-white/5' : 'divide-gray-100'">
                <div v-for="(driver, index) in allDrivers" :key="driver.id" :class="['flex items-center gap-4 p-4 transition-colors', isDark ? 'hover:bg-white/5' : 'hover:bg-gray-50']">
                    <div class="w-8 text-center">
                        <span v-if="index < 3" class="text-xl">{{ ['🥇', '🥈', '🥉'][index] }}</span>
                        <span v-else :class="['font-bold', isDark ? 'text-slate-400' : 'text-gray-500']">{{ index + 1 }}</span>
                    </div>
                    <img :src="driver.avatar" class="w-12 h-12 rounded-xl object-cover" />
                    <div class="flex-1">
                        <p :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">{{ driver.name }}</p>
                        <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ driver.deliveries }} dostaw • {{ driver.rating }}★</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-emerald-400">{{ driver.points }} pkt</p>
                        <p :class="['text-xs', driver.change > 0 ? 'text-emerald-400' : 'text-red-400']">
                            {{ driver.change > 0 ? '↑' : '↓' }} {{ Math.abs(driver.change) }} vs tydzień
                        </p>
                    </div>
                    <!-- Badges -->
                    <div class="flex gap-1">
                        <span v-for="badge in driver.badges" :key="badge" class="text-lg" :title="badgeNames[badge]">{{ badge }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Achievements Section -->
        <div :class="['rounded-2xl border p-6 mt-6', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
            <h3 :class="['font-semibold mb-4', isDark ? 'text-white' : 'text-gray-900']">Dostępne osiągnięcia</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="ach in achievements" :key="ach.id" :class="['p-4 rounded-xl text-center transition-all', ach.unlocked ? 'bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 border border-emerald-500/30' : (isDark ? 'bg-white/5 opacity-50' : 'bg-gray-100 opacity-50')]">
                    <div class="text-4xl mb-2" :class="{ 'grayscale': !ach.unlocked }">{{ ach.icon }}</div>
                    <p :class="['font-semibold text-sm', isDark ? 'text-white' : 'text-gray-900']">{{ ach.name }}</p>
                    <p :class="['text-xs mt-1', isDark ? 'text-slate-400' : 'text-gray-500']">{{ ach.description }}</p>
                    <div v-if="!ach.unlocked" class="mt-2">
                        <div :class="['h-1.5 rounded-full overflow-hidden', isDark ? 'bg-slate-700' : 'bg-gray-200']">
                            <div class="h-full bg-emerald-500 rounded-full" :style="{ width: ach.progress + '%' }"></div>
                        </div>
                        <p :class="['text-xs mt-1', isDark ? 'text-slate-500' : 'text-gray-400']">{{ ach.progress }}%</p>
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

const topDrivers = ref([
    { id: 1, name: 'Tomasz Nowak', points: 2450, avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face' },
    { id: 2, name: 'Anna Wiśniewska', points: 2280, avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&crop=face' },
    { id: 3, name: 'Marek Zieliński', points: 2150, avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face' },
]);

const allDrivers = ref([
    { id: 1, name: 'Tomasz Nowak', points: 2450, deliveries: 156, rating: 4.9, change: 120, badges: ['🔥', '⭐', '🏃'], avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face' },
    { id: 2, name: 'Anna Wiśniewska', points: 2280, deliveries: 142, rating: 4.8, change: 85, badges: ['⭐', '💎'], avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&crop=face' },
    { id: 3, name: 'Marek Zieliński', points: 2150, deliveries: 138, rating: 4.9, change: -15, badges: ['🎯', '⭐'], avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face' },
    { id: 4, name: 'Piotr Kowalczyk', points: 1980, deliveries: 125, rating: 4.7, change: 45, badges: ['🏃'], avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face' },
    { id: 5, name: 'Karol Mazur', points: 1820, deliveries: 112, rating: 4.6, change: 30, badges: [], avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&h=200&fit=crop&crop=face' },
]);

const badgeNames = {
    '🔥': 'Hot Streak - 10 dostaw z rzędu',
    '⭐': 'Gwiazda - ocena powyżej 4.8',
    '🏃': 'Speed Demon - najszybsze dostawy',
    '💎': 'Diament - 1000+ dostaw',
    '🎯': 'Precyzja - 100% punktualność',
};

const achievements = ref([
    { id: 1, name: 'Pierwsza dostawa', description: 'Ukończ pierwszą dostawę', icon: '📦', unlocked: true, progress: 100 },
    { id: 2, name: 'Speed Demon', description: '50 dostaw przed czasem', icon: '⚡', unlocked: true, progress: 100 },
    { id: 3, name: 'Perfect Week', description: '7 dni bez opóźnień', icon: '🎯', unlocked: false, progress: 71 },
    { id: 4, name: 'Road Master', description: 'Przejedź 100,000 km', icon: '🛣️', unlocked: false, progress: 45 },
    { id: 5, name: 'Customer Favorite', description: 'Zdobądź 100 ocen 5★', icon: '⭐', unlocked: true, progress: 100 },
    { id: 6, name: 'Night Owl', description: '50 dostaw nocnych', icon: '🦉', unlocked: false, progress: 24 },
    { id: 7, name: 'Marathon', description: '1000 dostaw łącznie', icon: '🏆', unlocked: false, progress: 62 },
    { id: 8, name: 'Eco Driver', description: 'Oszczędź 500L paliwa', icon: '🌿', unlocked: false, progress: 38 },
]);
</script>

// update 109 

// update 178 

// update 221 

// update 268 

// update 323 

// u170

// op0skom
// pmpj8zhb