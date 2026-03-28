<template>
    <div class="flex items-center justify-center min-h-screen bg-slate-900 text-white p-4">
        <div class="w-full max-w-2xl bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700/50">
            <div class="mb-8 text-center md:text-left">
                <h1 class="text-3xl font-bold tracking-tight">Create Account</h1>
                <p class="text-slate-400 mt-2">Join us to start managing your tasks.</p>
            </div>

            <transition name="fade">
                <div v-if="alert.message" :class="[
                    'mb-6 p-4 rounded-xl text-sm font-medium flex items-center gap-3 border transition-all',
                    alert.type === 'success'
                        ? 'bg-green-500/10 border-green-500/20 text-green-400'
                        : 'bg-red-500/10 border-red-500/20 text-red-400'
                ]">
                    <div class="w-2 h-2 rounded-full animate-pulse"
                        :class="alert.type === 'success' ? 'bg-green-400' : 'bg-red-400'"></div>
                    {{ alert.message }}
                </div>
            </transition>

            <form @submit.prevent="handleRegister" class="space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2 ml-1">Full
                            Name</label>
                        <input v-model="name" type="text" placeholder="John Doe"
                            :class="[inputClass, errors.name ? 'border-red-500/50' : 'border-slate-700']" />
                        <p v-if="errors.name" class="text-red-400 text-xs mt-1.5 ml-1">{{ errors.name }}</p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2 ml-1">Email
                            Address</label>
                        <input v-model="email" type="email" placeholder="name@company.com"
                            :class="[inputClass, errors.email ? 'border-red-500/50' : 'border-slate-700']" />
                        <p v-if="errors.email" class="text-red-400 text-xs mt-1.5 ml-1">{{ errors.email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2 ml-1">Password</label>
                        <div class="relative group">
                            <input :type="showPassword ? 'text' : 'password'" v-model="password" placeholder="••••••••"
                                :class="[inputClass, 'pr-12', errors.password ? 'border-red-500/50' : 'border-slate-700']" />
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white p-1 transition-colors">
                                <Eye v-if="!showPassword" :size="18" />
                                <EyeOff v-else :size="18" />
                            </button>
                        </div>
                        <p v-if="errors.password" class="text-red-400 text-xs mt-1.5 ml-1 leading-tight">{{
                            errors.password }}</p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2 ml-1">Confirm
                            Password</label>
                        <div class="relative group">
                            <input :type="showConfirm ? 'text' : 'password'" v-model="password_confirmation"
                                placeholder="••••••••"
                                :class="[inputClass, 'pr-12', errors.password_confirmation ? 'border-red-500/50' : 'border-slate-700']" />
                            <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white p-1 transition-colors">
                                <Eye v-if="!showConfirm" :size="18" />
                                <EyeOff v-else :size="18" />
                            </button>
                        </div>
                        <p v-if="errors.password_confirmation" class="text-red-400 text-xs mt-1.5 ml-1 leading-tight">{{
                            errors.password_confirmation }}</p>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" :disabled="loading"
                        class="w-full py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-all transform active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-600/20 flex justify-center items-center">
                        <svg v-if="loading" class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span v-if="loading">Processing...</span>
                        <span v-else>Create Account</span>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-700/50 text-center">
                <p class="text-sm text-slate-400">
                    Already have an account?
                    <router-link to="/login"
                        class="text-blue-400 font-semibold hover:text-blue-300 transition-colors ml-1">
                        Sign in here
                    </router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { parseError } from "@/shared/lib/errorHandler";
import { Eye, EyeOff } from "@lucide/vue";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { registerApi } from "../api/auth.api";

const name = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref("");
const showPassword = ref(false);
const showConfirm = ref(false);
const loading = ref(false);

const router = useRouter();

const errors = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const alert = ref({ type: "", message: "" });

const inputClass =
    "w-full px-4 py-3 bg-slate-900/50 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-slate-600 text-slate-200";

const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

const validate = () => {
    errors.value = { name: "", email: "", password: "", password_confirmation: "" };
    let valid = true;

    if (!name.value.trim()) { errors.value.name = "Full name required"; valid = false; }
    if (!email.value.trim()) { errors.value.email = "Email required"; valid = false; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) { errors.value.email = "Invalid email"; valid = false; }

    if (!password.value) { errors.value.password = "Password required"; valid = false; }
    else if (!passwordRegex.test(password.value)) {
        errors.value.password = "Min 8 chars, uppercase, lowercase, number, symbol";
        valid = false;
    }

    if (password_confirmation.value !== password.value) {
        errors.value.password_confirmation = "Passwords do not match";
        valid = false;
    }

    return valid;
};

const handleRegister = async () => {
    alert.value.message = "";
    if (!validate()) return;

    try {
        loading.value = true;
        await registerApi({
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value,
        });

        alert.value = { type: "success", message: "Registration successful! Redirecting..." };
        setTimeout(() => router.push("/login"), 1500);
    } catch (err: any) {
        const res = err.response?.data;
        if (res?.errors) {
            errors.value = {
                name: res.errors.name?.[0] || "",
                email: res.errors.email?.[0] || "",
                password: res.errors.password?.[0] || "",
                password_confirmation: res.errors.password_confirmation?.[0] || "",
            };
        }
        alert.value = { type: "error", message: parseError(err) || "Failed to register" };
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>