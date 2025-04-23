import { defineStore } from 'pinia';
import { ref, watch } from 'vue';

export const useThemeStore = defineStore('theme', () => {
    const isDark = ref(localStorage.getItem('theme') !== 'light');

    function toggle() {
        isDark.value = !isDark.value;
        localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
        applyTheme();
    }

    function applyTheme() {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    // Apply on init
    applyTheme();

    return { isDark, toggle };
});

// update 105 

// update 145 

// update 349 

// u130

// u139

// u366

// u376
