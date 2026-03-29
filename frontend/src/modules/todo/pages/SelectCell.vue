<script setup lang="ts">
import { Loader2 } from "@lucide/vue"; // Pastikan import benar
import { computed, ref, watch } from "vue";
import { useTodoStore } from "../store/todo.store"; // Import store Pinia Anda
import { getColorClass } from "../utils/helper-color";

const props = defineProps<{
    todoId: string; // Tambahkan todoId agar tahu baris mana yang loading
    value: any;
    options: string[];
    category: 'status' | 'priority' | 'type';
}>();

const emit = defineEmits(["save"]);
const store = useTodoStore();
const localValue = ref(props.value || "");

// 🔥 Ambil status loading spesifik dari Pinia berdasarkan ID dan Kategori (Field)
const isSaving = computed(() => store.isFieldLoading(props.todoId, props.category));

watch(() => props.value, (newVal) => {
    // Sync localValue dengan props, pastikan lowercase jika perlu
    localValue.value = newVal ? newVal.toString().toLowerCase() : "";
}, { immediate: true });

const handleSelectChange = () => {
    emit("save", localValue.value);
};

const bgClass = computed(() => {
    return getColorClass(localValue.value, props.category);
});
</script>

<template>
    <div class="relative h-[38px] w-full flex items-center justify-center overflow-hidden border-r border-slate-700/50">
        <!-- Select Element -->
        <!-- Z-index 20 agar tetap di atas, tapi disabled saat saving -->
        <select v-model="localValue" @change="handleSelectChange" :disabled="isSaving"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20 disabled:cursor-not-allowed">
            <option value="">-</option>
            <option v-for="opt in options" :key="opt" :value="opt">{{ opt }}</option>
        </select>

        <!-- Visual Display Layer -->
        <div :class="[
            bgClass,
            'w-full h-full flex items-center justify-center text-[10px] font-bold px-2 text-center transition-all uppercase tracking-tighter relative'
        ]">
            <!-- Teks disamarkan saat loading -->
            <span :class="{ 'opacity-10': isSaving }">
                {{ localValue?.replace(/_/g, ' ') || '-' }}
            </span>

            <!-- 🔥 Loader muncul tepat di tengah cell yang bersangkutan -->
            <div v-if="isSaving"
                class="absolute inset-0 flex items-center justify-center bg-black/20 backdrop-blur-[1px]">
                <Loader2 class="w-4 h-4 animate-spin text-white" />
            </div>
        </div>
    </div>
</template>