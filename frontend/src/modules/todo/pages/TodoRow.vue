<template>
    <!-- Tambahkan class bg-slate-800/20 jika isSelected true agar baris terlihat terpilih secara visual -->
    <tr class="border-b border-slate-700/50 hover:bg-slate-800/30 transition-colors group"
        :class="{ 'bg-slate-800/50': isSelected }">

        <!-- Checkbox: Sekarang terikat ke prop isSelected -->
        <td class="w-10 px-3 border-r border-slate-700/50">
            <input type="checkbox" :checked="isSelected" @change="$emit('toggle')"
                class="rounded bg-slate-900 border-slate-700 cursor-pointer checked:bg-indigo-500" />
        </td>

        <!-- Task Name -->
        <td class="p-0 border-r border-slate-700/50">
            <TextCell :value="todo.title" @save="(val) => updateField('title', val)" />
        </td>

        <!-- Developer -->
        <td class="w-32 p-0 border-r border-slate-700/50 text-center">
            <DeveloperCell :value="todo.assignee" />
        </td>

        <!-- Contoh kolom Status -->
        <SelectCell :todo-id="todo.id.toString()" :value="todo.status"
            :options="['ready to start', 'in_progress', 'done']" category="status"
            @save="(val) => updateField('status', val)" />

        <!-- Priority  -->
        <td class="w-32 p-0 border-r border-slate-700/50">
            <SelectCell :todo-id="todo.id.toString()" :value="todo.priority || ''"
                :options="['critical', 'high', 'medium', 'low', 'best effort']" category="priority"
                @save="(val) => updateField('priority', val)" />
        </td>

        <!-- Contoh kolom Type -->
        <SelectCell :todo-id="todo.id.toString()" :value="todo.type" :options="['task', 'bug', 'feature']"
            category="type" @save="(val) => updateField('type', val)" />

        <!-- Date -->
        <td class="w-32 p-0 border-r border-slate-700/50">
            <TextCell type="date" :value="todo.due_date" @save="(val) => updateField('due_date', val)" />
        </td>

        <!-- Est SP -->
        <td class="w-24 p-0 border-r border-slate-700/50">
            <TextCell type="number" field="estimated_sp" :todo-id="todo.id.toString()" :value="todo.estimated_sp"
                @save="(val) => updateField('estimated_sp', val)" />
        </td>

        <!-- Act SP -->
        <td class="w-24 p-0">
            <TextCell type="number" field="actual_sp" :todo-id="todo.id.toString()" :value="todo.actual_sp"
                @save="(val) => updateField('actual_sp', val)" />
        </td>
    </tr>
</template>

<script setup lang="ts">
import { useUpdateTodoBatched } from "../hooks/useUpdateTodoBatched";
import DeveloperCell from "./DeveloperCell.vue";
import SelectCell from "./SelectCell.vue";
import TextCell from "./TextCell.vue";

// 1. Tambahkan isSelected ke dalam Props
const props = defineProps<{
    todo: any,
    isSelected: boolean
}>();

// 2. Tambahkan defineEmits untuk mengirim event ke Parent
const emit = defineEmits(['toggle']);

const { enqueue, isPending } = useUpdateTodoBatched();

const updateField = (field: string, value: any) => {
    enqueue(props.todo.id.toString(), { [field]: value });
};
</script>