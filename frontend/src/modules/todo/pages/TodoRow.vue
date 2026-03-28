<template>
    <tr class="border-b border-slate-700/50 hover:bg-slate-800/30 transition-colors group">
        <!-- Checkbox -->
        <td class="w-10 px-3 border-r border-slate-700/50">
            <input type="checkbox" class="rounded bg-slate-900 border-slate-700 cursor-pointer" />
        </td>

        <!-- Task Name -->
        <td class="p-0 border-r border-slate-700/50">
            <TextCell :value="todo.title" @save="(val) => updateField('title', val)" />
        </td>

        <!-- Developer -->
        <td class="w-32 p-0 border-r border-slate-700/50 text-center">
            <DeveloperCell :value="todo.assignee" />
        </td>

        <!-- Status  -->
        <td class="w-40 p-0 border-r border-slate-700/50">
            <SelectCell :value="todo.status || ''"
                :options="['ready to start', 'in progress', 'waiting for review', 'pending deploy', 'done', 'stuck']"
                :is-saving="isPending" category="status" @save="(val) => updateField('status', val)" />
        </td>

        <!-- Priority  -->
        <td class="w-32 p-0 border-r border-slate-700/50">
            <SelectCell :value="todo.priority || ''" :options="['critical', 'high', 'medium', 'low', 'best effort']"
                :is-saving="isPending" category="priority" @save="(val) => updateField('priority', val)" />
        </td>

        <!-- Type  -->
        <td class="w-48 p-0 border-r border-slate-700/50">
            <SelectCell :value="todo.type || 'task'" :options="['task', 'bug', 'feature', 'refactor']"
                :is-saving="isPending" category="type" @save="(val) => updateField('type', val)" />
        </td>

        <!-- Date -->
        <td class="w-32 p-0 border-r border-slate-700/50">
            <TextCell type="date" :value="todo.due_date" @save="(val) => updateField('due_date', val)" />
        </td>

        <!-- Est SP -->
        <td class="w-24 p-0 border-r border-slate-700/50">
            <TextCell type="number" :value="todo.estimated_sp || 0"
                @save="(val) => updateField('estimated_sp', Number(val))" />
        </td>

        <!-- Act SP -->
        <td class="w-24 p-0">
            <TextCell type="number" :value="todo.actual_sp || 0"
                @save="(val) => updateField('actual_sp', Number(val))" />
        </td>
    </tr>
</template>

<script setup lang="ts">
import { useUpdateTodoBatched } from "../hooks/useUpdateTodoBatched";
import DeveloperCell from "./DeveloperCell.vue";
import SelectCell from "./SelectCell.vue";
import TextCell from "./TextCell.vue";

const props = defineProps<{ todo: any }>();

const { enqueue, isPending } = useUpdateTodoBatched();

const updateField = (field: string, value: any) => {

    enqueue(props.todo.id.toString(), { [field]: value });
};
</script>