<template>
    <div
        class="flex items-center justify-center h-[38px] w-full border-r border-slate-700/50 group relative overflow-hidden">

        <div v-if="value && value.id" class="flex items-center justify-center relative">
            <div class="w-7 h-7 rounded-full bg-slate-700 border border-slate-900 flex items-center justify-center text-[10px] font-bold text-white shadow-sm hover:scale-110 transition-transform overflow-hidden relative"
                :title="`${value.name} (${value.developer?.status_akun || 'no status'})`">

                <img v-if="value.developer?.profile_picture" :src="value.developer.profile_picture"
                    class="w-full h-full object-cover" alt="profile" />

                <span v-else>
                    {{ value.name?.charAt(0).toUpperCase() || '?' }}
                </span>
            </div>

            <div v-if="value.developer?.status_akun === 'active'"
                class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-slate-900"
                title="Active Account"></div>
        </div>

        <div v-else class="flex items-center justify-center opacity-20 group-hover:opacity-100 transition-opacity">
            <div class="w-7 h-7 rounded-full border border-dashed border-slate-500 flex items-center justify-center">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    class="text-slate-400">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            </div>
        </div>
        <div class="absolute inset-0 cursor-pointer z-10" @click="handleEdit"></div>
    </div>
</template>

<script setup lang="ts">
interface AssigneeProps {
    id: string;
    name: string;
    developer?: {
        profile_picture: string | null;
        status_akun: string;
    }
}

const props = defineProps<{
    value?: AssigneeProps | null;
}>();

const emit = defineEmits(['save']);

const handleEdit = () => {
    console.log("Edit assignee for ID:", props.value?.id);
};
</script>

<style scoped>
div {
    user-select: none;
}
</style>