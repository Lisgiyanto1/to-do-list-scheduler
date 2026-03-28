<template>

    <div class="flex flex-col md:flex-col items-start justify-between gap-4 mb-6 px-1">
        <h1 class="text-xl font-bold text-white flex items-center gap-2">
            <span class="text-indigo-500">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M2 12h20" />
                </svg>
            </span>
            All Tasks
        </h1>

        <div class="flex flex-col md:flex-row gap-3">

            <button @click="openModal"
                class="bg-indigo-600 cursor-pointer hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-lg active:scale-95">
                + New Item
            </button>


            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-64 group">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </span>
                    <input v-model="searchModel" type="text" placeholder="Search task..."
                        class="w-full bg-slate-900/50 border border-slate-700/50 rounded-lg pl-10 pr-4 py-2 text-sm text-slate-200 outline-none focus:border-indigo-500 transition-all" />
                </div>

                <div class="relative inline-block">
                    <select v-model="assigneeFilter"
                        class="appearance-none bg-slate-900/50 border border-slate-700/50 rounded-lg py-2 pl-4 pr-10 text-sm text-slate-200 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all cursor-pointer">
                        <option :value="null">Filter Person</option>
                        <option value="me">Assigned to Me</option>
                        <option value="unassigned">Unassigned</option>
                    </select>


                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                        <ChevronDown :size="16" stroke-width="2" />
                    </div>
                </div>

                <button @click="store.setSort('priority')"
                    class="flex cursor-pointer items-center gap-2 bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-sm text-slate-300"
                    :class="{ 'border-indigo-500 text-indigo-400': store.sortBy === 'priority' }">
                    Sort <span v-if="store.sortBy === 'priority'">{{ store.sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <Transition name="fade">
        <div v-if="isModalOpen"
            class="fixed inset-0 bg-black/70 z-[100] flex items-center justify-center backdrop-blur-sm p-4">
            <div class="bg-[#1d1f27] border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-700/50 flex justify-between items-center bg-slate-800/20">
                    <h3 class="text-lg font-bold text-white">Create New Task</h3>
                    <button @click="isModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form @submit.prevent="handleCreate" class="p-6 space-y-5">
                    <!-- Title -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Task Title</label>
                        <input v-model="form.title" type="text" required
                            class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white outline-none focus:ring-2 focus:ring-indigo-500/50" />
                    </div>

                    <!-- Info Assignee-->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Assignee Info</label>
                        <div v-if="currentUser"
                            class="bg-slate-900/50 border border-slate-700/30 rounded-lg p-3 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-bold text-white">
                                {{ (currentUser?.name || "?").charAt(0) }}
                            </div>
                            <div>
                                <p class="text-sm text-slate-200">{{ currentUser.name }}</p>
                                <p class="text-[10px] text-slate-500 font-mono">{{ currentUser.id }}</p>
                            </div>
                        </div>
                        <div v-else class="text-slate-500 italic text-sm animate-pulse">Fetching user data...</div>
                    </div>

                    <!-- Date & Priority -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Due Date</label>
                            <input v-model="form.due_date" type="date" required
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white outline-none" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Priority</label>
                            <select v-model="form.priority" required
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white outline-none">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="isModalOpen = false"
                            class="flex-1 px-4 py-3 bg-slate-800 text-slate-300 rounded-xl font-semibold">Cancel</button>
                        <button type="submit" :disabled="createMutation.isPending.value || !form.assignee_id"
                            class="flex-1 px-4 py-3 bg-indigo-600 disabled:opacity-50 text-white rounded-xl font-bold shadow-lg active:scale-95">
                            {{ createMutation.isPending.value ? 'Creating...' : 'Create Task' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
import { apiClient } from "@/shared/lib/axios"; // Path axios Anda
import { ChevronDown } from "@lucide/vue";
import { computed, onMounted, reactive, ref } from "vue";
import { useTodoMutations } from "../hooks/useTodosQuery";
import { useTodoStore } from "../store/todo.store";

const store = useTodoStore();
const { createMutation } = useTodoMutations();

const isModalOpen = ref(false);
const currentUser = ref<{ id: string, name: string } | null>(null);

const form = reactive({
    title: "",
    assignee_id: "",
    due_date: new Date().toISOString().split('T')[0],
    priority: "medium"
});

// --- FETCH DATA DARI ENDPOINT /developer ---
// --- FETCH DATA DARI ENDPOINT /developer ---
const fetchUserData = async () => {
    try {
        // Karena apiClient sudah return response.data di interceptor,
        // maka 'res' di bawah ini berisi: { success: true, message: "...", data: {...} }
        const res: any = await apiClient.get("/developer");

        // Cek jika request sukses
        if (res && res.success) {
            // Sesuai JSON kamu, datanya ada di dalam field 'data'
            const userData = res.data;

            currentUser.value = {
                id: userData.id,     // Ini UUID user (0de87221...)
                name: userData.name  // Ini "Sofiyan"
            };

            // Masukkan UUID ke form secara otomatis
            form.assignee_id = userData.id;

            console.log("User data loaded:", userData.id);
        }
    } catch (error: any) {
        console.error("Gagal memuat data user:", error);
        // Jika error 401, interceptor Anda sudah menangani redirect ke /login
    }
};

onMounted(() => {
    fetchUserData();
});

// --- REAKTIVITAS PINIA ---
const searchModel = computed({
    get: () => store.search,
    set: (val: string) => store.setSearch(val)
});

const assigneeFilter = computed({
    get: () => store.selectedAssigneeId,
    set: (val: string | null) => store.setFilter(val)
});

const openModal = () => {
    // Jika data user belum ada (misal failed fetch awal), coba ambil lagi
    if (!form.assignee_id) fetchUserData();
    isModalOpen.value = true;
};

const handleCreate = () => {
    if (!form.assignee_id) return;
    console.log(JSON.stringify(form));
    createMutation.mutate({ ...form }, {
        onSuccess: () => {
            isModalOpen.value = false;
            form.title = "";
            form.due_date = new Date().toISOString().split('T')[0];
            form.priority = "medium";
        }
    });
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}
</style>