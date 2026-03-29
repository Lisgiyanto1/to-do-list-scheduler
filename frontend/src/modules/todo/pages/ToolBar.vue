<template>
    <div class="flex flex-col items-start justify-between gap-4 mb-6 px-1">
        <div class="flex justify-between items-center w-full">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="text-indigo-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M2 12h20" />
                    </svg>
                </span>
                All Tasks
            </h1>

            <div class="flex items-center gap-3">
                <button @click="isLogoutModalOpen = true"
                    class="group flex items-center cursor-pointer justify-center bg-slate-800/50 hover:bg-red-500/20 text-slate-400 hover:text-red-400 border border-slate-700 hover:border-red-500/50 h-10 w-10 hover:w-32 px-0 hover:px-4 rounded-full transition-all duration-300 ease-in-out overflow-hidden">
                    <LogOut class="w-5 h-5 shrink-0" />
                    <span
                        class="max-w-0 opacity-0 group-hover:max-w-[100px] group-hover:opacity-100 group-hover:ml-2 whitespace-nowrap transition-all duration-300 font-bold text-sm">
                        Logout
                    </span>
                </button>

                <div class="relative group cursor-pointer" @click="openProfileModal">
                    <div
                        class="w-10 h-10 rounded-full border-2 border-indigo-500 p-0.5 transition-all group-hover:border-indigo-400 group-hover:scale-105 shadow-lg shadow-indigo-500/20">
                        <img v-if="currentUser?.full_profile_picture" :src="currentUser.full_profile_picture"
                            class="w-full h-full rounded-full object-cover" alt="Profile" />
                        <div v-else
                            class="w-full h-full rounded-full bg-slate-700 flex items-center justify-center text-white text-[10px] font-bold uppercase">
                            {{ (currentUser?.name || "?").charAt(0) }}
                        </div>
                    </div>
                    <div
                        class="absolute -bottom-0.5 -right-0.5 bg-indigo-600 rounded-full p-1 border border-slate-900 shadow-lg group-hover:bg-indigo-500 transition-colors">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-3 ">
            <button @click="openCreateModal"
                class="bg-indigo-600 cursor-pointer hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-lg active:scale-95 whitespace-nowrap">
                + New Item
            </button>

            <div class="flex flex-wrap items-center gap-3 w-full">
                <div class="relative flex-1 min-w-[200px] group">
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

                <div class="relative">
                    <select v-model="assigneeFilter"
                        class="appearance-none bg-slate-900/50 border border-slate-700/50 rounded-lg py-2 pl-4 pr-10 text-sm text-slate-200 outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all cursor-pointer">
                        <option :value="null">Filter Person</option>
                        <option value="me">Assigned to Me</option>
                        <option value="unassigned">Unassigned</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                        <ChevronDown :size="14" />
                    </div>
                </div>

                <button @click="store.setSort('priority')"
                    class="flex cursor-pointer items-center gap-2 bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 transition-colors"
                    :class="{ 'border-indigo-500 text-indigo-400': store.sortBy === 'priority' }">
                    Sort <span v-if="store.sortBy === 'priority'">{{ store.sortOrder === 'asc' ? '↑' : '↓' }}</span>
                </button>
            </div>
        </div>
    </div>

    <Transition name="fade">
        <div v-if="isCreateModalOpen"
            class="fixed inset-0 bg-black/70 z-[100] flex items-center justify-center backdrop-blur-sm p-4">
            <div class="bg-[#1d1f27] border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-700/50 flex justify-between items-center bg-slate-800/20">
                    <h3 class="text-lg font-bold text-white">Create New Task</h3>
                    <button @click="isCreateModalOpen = false"
                        class="text-slate-400 hover:text-white text-2xl">&times;</button>
                </div>

                <form @submit.prevent="handleCreate" class="p-6 space-y-5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Task Title</label>
                        <input v-model="form.title" type="text" required placeholder="What needs to be done?"
                            class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white outline-none focus:ring-2 focus:ring-indigo-500/50" />
                    </div>

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
                        <button type="button" @click="isCreateModalOpen = false"
                            class="flex-1 px-4 py-3 bg-slate-800 text-slate-300 rounded-xl font-semibold hover:bg-slate-700 transition-colors">Cancel</button>
                        <button type="submit" :disabled="createMutation.isPending.value"
                            class="flex-1 px-4 py-3 bg-indigo-600 disabled:opacity-50 text-white rounded-xl font-bold shadow-lg active:scale-95 transition-all">
                            {{ createMutation.isPending.value ? 'Creating...' : 'Create Task' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>

    <Transition name="fade">
        <div v-if="isProfileModalOpen"
            class="fixed inset-0 bg-black/80 z-[110] flex items-center justify-center backdrop-blur-md p-4">
            <div class="bg-[#1d1f27] border border-slate-700 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700/50 flex justify-between items-center bg-slate-800/10">
                    <h3 class="text-lg font-bold text-white">Edit My Profile</h3>
                    <button @click="isProfileModalOpen = false"
                        class="text-slate-400 hover:text-white text-2xl">&times;</button>
                </div>

                <form @submit.prevent="handleUpdateProfile" class="p-6 space-y-6">
                    <div class="flex flex-col items-center gap-4">
                        <div class="relative group w-28 h-28">
                            <img :src="profilePreview || currentUser?.full_profile_picture || 'https://ui-avatars.com/api/?background=6366f1&color=fff&name=' + currentUser?.name"
                                class="w-full h-full rounded-full object-cover border-4 border-slate-800 shadow-xl" />
                            <label
                                class="absolute inset-0 bg-black/60 rounded-full flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-all duration-300 backdrop-blur-sm">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white"
                                    stroke-width="2" class="mb-1">
                                    <path
                                        d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                    <circle cx="12" cy="13" r="4" />
                                </svg>
                                <span class="text-[10px] text-white font-bold uppercase tracking-wider">Change
                                    Photo</span>
                                <input type="file" class="hidden" accept="image/*" @change="onFileSelected" />
                            </label>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-slate-200 font-semibold">{{ currentUser?.name }}</p>
                            <p class="text-[11px] text-slate-500 font-mono mt-0.5">ID: {{ currentUser?.id }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Display
                                Name</label>
                            <input v-model="profileForm.name" type="text" required
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white outline-none focus:ring-2 focus:ring-indigo-500/50" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Email
                                Address</label>
                            <input v-model="profileForm.email" type="email" required
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white outline-none focus:ring-2 focus:ring-indigo-500/50" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Account
                            Status</label>
                        <select v-model="profileForm.status_akun"
                            class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white outline-none focus:ring-2 focus:ring-indigo-500/50">
                            <option value="active">Active (Available)</option>
                            <option value="busy">Busy (Do Not Disturb)</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="isProfileModalOpen = false"
                            class="flex-1 px-4 py-3 bg-slate-800 text-slate-300 rounded-xl font-semibold hover:bg-slate-700 transition-colors">Cancel</button>
                        <button type="submit" :disabled="isUpdating"
                            class="flex-1 px-4 py-3 bg-indigo-600 disabled:opacity-50 text-white rounded-xl font-bold shadow-lg active:scale-95 transition-all">
                            {{ isUpdating ? 'Saving Changes...' : 'Save Profile' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>

    <Transition name="fade">
        <div v-if="isLogoutModalOpen"
            class="fixed inset-0 bg-black/80 z-[200] flex items-center justify-center backdrop-blur-md p-4">
            <div
                class="bg-[#1d1f27] border border-slate-700 rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden p-6 text-center">
                <div
                    class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Sign Out?</h3>
                <p class="text-slate-400 text-sm mb-6">Are you sure you want to end your session? You will need to login
                    again to access your tasks.</p>

                <div class="flex gap-3">
                    <button @click="isLogoutModalOpen = false"
                        class="flex-1 px-4 py-3 cursor-pointer bg-slate-800 text-slate-300 rounded-xl font-semibold hover:bg-slate-700 transition-colors">
                        Cancel
                    </button>
                    <button @click="handleLogout"
                        class="flex-1 px-4 py-3 cursor-pointer bg-red-600 text-white rounded-xl font-bold shadow-lg shadow-red-600/20 active:scale-95 transition-all">
                        Yes, Logout
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
import { apiClient } from "@/shared/lib/axios";
import { ChevronDown, LogOut } from "@lucide/vue";
import { computed, onMounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useTodoMutations } from "../hooks/useTodosQuery";
import { useTodoStore } from "../store/todo.store";

const router = useRouter();
const store = useTodoStore();
const { createMutation } = useTodoMutations();

const isCreateModalOpen = ref(false);
const isProfileModalOpen = ref(false);
const isUpdating = ref(false);
const isLogoutModalOpen = ref(false);

const currentUser = ref<any>(null);
const profilePreview = ref<string | null>(null);
const selectedFile = ref<File | null>(null);

const form = reactive({
    title: "",
    assignee_id: "",
    due_date: new Date().toISOString().split('T')[0],
    priority: "medium"
});

const profileForm = reactive({
    name: "",
    email: "",
    status_akun: "active"
});

const fetchUserData = async () => {
    try {
        const res: any = await apiClient.get("/developer");
        const responseData = res.data?.success ? res.data.data : res.data;

        if (responseData) {
            const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';
            const profilePath = responseData.developer?.profile_picture;

            currentUser.value = {
                ...responseData,
                full_profile_picture: profilePath ? `${baseUrl}/storage/${profilePath}` : null
            };

            form.assignee_id = responseData.id;
            profileForm.name = responseData.name;
            profileForm.email = responseData.email;
            profileForm.status_akun = responseData.developer?.status_akun || "active";
        }
    } catch (error) {
        console.error("Fetch user failed:", error);
    }
};

const onFileSelected = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        selectedFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            profilePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const handleUpdateProfile = async () => {
    isUpdating.value = true;
    try {
        const formData = new FormData();
        formData.append("name", profileForm.name);
        formData.append("email", profileForm.email);
        formData.append("status_akun", profileForm.status_akun);
        formData.append("_method", "PATCH");

        if (selectedFile.value) {
            formData.append("profile_picture", selectedFile.value);
        }

        const res: any = await apiClient.post("/developer", formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (res.success || res.data?.success) {
            await fetchUserData();
            isProfileModalOpen.value = false;
            profilePreview.value = null;
            selectedFile.value = null;
        }
    } catch (error: any) {
        alert(error.response?.data?.message || "Failed to update profile");
    } finally {
        isUpdating.value = false;
    }
};

const openCreateModal = () => {
    if (!form.assignee_id) fetchUserData();
    isCreateModalOpen.value = true;
};

const openProfileModal = () => {
    isProfileModalOpen.value = true;
};

const handleCreate = () => {
    if (!form.assignee_id) return;
    createMutation.mutate({ ...form }, {
        onSuccess: () => {
            isCreateModalOpen.value = false;
            form.title = "";
            form.due_date = new Date().toISOString().split('T')[0];
            form.priority = "medium";
        }
    });
};

const handleLogout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    if (router) {
        router.push("/login");
    } else {
        window.location.href = "/login";
    }
};

const searchModel = computed({
    get: () => store.search,
    set: (val: string) => store.setSearch(val)
});

const assigneeFilter = computed({
    get: () => store.selectedAssigneeId,
    set: (val: string | null) => store.setFilter(val)
});

onMounted(fetchUserData);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}
</style>