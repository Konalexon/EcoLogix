<template>
    <Teleport to="body">
        <div class="fixed bottom-4 right-4 z-50 space-y-2">
            <TransitionGroup name="toast">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="['toast', `toast-${toast.type}`]"
                >
                    <component :is="getIcon(toast.type)" class="w-5 h-5" />
                    <span>{{ toast.message }}</span>
                    <button @click="removeToast(toast.id)" class="ml-2 opacity-70 hover:opacity-100">
                        <XMarkIcon class="w-4 h-4" />
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import {
    CheckCircleIcon,
    ExclamationCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

const toasts = ref([]);
let toastId = 0;

function addToast(message, type = 'info', duration = 5000) {
    const id = ++toastId;
    toasts.value.push({ id, message, type });
    
    if (duration > 0) {
        setTimeout(() => removeToast(id), duration);
    }
}

function removeToast(id) {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index !== -1) {
        toasts.value.splice(index, 1);
    }
}

function getIcon(type) {
    const icons = {
        success: CheckCircleIcon,
        error: ExclamationCircleIcon,
        warning: ExclamationTriangleIcon,
        info: InformationCircleIcon,
    };
    return icons[type] || InformationCircleIcon;
}

// Expose globally
window.$toast = {
    success: (msg) => addToast(msg, 'success'),
    error: (msg) => addToast(msg, 'error'),
    warning: (msg) => addToast(msg, 'warning'),
    info: (msg) => addToast(msg, 'info'),
};
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
</style>

// update 248 

// update 267 

// update 331 

// update 342 

// u78

// u202
