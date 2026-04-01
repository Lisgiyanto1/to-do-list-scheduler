<script setup lang="ts">
import { useSortable, type UseSortableOptions } from '@vueuse/integrations/useSortable';
import { computed } from 'vue';
import { useTodoMutations, useTodosQuery } from '../todo/hooks/useTodosQuery';
import ToolBar from '../todo/pages/ToolBar.vue';

const { data: queryResponse, isLoading } = useTodosQuery();
const { updateMutation } = useTodoMutations();

const statuses = [
    { id: 'ready to start', label: 'Ready to start', color: 'bg-[#2563eb]' },
    { id: 'in_progress', label: 'In Progress', color: 'bg-[#f59e0b]' },
    { id: 'waiting for review', label: 'Waiting for review', color: 'bg-[#06b6d4]' },
    { id: 'done', label: 'Done', color: 'bg-[#10b981]' }
];

const getProfileUrl = (path: string | null | undefined) => {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';
    return `${baseUrl}/storage/${path}`;
};

const todos = computed(() => {
    const rootData = queryResponse.value;
    if (!rootData) return [];
    const items = rootData.data?.data || rootData.data || [];
    return Array.isArray(items) ? items : [];
});

const initColumn = (el: HTMLElement | null, status: string) => {
    if (!el) return;
    useSortable(el, [] as any[], {
        group: 'kanban-tasks',
        animation: 250,
        ghostClass: 'opacity-20',
        onEnd: (evt: any) => {
            const taskId = evt.item.getAttribute('data-id');
            const newStatus = evt.to.getAttribute('data-status');
            if (taskId && newStatus && newStatus !== evt.from.getAttribute('data-status')) {
                updateMutation.mutate({ id: taskId, data: { status: newStatus } });
            }
        },
    } as UseSortableOptions);
};

const getTasksByStatus = (status: string) => todos.value.filter((t: any) => t.status === status);

// Logic Warna Border Left Priority
const getPriorityBorderColor = (priority: string) => {
    switch (priority?.toLowerCase()) {
        case 'high': return 'border-l-[#818cf8]';
        case 'medium': return 'border-l-[#fbbf24]';
        default: return 'border-l-slate-500';
    }
};
</script>

<template>

    <div class=" min-h-screen bg-[#0f111a]">
        
        <ToolBar />
        <div class="w-full h-[1px] bg-[#2d3343]"></div>
        <div v-if="isLoading" class="flex justify-center items-center h-64 text-slate-500">Loading...</div>

        <div v-else class="flex overflow-x-auto gap-4 mt-8 pb-10 custom-scrollbar items-start">
            <!-- Column Container -->
            <div v-for="status in statuses" :key="status.id"
                class="flex-shrink-0 w-[300px] bg-[#2d3343] rounded-xl overflow-hidden shadow-2xl">

                <!-- Header: Warna Solid & Teks Putih -->
                <div :class="['px-4 py-3 flex items-center gap-2 text-white font-bold', status.color]">
                    <span class="text-sm tracking-wide">{{ status.label }}</span>
                    <span class="opacity-70 text-sm font-medium">{{ getTasksByStatus(status.id).length }}</span>
                </div>

                <!-- Drop Area: Body Gray/Slate -->
                <div :ref="(el) => initColumn(el as HTMLElement, status.id)" :data-status="status.id"
                    class="p-3 space-y-3 min-h-[500px]">

                    <!-- Card Task (Droppable) -->
                    <div v-for="task in getTasksByStatus(status.id)" :key="task.id" :data-id="task.id"
                        class="bg-[#1a1d26] border border-slate-700/30 rounded-xl p-4 shadow-lg cursor-grab active:cursor-grabbing group transition-all">

                        <!-- Title Task -->
                        <h3 class="text-slate-300 text-[13px] font-medium mb-3 leading-snug">
                            {{ task.title }}
                        </h3>

                        <!-- Row: Badges (Priority, SP, Type) -->
                        <div class="flex items-center gap-2 mb-4">
                            <!-- Priority Card Kecil -->
                            <div
                                :class="['bg-[#252a37] text-slate-400 text-[10px] px-2 py-1 border-l-2 rounded-r rounded-l-sm font-semibold', getPriorityBorderColor(task.priority)]">
                                {{ task.priority || 'Medium' }}
                            </div>

                            <!-- SP Card Kecil -->
                            <div
                                class="bg-[#252a37] text-slate-400 text-[10px] px-2 py-1 border-l-2 border-l-slate-500 rounded-r rounded-l-sm font-semibold">
                                {{ task.estimated_sp || 0 }} <span class="text-[9px] opacity-60">SP</span>
                            </div>

                            <!-- Type Card Kecil -->
                            <div
                                class="bg-[#252a37] text-slate-400 text-[10px] px-2 py-1 border-l-2 border-l-[#c084fc] rounded-r rounded-l-sm font-semibold italic">
                                {{ task.type || 'Other' }}
                            </div>
                        </div>

                        <!-- Footer Card: Profile & Icons -->
                        <div class="flex justify-between items-end">
                            <div
                                class="relative w-7 h-7 rounded-full bg-slate-700 border border-slate-900 overflow-hidden shadow-sm">
                                <img v-if="getProfileUrl(task.assignee?.developer?.profile_picture)"
                                    :src="getProfileUrl(task.assignee?.developer?.profile_picture)!"
                                    class="w-full h-full object-cover" />
                                <span v-else
                                    class="flex items-center justify-center h-full text-[10px] text-white font-bold">
                                    {{ task.assignee?.name?.charAt(0) || '?' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 text-slate-500 opacity-60">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 3h6l2 2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V3z"></path>
                                    <path d="M12 10v6"></path>
                                    <path d="M9 13h6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State Placeholder -->
                    <div v-if="getTasksByStatus(status.id).length === 0"
                        class="h-20 border border-dashed border-slate-700/50 rounded-xl"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #0f111a;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #334155;
    border-radius: 10px;
}

div {
    user-select: none;
}
</style>