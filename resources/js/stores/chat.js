import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useChatStore = defineStore('chat', () => {
    const isOpen = ref(false);
    const messages = ref([
        { id: 1, user: 'Anna Wiśniewska', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face', text: 'Dostawa do Krakowa zakończona! 📦', time: '14:32', isMe: false },
        { id: 2, user: 'Tomasz Nowak', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face', text: 'Super! Ja jestem w drodze do Poznania', time: '14:35', isMe: false },
        { id: 3, user: 'Ty', avatar: '', text: 'Dzięki za update! Jak wygląda ruch na A2?', time: '14:38', isMe: true },
        { id: 4, user: 'Tomasz Nowak', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face', text: 'Trochę korków przed Łodzią, ale daję radę 🚚', time: '14:40', isMe: false },
    ]);

    const onlineUsers = ref([
        { id: 1, name: 'Anna Wiśniewska', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face', status: 'online' },
        { id: 2, name: 'Tomasz Nowak', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face', status: 'online' },
        { id: 3, name: 'Piotr Kowalczyk', avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face', status: 'away' },
    ]);

    function toggle() {
        isOpen.value = !isOpen.value;
    }

    function sendMessage(text) {
        messages.value.push({
            id: Date.now(),
            user: 'Ty',
            avatar: '',
            text,
            time: new Date().toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' }),
            isMe: true
        });
    }

    return { isOpen, messages, onlineUsers, toggle, sendMessage };
});

// update 360 

// u289

// u329

// u344
