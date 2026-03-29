<template>
    <ToolBar />
    <div class="rounded-lg overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap border-collapse">
            <thead class="text-slate-400 border border-slate-700 bg-slate-800/50 font-medium">
                <tr class="border border-slate-700">
                    <th class="w-10 px-3 py-2 border-r border-slate-700/50">
                        <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll"
                            class="rounded bg-slate-800 border-slate-600 checked:bg-indigo-500 cursor-pointer" />
                    </th>
                    <th class="px-4 py-2 border-r border-slate-700/50">Task</th>
                    <th class="w-32 px-4 py-2 border-r border-slate-700/50 text-center">Developer</th>
                    <th class="w-40 px-4 py-2 border-r border-slate-700/50 text-center">Status</th>
                    <th class="w-32 px-4 py-2 border-r border-slate-700/50 text-center">Priority</th>
                    <th class="w-48 px-4 py-2 border-r border-slate-700/50 text-center">Type</th>
                    <th class="w-32 px-4 py-2 border-r border-slate-700/50 text-center">Date</th>
                    <th class="w-24 px-4 py-2 border-r border-slate-700/50 text-center">Est. SP</th>
                    <th class="w-24 px-4 py-2 text-center">Act. SP</th>
                </tr>
            </thead>

            <tbody class="border border-slate-700">
                <tr v-if="isLoading">
                    <td colspan="9" class="py-10 text-center text-slate-500">Loading...</td>
                </tr>
                <template v-else-if="todos.length > 0">
                    <TodoRow v-for="todo in todos" :key="todo.id" :todo="todo"
                        :is-selected="selectedIds.includes(todo.id)" @toggle="toggleSelect(todo.id)" />
                </template>
                <tr v-else>
                    <td colspan="9" class="py-10 text-center text-slate-500">No tasks found.</td>
                </tr>
            </tbody>

            <tfoot class="text-xs text-slate-400">
                <tr class="footer-stats-row">
                    <td colspan="3" class="bg-transparent !border-none"></td>
                    
                    <td class="p-0 !border-none">
                        <div class="flex items-center w-full bg-slate-800/40 border border-slate-700 rounded-bl-xl h-10 px-3">
                            <div class="flex h-4 w-full rounded overflow-hidden opacity-80 shadow-inner bg-slate-900/50">
                                <div v-for="(count, status) in stats.status" :key="status"
                                    :title="`${status}: ${count} tasks`"
                                    :style="{ width: `${(count / (todos.length || 1)) * 100}%` }"
                                    :class="getColorClass(status as string, 'status')">
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="p-0 !border-none">
                        <div class="flex items-center w-full bg-slate-800/40 border border-slate-700 h-10 px-3">
                            <div class="flex h-4 w-full rounded overflow-hidden opacity-80 shadow-inner bg-slate-900/50">
                                <div v-for="(count, prio) in stats.priority" :key="prio"
                                    :title="`${prio}: ${count} tasks`"
                                    :style="{ width: `${(count / (todos.length || 1)) * 100}%` }"
                                    :class="getColorClass(prio as string, 'priority')">
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="p-0 !border-none">
                        <div class="flex items-center w-full bg-slate-800/40 border border-slate-700 h-10 px-3">
                            <div class="flex h-4 w-full rounded overflow-hidden opacity-80 shadow-inner bg-slate-900/50">
                                <div v-for="(count, type) in stats.type" :key="type"
                                    :title="`${type}: ${count} tasks`"
                                    :style="{ width: `${(count / (todos.length || 1)) * 100}%` }"
                                    :class="getColorClass(type as string, 'type')">
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="p-0 !border-none">
                        <div class="flex items-center w-full bg-slate-800/40 border border-slate-700 h-10 px-3">
                            <div class="flex h-4 w-full rounded overflow-hidden opacity-80 shadow-inner bg-slate-900/50">
                                <div class="w-full h-full bg-slate-600/50" title="Timeline coverage"></div>
                            </div>
                        </div>
                    </td>

                    <td class="p-0 !border-none">
                        <div class="flex items-center justify-center w-full bg-slate-800/40 border border-slate-700 h-10">
                            <span class="font-bold text-slate-200">{{ stats.estSP }} SP</span>
                        </div>
                    </td>

                    <td class="p-0 !border-none">
                        <div class="flex items-center justify-center w-full bg-slate-800/40 border border-slate-700 rounded-br-xl h-10">
                            <span class="font-bold text-slate-200">{{ stats.actSP }} SP</span>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { useTodosQuery } from "../hooks/useTodosQuery";
import { getColorClass } from "../utils/helper-color";
import TodoRow from './TodoRow.vue';
import ToolBar from './ToolBar.vue';

const { data: queryResponse, isLoading } = useTodosQuery();
const selectedIds = ref<string[]>([]);

const todos = computed(() => {
    const rootData = queryResponse.value;
    if (!rootData) return [];
    const items = rootData.data?.data || rootData.data || [];
    return Array.isArray(items) ? items : [];
});

const isAllSelected = computed(() => {
    return todos.value.length > 0 && selectedIds.value.length === todos.value.length;
});

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = todos.value.map((t: any) => t.id);
    }
};

const toggleSelect = (id: string) => {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(id);
    }
};

const stats = computed(() => {
    const s = {
        status: {} as Record<string, number>,
        priority: {} as Record<string, number>,
        type: {} as Record<string, number>,
        estSP: 0,
        actSP: 0
    };

    todos.value.forEach((t: any) => {
        if (t.status) s.status[t.status] = (s.status[t.status] || 0) + 1;
        if (t.priority) s.priority[t.priority] = (s.priority[t.priority] || 0) + 1;
        if (t.type) s.type[t.type] = (s.type[t.type] || 0) + 1;
        
        s.estSP += Number(t.estimated_sp) || 0;
        s.actSP += Number(t.actual_sp) || 0;
    });

    return s;
});
</script>

<style scoped>
.footer-stats-row td {
    border-top: none !important;
}

[title] {
    cursor: help;
}
</style>