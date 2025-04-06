<template>
    <div :class="['min-h-screen transition-colors duration-300', isDark ? 'bg-slate-900' : 'bg-gray-100']">
        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-xl bg-emerald-500 text-white shadow-lg">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Sidebar Overlay -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false" class="lg:hidden fixed inset-0 bg-black/50 z-40"></div>

        <!-- Sidebar -->
        <aside :class="[
            'fixed left-0 top-0 h-full w-72 z-40 transition-all duration-300 border-r',
            isDark ? 'bg-slate-800/90 backdrop-blur-xl border-white/5' : 'bg-white border-gray-200',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]">
            <!-- Logo -->
            <div :class="['p-6 border-b', isDark ? 'border-white/5' : 'border-gray-200']">
                <div class="flex items-center gap-3">
                    <div class="relative group cursor-pointer">
                        <!-- Animated logo container -->
                        <div class="w-14 h-14 relative">
                            <!-- Glow effect -->
                            <div class="absolute inset-0 bg-emerald-500 rounded-2xl blur-xl opacity-30 group-hover:opacity-50 transition-opacity"></div>
                            <!-- Main logo -->
                            <div class="relative w-14 h-14 bg-gradient-to-br from-emerald-400 via-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-105 transition-transform">
                                <!-- Leaf + Truck icon -->
                                <svg class="w-8 h-8 text-white" viewBox="0 0 32 32" fill="none">
                                    <!-- Leaf -->
                                    <path d="M8 8C8 8 16 6 20 14C24 22 16 26 16 26C16 26 20 18 16 14C12 10 8 8 8 8Z" fill="currentColor" opacity="0.3"/>
                                    <!-- Truck body -->
                                    <rect x="4" y="12" width="14" height="10" rx="1" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    <!-- Truck cabin -->
                                    <path d="M18 15H22L25 18V22H18V15Z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    <!-- Wheels -->
                                    <circle cx="8" cy="24" r="2" stroke="currentColor" stroke-width="1.5"/>
                                    <circle cx="22" cy="24" r="2" stroke="currentColor" stroke-width="1.5"/>
                                    <!-- Speed lines -->
                                    <path class="animate-pulse" d="M2 16H4M2 19H3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <!-- Corner accent -->
                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L11 6.414V15a1 1 0 11-2 0V6.414L7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h1 :class="['text-2xl font-extrabold tracking-tight', isDark ? 'text-white' : 'text-gray-900']">
                            <span class="bg-gradient-to-r from-emerald-400 to-teal-500 bg-clip-text text-transparent">Eco</span><span :class="isDark ? 'text-white' : 'text-gray-900'">Logix</span>
                        </h1>
                        <p :class="['text-[10px] uppercase tracking-[0.2em] font-medium', isDark ? 'text-slate-400' : 'text-gray-500']">Fleet Management Pro</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 pb-24 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 120px);">
                <RouterLink to="/" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    <span>Dashboard</span>
                </RouterLink>
                <RouterLink to="/fleet" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/fleet' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    <span>Mapa floty</span>
                </RouterLink>
                <RouterLink to="/shipments" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/shipments' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    <span>Przesyłki</span>
                    <span class="ml-auto bg-emerald-500/20 text-emerald-400 text-xs px-2 py-0.5 rounded-full font-medium">248</span>
                </RouterLink>
                <RouterLink to="/vehicles" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/vehicles' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>
                    <span>Pojazdy</span>
                </RouterLink>
                <RouterLink to="/drivers" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/drivers' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <span>Kierowcy</span>
                </RouterLink>
                <RouterLink to="/analytics" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/analytics' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span>Analityka</span>
                </RouterLink>

                <!-- Divider -->
                <div :class="['my-3 border-t', isDark ? 'border-white/5' : 'border-gray-200']"></div>
                <p :class="['text-xs uppercase tracking-wider px-4 py-2', isDark ? 'text-slate-500' : 'text-gray-400']">Narzędzia</p>

                <RouterLink to="/leaderboard" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/leaderboard' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <span class="text-lg">🏆</span>
                    <span>Ranking</span>
                </RouterLink>
                <RouterLink to="/calendar" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/calendar' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <span class="text-lg">📅</span>
                    <span>Kalendarz</span>
                </RouterLink>
                <RouterLink to="/calculator" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/calculator' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <span class="text-lg">💰</span>
                    <span>Kalkulator</span>
                </RouterLink>
                <RouterLink to="/settings" @click="sidebarOpen = false" :class="['nav-link group', route.path === '/settings' ? 'nav-link-active' : '', isDark ? '' : 'light']">
                    <span class="text-lg">⚙️</span>
                    <span>Ustawienia</span>
                </RouterLink>
            </nav>

            <!-- User -->
            <div :class="['absolute bottom-0 left-0 right-0 p-4 border-t', isDark ? 'border-white/5 bg-slate-800' : 'border-gray-200 bg-white']">
                <div :class="['flex items-center gap-3 p-3 rounded-xl transition-colors cursor-pointer', isDark ? 'bg-white/5 hover:bg-white/10' : 'bg-gray-100 hover:bg-gray-200']">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face" class="w-10 h-10 rounded-xl object-cover" />
                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2" :class="isDark ? 'border-slate-800' : 'border-white'"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p :class="['text-sm font-medium truncate', isDark ? 'text-white' : 'text-gray-900']">Jan Kowalski</p>
                        <p :class="['text-xs', isDark ? 'text-slate-400' : 'text-gray-500']">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main :class="['lg:ml-72 min-h-screen transition-all duration-300', isDark ? '' : 'bg-gray-50']">
            <!-- Top Bar -->
            <header :class="['sticky top-0 z-30 px-4 lg:px-6 py-4 border-b backdrop-blur-xl', isDark ? 'bg-slate-900/80 border-white/5' : 'bg-white/80 border-gray-200']">
                <div class="flex items-center justify-between">
                    <div class="lg:hidden w-10"></div>
                    
                    <!-- Search -->
                    <div class="hidden md:block flex-1 max-w-md">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5" :class="isDark ? 'text-slate-400' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <input type="text" placeholder="Szukaj przesyłek, pojazdów..." :class="['w-full pl-10 pr-4 py-2 rounded-xl border transition-all', isDark ? 'bg-white/5 border-white/10 text-white placeholder-slate-500 focus:border-emerald-500/50' : 'bg-gray-100 border-transparent text-gray-900 placeholder-gray-500 focus:border-emerald-500']" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Theme Toggle -->
                        <button @click="themeStore.toggle()" :class="['p-2.5 rounded-xl transition-all', isDark ? 'bg-white/5 text-slate-400 hover:text-white hover:bg-white/10' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']" title="Zmień motyw">
                            <svg v-if="isDark" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        </button>

                        <!-- Notifications -->
                        <div class="relative">
                            <button @click="showNotifications = !showNotifications" :class="['p-2.5 rounded-xl transition-all relative', isDark ? 'bg-white/5 text-slate-400 hover:text-white hover:bg-white/10' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                <span v-if="notificationStore.unreadCount() > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center animate-bounce">{{ notificationStore.unreadCount() }}</span>
                            </button>

                            <!-- Notifications Dropdown -->
                            <Transition name="dropdown">
                                <div v-if="showNotifications" :class="['absolute right-0 top-12 w-80 rounded-2xl border shadow-2xl overflow-hidden', isDark ? 'bg-slate-800 border-white/10' : 'bg-white border-gray-200']">
                                    <div :class="['p-4 border-b flex items-center justify-between', isDark ? 'border-white/10' : 'border-gray-200']">
                                        <h3 :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">Powiadomienia</h3>
                                        <button @click="notificationStore.markAllAsRead()" class="text-xs text-emerald-500 hover:text-emerald-400">Oznacz jako przeczytane</button>
                                    </div>
                                    <div class="max-h-80 overflow-y-auto">
                                        <div v-for="n in notificationStore.notifications" :key="n.id" @click="notificationStore.markAsRead(n.id)" :class="['p-4 border-b cursor-pointer transition-colors', isDark ? 'border-white/5 hover:bg-white/5' : 'border-gray-100 hover:bg-gray-50', !n.read ? (isDark ? 'bg-emerald-500/5' : 'bg-emerald-50') : '']">
                                            <div class="flex gap-3">
                                                <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0', notifColor(n.type)]">
                                                    {{ notifIcon(n.type) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p :class="['text-sm font-medium', isDark ? 'text-white' : 'text-gray-900']">{{ n.title }}</p>
                                                    <p :class="['text-xs truncate', isDark ? 'text-slate-400' : 'text-gray-500']">{{ n.message }}</p>
                                                    <p :class="['text-xs mt-1', isDark ? 'text-slate-500' : 'text-gray-400']">{{ n.time }}</p>
                                                </div>
                                                <div v-if="!n.read" class="w-2 h-2 bg-emerald-500 rounded-full shrink-0 mt-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Chat Toggle -->
                        <button @click="chatStore.toggle()" :class="['p-2.5 rounded-xl transition-all relative', isDark ? 'bg-white/5 text-slate-400 hover:text-white hover:bg-white/10' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </header>

            <RouterView v-slot="{ Component }">
                <Transition name="page" mode="out-in">
                    <component :is="Component" />
                </Transition>
            </RouterView>
        </main>

        <!-- Chat Panel -->
        <Transition name="slide">
            <div v-if="chatStore.isOpen" :class="['fixed right-0 top-0 h-full w-96 z-50 border-l shadow-2xl flex flex-col', isDark ? 'bg-slate-800 border-white/10' : 'bg-white border-gray-200']">
                <!-- Chat Header -->
                <div :class="['p-4 border-b flex items-center justify-between', isDark ? 'border-white/10' : 'border-gray-200']">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>
                        </div>
                        <div>
                            <h3 :class="['font-semibold', isDark ? 'text-white' : 'text-gray-900']">Chat zespołu</h3>
                            <p class="text-xs text-emerald-500">{{ chatStore.onlineUsers.filter(u => u.status === 'online').length }} online</p>
                        </div>
                    </div>
                    <button @click="chatStore.toggle()" :class="['p-2 rounded-lg transition-colors', isDark ? 'hover:bg-white/10 text-slate-400' : 'hover:bg-gray-100 text-gray-500']">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Online Users -->
                <div :class="['p-3 border-b flex gap-2 overflow-x-auto', isDark ? 'border-white/10' : 'border-gray-200']">
                    <div v-for="user in chatStore.onlineUsers" :key="user.id" class="flex flex-col items-center shrink-0">
                        <div class="relative">
                            <img :src="user.avatar" class="w-10 h-10 rounded-full object-cover" />
                            <div :class="['absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2', user.status === 'online' ? 'bg-emerald-400' : 'bg-amber-400', isDark ? 'border-slate-800' : 'border-white']"></div>
                        </div>
                        <p :class="['text-xs mt-1 truncate w-14 text-center', isDark ? 'text-slate-400' : 'text-gray-500']">{{ user.name.split(' ')[0] }}</p>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <div v-for="msg in chatStore.messages" :key="msg.id" :class="['flex gap-3', msg.isMe ? 'flex-row-reverse' : '']">
                        <img v-if="!msg.isMe" :src="msg.avatar" class="w-8 h-8 rounded-full object-cover shrink-0" />
                        <div :class="['max-w-[70%]', msg.isMe ? 'text-right' : '']">
                            <p v-if="!msg.isMe" :class="['text-xs mb-1', isDark ? 'text-slate-400' : 'text-gray-500']">{{ msg.user }}</p>
                            <div :class="['px-4 py-2 rounded-2xl inline-block', msg.isMe ? 'bg-emerald-500 text-white rounded-br-md' : (isDark ? 'bg-white/10 text-white rounded-bl-md' : 'bg-gray-100 text-gray-900 rounded-bl-md')]">
                                <p class="text-sm">{{ msg.text }}</p>
                            </div>
                            <p :class="['text-xs mt-1', isDark ? 'text-slate-500' : 'text-gray-400']">{{ msg.time }}</p>
                        </div>
                    </div>
                </div>

                <!-- Input -->
                <div :class="['p-4 border-t', isDark ? 'border-white/10' : 'border-gray-200']">
                    <div class="flex gap-2">
                        <input v-model="newMessage" @keyup.enter="sendMessage" type="text" placeholder="Napisz wiadomość..." :class="['flex-1 px-4 py-2 rounded-xl border transition-all', isDark ? 'bg-white/5 border-white/10 text-white placeholder-slate-500' : 'bg-gray-100 border-transparent text-gray-900 placeholder-gray-500']" />
                        <button @click="sendMessage" class="px-4 py-2 bg-emerald-500 text-white rounded-xl hover:bg-emerald-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Command Palette -->
        <CommandPalette ref="commandPalette" />

        <!-- Onboarding Tutorial -->
        <OnboardingTutorial ref="onboarding" />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useThemeStore } from '@/stores/theme';
import { useNotificationStore } from '@/stores/notifications';
import { useChatStore } from '@/stores/chat';
import CommandPalette from '@/components/CommandPalette.vue';
import OnboardingTutorial from '@/components/OnboardingTutorial.vue';

const route = useRoute();
const themeStore = useThemeStore();
const notificationStore = useNotificationStore();
const chatStore = useChatStore();

const isDark = computed(() => themeStore.isDark);
const sidebarOpen = ref(false);
const showNotifications = ref(false);
const newMessage = ref('');

function notifIcon(type) {
    return { success: '✅', info: '📦', warning: '⚠️', error: '❌' }[type] || '📌';
}

function notifColor(type) {
    return { success: 'bg-emerald-500/20', info: 'bg-blue-500/20', warning: 'bg-amber-500/20', error: 'bg-red-500/20' }[type] || 'bg-slate-500/20';
}

function sendMessage() {
    if (newMessage.value.trim()) {
        chatStore.sendMessage(newMessage.value);
        newMessage.value = '';
    }
}
</script>

<style>
.nav-link {
    @apply flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-white/5 hover:text-white transition-all font-medium;
}
.nav-link.light {
    @apply text-gray-600 hover:bg-gray-100 hover:text-gray-900;
}
.nav-link-active {
    @apply bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 !important;
}

/* Animations */
.page-enter-active, .page-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.page-enter-from { opacity: 0; transform: translateY(10px); }
.page-leave-to { opacity: 0; transform: translateY(-10px); }

.dropdown-enter-active, .dropdown-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.dropdown-enter-from, .dropdown-leave-to {
    opacity: 0; transform: translateY(-10px) scale(0.95);
}

.slide-enter-active, .slide-leave-active {
    transition: transform 0.3s ease;
}
.slide-enter-from, .slide-leave-to {
    transform: translateX(100%);
}
</style>

// update 99 

// update 106 

// update 121 

// update 141 

// update 178 

// update 254 

// update 325 

// update 374 

// u291

// u341
