<template>
    <!-- Onboarding Modal -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                <div class="absolute inset-0 bg-black/80"></div>
                
                <div :class="['relative w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden', isDark ? 'bg-slate-800' : 'bg-white']">
                    <!-- Progress -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-slate-700">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-500 transition-all duration-500" :style="{ width: ((currentStep + 1) / steps.length * 100) + '%' }"></div>
                    </div>

                    <!-- Content -->
                    <div class="p-8">
                        <Transition name="slide" mode="out-in">
                            <div :key="currentStep" class="text-center">
                                <div class="text-6xl mb-6">{{ steps[currentStep].icon }}</div>
                                <h2 :class="['text-2xl font-bold mb-4', isDark ? 'text-white' : 'text-gray-900']">{{ steps[currentStep].title }}</h2>
                                <p :class="['text-lg mb-8', isDark ? 'text-slate-400' : 'text-gray-600']">{{ steps[currentStep].description }}</p>
                                
                                <!-- Step specific content -->
                                <div v-if="steps[currentStep].features" class="grid grid-cols-2 gap-4 mb-8 text-left">
                                    <div v-for="feature in steps[currentStep].features" :key="feature.name" :class="['p-4 rounded-xl', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                        <span class="text-2xl block mb-2">{{ feature.icon }}</span>
                                        <p :class="['font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ feature.name }}</p>
                                        <p :class="['text-sm', isDark ? 'text-slate-400' : 'text-gray-500']">{{ feature.desc }}</p>
                                    </div>
                                </div>

                                <!-- Keyboard shortcuts -->
                                <div v-if="steps[currentStep].shortcuts" class="space-y-3 mb-8">
                                    <div v-for="shortcut in steps[currentStep].shortcuts" :key="shortcut.keys" :class="['flex items-center justify-between p-3 rounded-xl', isDark ? 'bg-white/5' : 'bg-gray-50']">
                                        <span :class="isDark ? 'text-white' : 'text-gray-900'">{{ shortcut.action }}</span>
                                        <kbd :class="['px-3 py-1.5 rounded-lg font-mono text-sm', isDark ? 'bg-slate-700 text-white' : 'bg-gray-200 text-gray-700']">{{ shortcut.keys }}</kbd>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Navigation -->
                    <div :class="['flex items-center justify-between p-6 border-t', isDark ? 'border-white/10' : 'border-gray-200']">
                        <button v-if="currentStep > 0" @click="prevStep" :class="['px-6 py-2.5 rounded-xl font-medium transition-colors', isDark ? 'bg-white/5 text-white hover:bg-white/10' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']">
                            Wstecz
                        </button>
                        <div v-else></div>

                        <!-- Dots -->
                        <div class="flex items-center gap-2">
                            <button v-for="(_, i) in steps" :key="i" @click="currentStep = i" :class="['w-2 h-2 rounded-full transition-all', currentStep === i ? 'w-6 bg-emerald-500' : (isDark ? 'bg-white/20 hover:bg-white/40' : 'bg-gray-300 hover:bg-gray-400')]"></button>
                        </div>

                        <button v-if="currentStep < steps.length - 1" @click="nextStep" class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium hover:from-emerald-400 hover:to-emerald-500 transition-all">
                            Dalej
                        </button>
                        <button v-else @click="complete" class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium hover:from-emerald-400 hover:to-emerald-500 transition-all">
                            Rozpocznij! 🚀
                        </button>
                    </div>

                    <!-- Skip -->
                    <button @click="complete" :class="['absolute top-6 right-6 text-sm', isDark ? 'text-slate-500 hover:text-white' : 'text-gray-400 hover:text-gray-600']">
                        Pomiń
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useThemeStore } from '@/stores/theme';

const themeStore = useThemeStore();
const isDark = computed(() => themeStore.isDark);

const isOpen = ref(false);
const currentStep = ref(0);

const steps = [
    {
        icon: '🚀',
        title: 'Witaj w EcoLogix!',
        description: 'Nowoczesny system zarządzania flotą logistyczną. Pozwól, że pokażę Ci najważniejsze funkcje.',
    },
    {
        icon: '📊',
        title: 'Panel główny',
        description: 'Dashboard pokazuje wszystkie kluczowe statystyki w jednym miejscu.',
        features: [
            { icon: '📦', name: 'Dostawy', desc: 'Live tracking przesyłek' },
            { icon: '🚚', name: 'Flota', desc: 'Pozycje pojazdów w czasie rzeczywistym' },
            { icon: '📈', name: 'Statystyki', desc: 'Wydajność i przychody' },
            { icon: '🔔', name: 'Aktywność', desc: 'Feed na żywo' },
        ]
    },
    {
        icon: '⌨️',
        title: 'Skróty klawiszowe',
        description: 'Pracuj szybciej dzięki skrótom klawiszowym.',
        shortcuts: [
            { keys: 'Ctrl + K', action: 'Otwórz Command Palette' },
            { keys: 'Ctrl + D', action: 'Zmień motyw' },
            { keys: 'Ctrl + N', action: 'Nowa przesyłka' },
        ]
    },
    {
        icon: '🏆',
        title: 'Ranking i osiągnięcia',
        description: 'Zdobywaj punkty i odznaki. Rywalizuj z innymi kierowcami!',
        features: [
            { icon: '⭐', name: 'Punkty', desc: 'Za każdą dostawę' },
            { icon: '🎯', name: 'Odznaki', desc: 'Za osiągnięcia' },
            { icon: '🥇', name: 'Ranking', desc: 'Miesięczny leaderboard' },
            { icon: '🎁', name: 'Nagrody', desc: 'Dla najlepszych' },
        ]
    },
    {
        icon: '✨',
        title: 'Gotowe!',
        description: 'Wszystko ustawione. Kliknij przycisk poniżej, aby rozpocząć pracę z EcoLogix!',
    }
];

function nextStep() {
    if (currentStep.value < steps.length - 1) currentStep.value++;
}

function prevStep() {
    if (currentStep.value > 0) currentStep.value--;
}

function complete() {
    isOpen.value = false;
    localStorage.setItem('onboarding-completed', 'true');
}

function show() {
    isOpen.value = true;
    currentStep.value = 0;
}

onMounted(() => {
    if (!localStorage.getItem('onboarding-completed')) {
        setTimeout(() => show(), 500);
    }
});

defineExpose({ show, isOpen });
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-active, .slide-leave-active { transition: all 0.3s ease; }
.slide-enter-from { opacity: 0; transform: translateX(20px); }
.slide-leave-to { opacity: 0; transform: translateX(-20px); }
</style>

// update 225 

// update 293 

// update 297 

// update 338 

// update 400 

// update 409 

// u287

// u415

// trz86ktn