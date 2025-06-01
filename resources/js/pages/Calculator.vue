<template>
    <div class="p-4 lg:p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 :class="['text-xl lg:text-2xl font-bold', isDark ? 'text-white' : 'text-gray-900']">Kalkulator kosztów</h1>
                <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">Oblicz koszt dostawy</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Calculator -->
            <div :class="['rounded-2xl border p-6', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                <h2 :class="['font-semibold mb-6', isDark ? 'text-white' : 'text-gray-900']">Dane przesyłki</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Miasto nadania</label>
                            <select v-model="form.from" :class="inputClass">
                                <option v-for="city in cities" :key="city">{{ city }}</option>
                            </select>
                        </div>
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Miasto dostawy</label>
                            <select v-model="form.to" :class="inputClass">
                                <option v-for="city in cities" :key="city">{{ city }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Waga (kg)</label>
                            <input v-model="form.weight" type="number" :class="inputClass" placeholder="0" />
                        </div>
                        <div>
                            <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Objętość (m³)</label>
                            <input v-model="form.volume" type="number" step="0.1" :class="inputClass" placeholder="0" />
                        </div>
                    </div>

                    <div>
                        <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Priorytet dostawy</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button v-for="p in priorities" :key="p.id" @click="form.priority = p.id" :class="['p-4 rounded-xl border-2 text-center transition-all', form.priority === p.id ? 'border-emerald-500 bg-emerald-500/10' : (isDark ? 'border-white/10 hover:border-white/20' : 'border-gray-200 hover:border-gray-300')]">
                                <span class="text-2xl block mb-1">{{ p.icon }}</span>
                                <p :class="['font-medium text-sm', isDark ? 'text-white' : 'text-gray-900']">{{ p.name }}</p>
                                <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">{{ p.time }}</p>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label :class="['block text-sm font-medium mb-2', isDark ? 'text-slate-300' : 'text-gray-700']">Dodatkowe usługi</label>
                        <div class="space-y-2">
                            <label v-for="service in services" :key="service.id" :class="['flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-colors', form.services.includes(service.id) ? 'bg-emerald-500/10' : (isDark ? 'bg-white/5 hover:bg-white/10' : 'bg-gray-50 hover:bg-gray-100')]">
                                <input type="checkbox" :value="service.id" v-model="form.services" class="w-4 h-4 text-emerald-500 rounded" />
                                <span class="text-xl">{{ service.icon }}</span>
                                <div class="flex-1">
                                    <p :class="['font-medium text-sm', isDark ? 'text-white' : 'text-gray-900']">{{ service.name }}</p>
                                </div>
                                <span :class="['font-medium', isDark ? 'text-slate-400' : 'text-gray-600']">+{{ service.price }} PLN</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result -->
            <div class="space-y-6">
                <div :class="['rounded-2xl border p-6', isDark ? 'bg-gradient-to-br from-emerald-500/20 to-teal-500/10 border-emerald-500/30' : 'bg-gradient-to-br from-emerald-50 to-teal-50 border-emerald-200']">
                    <h2 :class="['font-semibold mb-4', isDark ? 'text-white' : 'text-gray-900']">Wycena</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span :class="isDark ? 'text-slate-400' : 'text-gray-600'">Dystans</span>
                            <span :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ distance }} km</span>
                        </div>
                        <div class="flex justify-between">
                            <span :class="isDark ? 'text-slate-400' : 'text-gray-600'">Koszt bazowy</span>
                            <span :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ baseCost.toFixed(2) }} PLN</span>
                        </div>
                        <div class="flex justify-between">
                            <span :class="isDark ? 'text-slate-400' : 'text-gray-600'">Waga ({{ form.weight || 0 }} kg)</span>
                            <span :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ weightCost.toFixed(2) }} PLN</span>
                        </div>
                        <div class="flex justify-between">
                            <span :class="isDark ? 'text-slate-400' : 'text-gray-600'">Priorytet ({{ priorityName }})</span>
                            <span :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">×{{ priorityMultiplier }}</span>
                        </div>
                        <div v-if="servicesCost > 0" class="flex justify-between">
                            <span :class="isDark ? 'text-slate-400' : 'text-gray-600'">Usługi dodatkowe</span>
                            <span :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ servicesCost.toFixed(2) }} PLN</span>
                        </div>
                    </div>

                    <div :class="['border-t pt-4', isDark ? 'border-white/10' : 'border-emerald-200']">
                        <div class="flex justify-between items-center">
                            <span :class="['text-lg font-semibold', isDark ? 'text-white' : 'text-gray-900']">Razem</span>
                            <span class="text-3xl font-bold text-emerald-500">{{ totalCost.toFixed(2) }} PLN</span>
                        </div>
                        <p :class="['text-sm mt-1', isDark ? 'text-slate-400' : 'text-gray-500']">Cena zawiera VAT 23%</p>
                    </div>
                </div>

                <button class="w-full py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-2xl font-semibold text-lg shadow-lg shadow-emerald-500/30 hover:from-emerald-400 hover:to-emerald-500 transition-all hover:scale-[1.02]">
                    Zamów przesyłkę za {{ totalCost.toFixed(2) }} PLN
                </button>

                <!-- ETA -->
                <div :class="['rounded-2xl border p-6', isDark ? 'bg-slate-800/50 border-white/5' : 'bg-white border-gray-200']">
                    <h3 :class="['font-semibold mb-4', isDark ? 'text-white' : 'text-gray-900']">Szacowany czas dostawy</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                            <span class="text-3xl">🚚</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-emerald-400">{{ etaDate }}</p>
                            <p :class="isDark ? 'text-slate-400' : 'text-gray-500'">{{ etaTime }}</p>
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

const inputClass = computed(() => isDark.value 
    ? 'w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-emerald-500/50'
    : 'w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:border-emerald-500'
);

const cities = ['Warszawa', 'Kraków', 'Wrocław', 'Poznań', 'Gdańsk', 'Łódź', 'Katowice', 'Lublin', 'Szczecin'];

const priorities = [
    { id: 'economy', name: 'Economy', time: '3-5 dni', icon: '📦', multiplier: 1 },
    { id: 'standard', name: 'Standard', time: '1-2 dni', icon: '🚚', multiplier: 1.5 },
    { id: 'express', name: 'Express', time: 'Dziś/Jutro', icon: '⚡', multiplier: 2.5 },
];

const services = [
    { id: 'insurance', name: 'Ubezpieczenie', price: 50, icon: '🛡️' },
    { id: 'fragile', name: 'Delikatne', price: 30, icon: '🥚' },
    { id: 'signature', name: 'Podpis odbiorcy', price: 20, icon: '✍️' },
    { id: 'sms', name: 'SMS powiadomienia', price: 5, icon: '📱' },
];

const form = ref({
    from: 'Warszawa',
    to: 'Kraków',
    weight: 10,
    volume: 0.5,
    priority: 'standard',
    services: ['sms']
});

const distances = {
    'Warszawa-Kraków': 295,
    'Warszawa-Wrocław': 350,
    'Warszawa-Poznań': 310,
    'Warszawa-Gdańsk': 340,
    'Kraków-Wrocław': 270,
    'Kraków-Poznań': 450,
    'Gdańsk-Poznań': 310,
};

const distance = computed(() => {
    const key1 = `${form.value.from}-${form.value.to}`;
    const key2 = `${form.value.to}-${form.value.from}`;
    return distances[key1] || distances[key2] || 200;
});

const baseCost = computed(() => distance.value * 0.5);
const weightCost = computed(() => (parseFloat(form.value.weight) || 0) * 2);
const priorityMultiplier = computed(() => priorities.find(p => p.id === form.value.priority)?.multiplier || 1);
const priorityName = computed(() => priorities.find(p => p.id === form.value.priority)?.name || '');
const servicesCost = computed(() => form.value.services.reduce((sum, id) => sum + (services.find(s => s.id === id)?.price || 0), 0));
const totalCost = computed(() => (baseCost.value + weightCost.value) * priorityMultiplier.value + servicesCost.value);

const etaDate = computed(() => {
    const days = form.value.priority === 'express' ? 1 : form.value.priority === 'standard' ? 2 : 4;
    const date = new Date();
    date.setDate(date.getDate() + days);
    return date.toLocaleDateString('pl-PL', { weekday: 'long', day: 'numeric', month: 'long' });
});

const etaTime = computed(() => form.value.priority === 'express' ? 'do 18:00' : 'w godzinach 8:00-18:00');
</script>

// update 120 

// update 155 

// update 250 

// update 354 

// update 357 

// update 359 

// update 386 

// lyjtoy8h