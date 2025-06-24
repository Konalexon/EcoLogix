<template>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Background effects -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-8 shadow-2xl">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/30">
                        <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-400 to-blue-400 bg-clip-text text-transparent">EcoLogix</h1>
                    <p class="text-slate-400 mt-2">Zaloguj się do systemu</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleLogin" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                        <input
                            v-model="email"
                            type="email"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all"
                            placeholder="twoj@email.pl"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Hasło</label>
                        <input
                            v-model="password"
                            type="password"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all"
                            placeholder="••••••••"
                        />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" v-model="remember" class="w-4 h-4 rounded bg-white/5 border-white/20 text-emerald-500 focus:ring-emerald-500/50" />
                            <span class="text-sm text-slate-400">Zapamiętaj mnie</span>
                        </label>
                        <a href="#" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors">
                            Zapomniałeś hasła?
                        </a>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 px-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 transition-all duration-300 hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg v-if="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ loading ? 'Logowanie...' : 'Zaloguj się' }}</span>
                    </button>

                    <p v-if="error" class="text-red-400 text-sm text-center">{{ error }}</p>
                </form>

                <!-- Demo info -->
                <div class="mt-6 pt-6 border-t border-white/10 text-center">
                    <p class="text-slate-500 text-sm">
                        Demo: <span class="text-slate-300">admin@ecologix.pl</span> / <span class="text-slate-300">password</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-slate-600 text-sm mt-6">
                © 2024 EcoLogix. Wszelkie prawa zastrzeżone.
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const email = ref('admin@ecologix.pl');
const password = ref('password');
const remember = ref(true);
const loading = ref(false);
const error = ref('');

async function handleLogin() {
    error.value = '';
    loading.value = true;

    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000));

    // Demo login - accept any credentials
    if (email.value && password.value) {
        // Store fake auth token
        localStorage.setItem('auth_token', 'demo_token_' + Date.now());
        localStorage.setItem('user', JSON.stringify({
            name: 'Admin Demo',
            email: email.value,
            role: 'admin'
        }));
        
        // Redirect to dashboard
        router.push('/');
    } else {
        error.value = 'Wprowadź email i hasło';
    }

    loading.value = false;
}
</script>

// update 179 

// update 248 

// update 320 

// update 380 

// update 401 

// update 412 

// u248

// u391

// p6cagkb