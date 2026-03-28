<template>
    <div class="min-h-screen bg-[#0f1117] text-slate-200">
        <nav class="flex gap-6 px-6 pt-3 bg-[#1d1f27] border-b border-slate-800 items-center">
            <router-link to="/" class="flex items-center gap-2 pb-2 text-sm border-b-2 transition-all"
                :class="$route.path === '/' ? 'border-blue-500 text-blue-500' : 'border-transparent text-slate-400 hover:text-white'">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Main Table
            </router-link>

            <router-link to="/kanban" class="flex items-center gap-2 pb-2 text-sm border-b-2 transition-all"
                :class="$route.path === '/kanban' ? 'border-blue-500 text-blue-500' : 'border-transparent text-slate-400 hover:text-white'">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                    <path d="M7 7h10" />
                    <path d="M7 12h10" />
                    <path d="M7 17h10" />
                </svg>
                Kanban
            </router-link>

            <button class="mb-2 text-slate-500 hover:text-white cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
            </button>
        </nav>

        

        <main class="p-4">
            <router-view />
        </main>
    </div>
</template>

<script setup lang="ts">


</script>