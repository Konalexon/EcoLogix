import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/services/api';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const loading = ref(true);
    const token = ref(localStorage.getItem('auth_token'));

    const isAuthenticated = computed(() => !!user.value);
    const isAdmin = computed(() => user.value?.is_admin ?? false);
    const isDriver = computed(() => user.value?.is_driver ?? false);
    const isDispatcher = computed(() => user.value?.is_dispatcher ?? false);
    const companyId = computed(() => user.value?.company_id);

    async function checkAuth() {
        if (!token.value) {
            loading.value = false;
            return;
        }

        try {
            const response = await api.get('/user');
            user.value = response.data;
        } catch (error) {
            logout();
        } finally {
            loading.value = false;
        }
    }

    async function login(email, password) {
        const response = await api.post('/login', { email, password });
        token.value = response.data.token;
        user.value = response.data.user;
        localStorage.setItem('auth_token', token.value);
    }

    function logout() {
        user.value = null;
        token.value = null;
        localStorage.removeItem('auth_token');
    }

    return {
        user,
        loading,
        token,
        isAuthenticated,
        isAdmin,
        isDriver,
        isDispatcher,
        companyId,
        checkAuth,
        login,
        logout,
    };
});

// update 152 

// update 181 

// u138

// cazmvwt