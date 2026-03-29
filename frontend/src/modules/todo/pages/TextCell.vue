<template>
    <div @dblclick="startEdit"
        class="w-full h-[38px] flex items-center px-3 cursor-text border-r border-slate-700/50 group relative overflow-hidden">

        <!-- Input Mode -->
        <input v-if="editing" ref="inputRef" v-model="localValue" :type="type" @keydown.enter.prevent="handleEnter"
            @keydown.esc="cancel" @blur="handleBlur"
            class="bg-slate-900 text-slate-200 px-2 py-1 border border-blue-500 rounded w-[calc(100%-8px)] text-sm outline-none absolute left-1 z-20 shadow-lg" />

        <!-- Display Mode -->
        <div v-else class="flex items-center justify-between w-full h-full">
            <span
                class="block truncate text-sm text-slate-200 group-hover:bg-slate-800/50 w-full py-1 rounded transition-colors"
                :class="[textClass, { 'opacity-30': isSaving }]">
                {{ formatValue(value) }}
            </span>

            <!-- Icon Edit -->
            <span v-if="!isSaving"
                class="opacity-0 group-hover:opacity-100 text-slate-500 pointer-events-none transition-opacity ml-1">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                </svg>
            </span>

            <!-- Loader Pinia (Konsisten dengan SelectCell) -->
            <div v-if="isSaving" class="absolute inset-0 flex items-center justify-center bg-black/10">
                <Loader2 class="w-3 h-3 animate-spin text-blue-500" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Loader2 } from "@lucide/vue";
import { computed, nextTick, ref, watch } from "vue";
import { useTodoStore } from "../store/todo.store";

const props = defineProps({
    todoId: { type: String, required: false },
    field: { type: String, required: false },
    value: { type: [String, Number], default: "" },
    type: { type: String, default: "text" },
    suffix: { type: String, default: "" },
    textClass: { type: String, default: "" }
});

const emit = defineEmits(["save"]);
const store = useTodoStore();

const editing = ref(false);
const localValue = ref(props.value);
const inputRef = ref<HTMLInputElement | null>(null);

// Status loading dari Pinia
const isSaving = computed(() => {
    if (props.todoId && props.field) {
        return store.isFieldLoading(props.todoId, props.field);
    }
    return false;
});

watch(() => props.value, (val) => {
    if (!editing.value) localValue.value = val;
}, { immediate: true });

const formatValue = (val: any) => {
    if (val === null || val === undefined || val === '') return '-';
    return props.suffix ? `${val}${props.suffix}` : val;
};

const startEdit = async () => {
    if (isSaving.value) return; // Jangan edit jika sedang saving
    editing.value = true;
    await nextTick();
    inputRef.value?.focus();
    inputRef.value?.select();
};

const save = () => {
    if (!editing.value) return;

    // Konversi ke number jika type adalah number
    let finalValue = localValue.value;
    if (props.type === 'number') {
        finalValue = finalValue === "" ? 0 : Number(finalValue);
    }

    // Hanya emit jika nilai benar-benar berubah
    if (finalValue !== props.value) {
        emit("save", finalValue);
    }

    editing.value = false;
};

const handleEnter = () => {
    save();
};

const handleBlur = () => {
    if (editing.value) save();
};

const cancel = () => {
    editing.value = false;
    localValue.value = props.value;
};
</script>