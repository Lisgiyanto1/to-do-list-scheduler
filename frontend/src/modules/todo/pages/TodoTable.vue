<template>
    <ToolBar />
    <div class="border border-slate-700 rounded-lg overflow-x-auto bg-[#1d1f27]">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="text-slate-400 border-b border-slate-700 bg-slate-800/50 font-medium">
                <tr>
                    <th class="w-10 px-3 py-2 border-r border-slate-700/50">
                        <input type="checkbox" class="rounded" />
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

            <tbody>
                <tr v-if="isLoading">
                    <td colspan="9" class="py-10 text-center text-slate-500">Loading...</td>
                </tr>
                <template v-else-if="todos.length > 0">
                    <TodoRow v-for="todo in todos" :key="todo.id" :todo="todo" />
                </template>
                <tr v-else>
                    <td colspan="9" class="py-10 text-center text-slate-500">No tasks found.</td>
                </tr>
            </tbody>

            <tfoot class="border-t border-slate-700 bg-slate-800/20 text-xs text-slate-400">
                <tr>
                    <td colspan="3" class="px-4 py-2 border-r border-slate-700/50"></td>

                    <!-- Status Bars -->
                    <td class="p-1 border-r border-slate-700/50">
                        <div
                            class="flex h-6 w-full rounded overflow-hidden opacity-80 hover:opacity-100 transition-opacity">
                            <div v-for="(count, status) in stats.status" :key="status"
                                :style="{ width: `${(count / todos.length) * 100}%` }"
                                :class="getColorClass(status as string, 'status')" :title="`${status}: ${count}`"></div>
                        </div>
                    </td>

                    <!-- Priority Bars -->
                    <td class="p-1 border-r border-slate-700/50">
                        <div
                            class="flex h-6 w-full rounded overflow-hidden opacity-80 hover:opacity-100 transition-opacity">
                            <div v-for="(count, prio) in stats.priority" :key="prio"
                                :style="{ width: `${(count / todos.length) * 100}%` }"
                                :class="getColorClass(prio as string, 'priority')" :title="`${prio}: ${count}`"></div>
                        </div>
                    </td>

                    <!-- Type Bars -->
                    <td class="p-1 border-r border-slate-700/50">
                        <div
                            class="flex h-6 w-full rounded overflow-hidden opacity-80 hover:opacity-100 transition-opacity">
                            <div v-for="(count, type) in stats.type" :key="type"
                                :style="{ width: `${(count / todos.length) * 100}%` }"
                                :class="getColorClass(type as string, 'type')" :title="`${type}: ${count}`"></div>
                        </div>
                    </td>

                    <td class="px-4 py-2 border-r border-slate-700/50"></td>

                    <td class="px-2 py-2 border-r border-slate-700/50 text-center font-medium">
                        {{ stats.estSP }} <span class="text-[10px] text-slate-500 block -mt-1">sum</span>
                    </td>
                    <td class="px-2 py-2 text-center font-medium">
                        {{ stats.actSP }} <span class="text-[10px] text-slate-500 block -mt-1">sum</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useTodosQuery } from "../hooks/useTodosQuery";
import { getColorClass } from "../utils/helper-color";
import TodoRow from './TodoRow.vue';
import ToolBar from './ToolBar.vue';

const { data: queryResponse, isLoading } = useTodosQuery();

const todos = computed(() => {
    const rootData = queryResponse.value;
    if (!rootData) return [];
    const paginationObject = rootData.data;
    if (paginationObject && Array.isArray(paginationObject.data)) return paginationObject.data;
    if (Array.isArray(paginationObject)) return paginationObject;
    return [];
});

// Calculate Footer Stats
const stats = computed(() => {
    const s = {
        status: {} as Record<string, number>,
        priority: {} as Record<string, number>,
        type: {} as Record<string, number>,
        estSP: 0,
        actSP: 0
    };

    todos.value.forEach((t?: any) => {
        if (t.status) s.status[t.status] = (s.status[t.status] || 0) + 1;
        if (t.priority) s.priority[t.priority] = (s.priority[t.priority] || 0) + 1;
        if (t.type) s.type[t.type] = (s.type[t.type] || 0) + 1;
        s.estSP += Number(t.estimated_sp) || 0;
        s.actSP += Number(t.actual_sp) || 0;
    });

    return s;
});


</script>