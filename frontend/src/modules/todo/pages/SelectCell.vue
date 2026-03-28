<script setup lang="ts">
import { Loader2 } from "@lucide/vue";
import { computed, ref, watch } from "vue";
import { getColorClass } from "../utils/helper-color";


const props = defineProps<{
    value: any;
    options: string[];
    category: 'status' | 'priority' | 'type';
    isSaving?: boolean;
}>();

const emit = defineEmits(["save"]);
const localValue = ref(props.value || "");

watch(() => props.value, (val) => localValue.value = val || "");

const handleSelectChange = () => emit("save", localValue.value);
const bgClass = computed(() => {
    return getColorClass(localValue.value, props.category);
});
</script>

<template>
    <div class="relative h-[38px] w-full flex items-center justify-center overflow-hidden border-r border-slate-700/50">
        <select v-model="localValue" @change="handleSelectChange" :disabled="isSaving"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20 disabled:cursor-not-allowed">
            <option value="">-</option>
            <option v-for="opt in options" :key="opt" :value="opt">{{ opt }}</option>
        </select>

        <div :class="[
            bgClass,
            'w-full h-full flex items-center justify-center text-[10px] font-bold px-2 text-center transition-all uppercase tracking-tighter relative'
        ]">
            <span :class="{ 'opacity-20': isSaving }">
                {{ localValue?.replace(/_/g, ' ') || '-' }}
            </span>

            <div v-if="isSaving" class="absolute inset-0 flex items-center justify-center bg-black/10">
                <Loader2 class="w-3 h-3 animate-spin text-white" />
            </div>
        </div>
    </div>
</template>