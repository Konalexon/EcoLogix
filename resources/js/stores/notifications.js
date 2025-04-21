import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useNotificationStore = defineStore('notifications', () => {
    const notifications = ref([
        { id: 1, type: 'success', title: 'Dostawa zakończona', message: 'Przesyłka #ECO24X8KL9 została dostarczona', time: 'Teraz', read: false },
        { id: 2, type: 'info', title: 'Nowe zamówienie', message: 'Otrzymano zamówienie Express z Warszawy', time: '5 min', read: false },
        { id: 3, type: 'warning', title: 'Opóźnienie', message: 'Trasa KR-004 opóźniona o 15 min', time: '12 min', read: false },
        { id: 4, type: 'success', title: 'Kierowca online', message: 'Tomasz Nowak rozpoczął zmianę', time: '30 min', read: true },
    ]);

    const unreadCount = () => notifications.value.filter(n => !n.read).length;

    function markAsRead(id) {
        const notif = notifications.value.find(n => n.id === id);
        if (notif) notif.read = true;
    }

    function markAllAsRead() {
        notifications.value.forEach(n => n.read = true);
    }

    function addNotification(notif) {
        notifications.value.unshift({
            id: Date.now(),
            time: 'Teraz',
            read: false,
            ...notif
        });
    }

    return { notifications, unreadCount, markAsRead, markAllAsRead, addNotification };
});

// update 198 

// u102

// u284

// u285
