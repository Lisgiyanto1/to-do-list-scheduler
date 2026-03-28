<template>
    <div @dblclick="startEdit"
        class="w-full h-[38px] flex items-center px-3 cursor-text border-r border-slate-700/50 group relative">
        <input v-if="editing" ref="inputRef" v-model="localValue" :type="type" @keydown.enter.prevent="save"
            @keyup.esc="cancel" @blur="handleBlur"
            class="bg-slate-900 text-slate-200 px-2 py-1 border border-blue-500 rounded w-full text-sm outline-none absolute left-1 right-1 z-20 shadow-lg" />

        <span v-else
            class="block truncate text-sm text-slate-200 group-hover:bg-slate-800/50 w-full py-1 rounded transition-colors"
            :class="textClass">
            {{ formatValue(value) }}
        </span>

        <span v-if="!editing"
            class="absolute right-2 opacity-0 group-hover:opacity-100 text-slate-500 pointer-events-none transition-opacity">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
            </svg>
        </span>
    </div>
</template>

<script setup lang="ts">
import { nextTick, ref, watch } from "vue";

const props = defineProps({
    value: { type: [String, Number], default: "" },
    type: { type: String, default: "text" },
    suffix: { type: String, default: "" },
    textClass: { type: String, default: "" }
});

const emit = defineEmits(["save"]);

const editing = ref(false);
const localValue = ref(props.value);
const inputRef = ref<HTMLInputElement | null>(null);

watch(() => props.value, (val) => {
    if (!editing.value) localValue.value = val;
});

const formatValue = (val: any) => {
    if (val === null || val === undefined || val === '') return '';
    return props.suffix ? `${val}${props.suffix}` : val;
};

const startEdit = async () => {
    editing.value = true;
    await nextTick();
    if (inputRef.value) {
        inputRef.value.focus();
        inputRef.value.select();
    }
};

const save = () => {
    if (localValue.value === props.value) {
        editing.value = false;
        return;
    }
    editing.value = false;

    const valToEmit = props.type === 'number' ? Number(localValue.value) : localValue.value;
    emit("save", valToEmit);
};

const cancel = () => {
    editing.value = false;
    localValue.value = props.value;
};

const handleBlur = () => {
    if (editing.value) save();
};
</script>