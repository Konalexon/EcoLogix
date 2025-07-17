<template>
    <div class="p-4 lg:p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 :class="['text-xl lg:text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">Kalendarz dostaw</h1>
                <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">{{ currentMonthName }} {{ currentYear }}</p>
            </div>
            <div class="flex gap-2">
                <button @click="prevMonth" :class="['p-2 rounded-xl transition-colors', isDark ? 'bg-white/5 hover:bg-white/10 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700']">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button @click="nextMonth" :class="['p-2 rounded-xl transition-colors', isDark ? 'bg-white/5 hover:bg-white/10 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700']">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <button @click="goToToday" class="px-4 py-2 bg-emerald-500/20 text-emerald-400 rounded-xl font-medium hover:bg-emerald-500/30 transition-colors">
                    Dziś
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Calendar -->
            <div :class="['lg:col-span-3 rounded-2xl border overflow-hidden', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                <!-- Days header -->
                <div class="grid grid-cols-7 border-b" :class="isDark ? 'border-white/10' : 'border-gray-200'">
                    <div v-for="day in ['Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob', 'Nd']" :key="day" :class="['p-3 text-center text-sm font-medium', isDark ? 'text-slate-400' : 'text-gray-500']">
                        {{ day }}
                    </div>
                </div>

                <!-- Calendar grid -->
                <div class="grid grid-cols-7">
                    <div v-for="(day, index) in calendarDays" :key="index" @click="selectDate(day)" :class="[
                        'min-h-[100px] p-2 border-b border-r cursor-pointer transition-colors',
                        isDark ? 'border-white/5 hover:bg-white/5' : 'border-gray-100 hover:bg-gray-50',
                        !day.currentMonth ? 'opacity-30' : '',
                        day.isToday ? (isDark ? 'bg-emerald-500/10' : 'bg-emerald-50') : '',
                        selectedDate === day.date ? 'ring-2 ring-emerald-500 ring-inset' : ''
                    ]">
                        <div class="flex items-center justify-between mb-1">
                            <span :class="['text-sm font-medium', day.isToday ? 'w-7 h-7 bg-emerald-500 text-white rounded-full flex items-center justify-center' : (isDark ? 'text-white' : 'text-gray-900')]">
                                {{ day.day }}
                            </span>
                            <span v-if="getDeliveries(day.date).length" class="text-xs text-emerald-400 font-medium">{{ getDeliveries(day.date).length }}</span>
                        </div>
                        <div class="space-y-1">
                            <div v-for="delivery in getDeliveries(day.date).slice(0, 2)" :key="delivery.id" :class="['text-xs p-1 rounded truncate', statusBg(delivery.status)]">
                                {{ delivery.to }}
                            </div>
                            <div v-if="getDeliveries(day.date).length > 2" :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">
                                +{{ getDeliveries(day.date).length - 2 }} więcej
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deliveries for selected day -->
            <div :class="['rounded-2xl border p-4', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                <h3 :class="['font-semibold mb-4', isDark ? 'text-white' : 'text-gray-900']">
                    {{ selectedDateFormatted }}
                </h3>

                <div v-if="selectedDeliveries.length === 0" class="text-center py-8">
                    <span class="text-4xl mb-4 block">📅</span>
                    <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Brak zaplanowanych dostaw</p>
                </div>

                <div v-else class="space-y-3">
                    <div v-for="delivery in selectedDeliveries" :key="delivery.id" :class="['p-3 rounded-xl', isDark ? 'bg-white/5' : 'bg-gray-50']">
                        <div class="flex items-center justify-between mb-2">
                            <span :class="['px-2 py-0.5 rounded text-xs font-medium', statusClass(delivery.status)]">{{ delivery.statusLabel }}</span>
                            <span :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">{{ delivery.time }}</span>
                        </div>
                        <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ delivery.number }}</p>
                        <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ delivery.from }} → {{ delivery.to }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <img :src="delivery.driverAvatar" class="w-6 h-6 rounded-full object-cover" />
                            <span :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ delivery.driver }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick stats -->
                <div class="mt-6 pt-4 border-t" :class="isDark ? 'border-white/10' : 'border-gray-200'">
                    <h4 :class="['text-sm font-medium mb-3', isDark ? 'text-slate-400' : 'text-gray-500']">Podsumowanie miesiąca</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div :class="['p-3 rounded-xl text-center', isDark ? 'bg-emerald-500/10' : 'bg-emerald-50']">
                            <p class="text-xl font-bold text-emerald-400">{{ monthStats.total }}</p>
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Dostaw</p>
                        </div>
                        <div :class="['p-3 rounded-xl text-center', isDark ? 'bg-blue-500/10' : 'bg-blue-50']">
                            <p class="text-xl font-bold text-blue-400">{{ monthStats.pending }}</p>
                            <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Oczekujących</p>
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

const currentDate = ref(new Date());
const selectedDate = ref(new Date().toISOString().split('T')[0]);

const currentYear = computed(() => currentDate.value.getFullYear());
const currentMonth = computed(() => currentDate.value.getMonth());
const currentMonthName = computed(() => currentDate.value.toLocaleString('pl-PL', { month: 'long' }));

const deliveries = ref([
    { id: 1, number: 'ECO24X8KL9', from: 'Warszawa', to: 'Kraków', date: getTodayString(), time: '14:30', status: 'transit', statusLabel: 'W trasie', driver: 'Tomasz Nowak', driverAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face' },
    { id: 2, number: 'ECO24Y2ML3', from: 'Gdańsk', to: 'Poznań', date: getTodayString(), time: '16:00', status: 'pending', statusLabel: 'Oczekuje', driver: 'Anna Wiśniewska', driverAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face' },
    { id: 3, number: 'ECO24Z5NP7', from: 'Wrocław', to: 'Łódź', date: getTomorrowString(), time: '10:00', status: 'pending', statusLabel: 'Oczekuje', driver: 'Marek Zieliński', driverAvatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face' },
    { id: 4, number: 'ECO24A1QR2', from: 'Poznań', to: 'Szczecin', date: getTomorrowString(), time: '12:00', status: 'pending', statusLabel: 'Oczekuje', driver: 'Piotr Kowalczyk', driverAvatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face' },
    { id: 5, number: 'ECO24B3ST4', from: 'Kraków', to: 'Katowice', date: getDateString(2), time: '09:00', status: 'pending', statusLabel: 'Oczekuje', driver: 'Tomasz Nowak', driverAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face' },
]);

function getTodayString() {
    return new Date().toISOString().split('T')[0];
}

function getTomorrowString() {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return d.toISOString().split('T')[0];
}

function getDateString(daysFromNow) {
    const d = new Date();
    d.setDate(d.getDate() + daysFromNow);
    return d.toISOString().split('T')[0];
}

const calendarDays = computed(() => {
    const days = [];
    const firstDay = new Date(currentYear.value, currentMonth.value, 1);
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);
    
    // Get Monday as first day
    let startDay = firstDay.getDay() - 1;
    if (startDay < 0) startDay = 6;
    
    // Previous month days
    for (let i = startDay - 1; i >= 0; i--) {
        const d = new Date(currentYear.value, currentMonth.value, -i);
        days.push({ day: d.getDate(), date: d.toISOString().split('T')[0], currentMonth: false, isToday: false });
    }
    
    // Current month days
    const today = new Date().toISOString().split('T')[0];
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const d = new Date(currentYear.value, currentMonth.value, i);
        const dateStr = d.toISOString().split('T')[0];
        days.push({ day: i, date: dateStr, currentMonth: true, isToday: dateStr === today });
    }
    
    // Next month days
    const remaining = 42 - days.length;
    for (let i = 1; i <= remaining; i++) {
        const d = new Date(currentYear.value, currentMonth.value + 1, i);
        days.push({ day: i, date: d.toISOString().split('T')[0], currentMonth: false, isToday: false });
    }
    
    return days;
});

const selectedDeliveries = computed(() => deliveries.value.filter(d => d.date === selectedDate.value));

const selectedDateFormatted = computed(() => {
    const d = new Date(selectedDate.value);
    return d.toLocaleDateString('pl-PL', { weekday: 'long', day: 'numeric', month: 'long' });
});

const monthStats = computed(() => ({
    total: deliveries.value.length,
    pending: deliveries.value.filter(d => d.status === 'pending').length
}));

function getDeliveries(date) {
    return deliveries.value.filter(d => d.date === date);
}

function selectDate(day) {
    selectedDate.value = day.date;
}

function prevMonth() {
    currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
}

function nextMonth() {
    currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
}

function goToToday() {
    currentDate.value = new Date();
    selectedDate.value = new Date().toISOString().split('T')[0];
}

function statusBg(status) {
    return { 'transit': 'bg-blue-500/20 text-blue-400', 'delivered': 'bg-emerald-500/20 text-emerald-400', 'pending': 'bg-amber-500/20 text-amber-400' }[status] || 'bg-slate-500/20 text-slate-400';
}

function statusClass(status) {
    return { 'transit': 'bg-blue-500/20 text-blue-400', 'delivered': 'bg-emerald-500/20 text-emerald-400', 'pending': 'bg-amber-500/20 text-amber-400' }[status] || 'bg-slate-500/20 text-slate-400';
}
</script>

// update 111 

// update 168 

// update 241 

// update 242 

// update 319 

// update 374 

// update 376 

// u92

// u112

// u311

// u321

// e2butw33