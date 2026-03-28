<template>
    <div class="flex items-center justify-center min-h-screen bg-slate-900 text-white p-4">
        <div class="w-full max-w-md bg-slate-800 p-8 rounded-2xl shadow-2xl border border-slate-700/50">

            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold tracking-tight text-white">Welcome Back</h1>
                <p class="text-slate-400 mt-2 text-sm">Please enter your details to sign in.</p>
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

            <form @submit.prevent="handleLogin" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2 ml-1">Email
                        Address</label>
                    <input v-model="email" type="email" placeholder="name@company.com"
                        :class="[inputClass, errors.email ? 'border-red-500/50' : 'border-slate-700']" />
                    <p v-if="errors.email" class="text-red-400 text-xs mt-1.5 ml-1">{{ errors.email }}</p>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label
                            class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Password</label>
                        <router-link to="/forgot-password"
                            class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                            Forgot password?
                        </router-link>
                    </div>
                    <div class="relative group">
                        <input :type="showPassword ? 'text' : 'password'" v-model="password" placeholder="••••••••"
                            :class="[inputClass, 'pr-12', errors.password ? 'border-red-500/50' : 'border-slate-700']" />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white p-1 transition-colors">
                            <Eye v-if="!showPassword" :size="20" />
                            <EyeOff v-else :size="20" />
                        </button>
                    </div>
                    <p v-if="errors.password" class="text-red-400 text-xs mt-1.5 ml-1">{{ errors.password }}</p>
                </div>

                <div class="pt-2">
                    <button type="submit" :disabled="isPending"
                        class="w-full py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-all transform active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-600/20 flex justify-center items-center">
                        <svg v-if="isPending" class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span v-if="isPending">Signing in...</span>
                        <span v-else>Sign In</span>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-700/50 text-center">
                <p class="text-sm text-slate-400">
                    Don't have an account?
                    <router-link to="/register"
                        class="text-blue-400 font-semibold hover:text-blue-300 transition-colors ml-1">
                        Create an account
                    </router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Eye, EyeOff } from "@lucide/vue";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useLogin } from "../hooks/useLogin";

// State
const email = ref("");
const password = ref("");
const showPassword = ref(false);
const router = useRouter();

// Vue Query Hook
const { mutate, isPending } = useLogin();

const errors = ref({
    email: "",
    password: "",
});

const alert = ref({
    type: "",
    message: "",
});

// Reusable Tailwind Classes
const inputClass =
    "w-full px-4 py-3 bg-slate-900/50 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-slate-600 text-slate-200 shadow-inner";

// Form Validation
const validate = () => {
    errors.value = { email: "", password: "" };
    let valid = true;

    if (!email.value.trim()) {
        errors.value.email = "Email address is required";
        valid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        errors.value.email = "Please enter a valid email format";
        valid = false;
    }

    if (!password.value) {
        errors.value.password = "Password is required";
        valid = false;
    }

    return valid;
};

// Login Action
const handleLogin = () => {
    alert.value.message = "";
    if (!validate()) return;

    const payload = {
        email: email.value.trim(),
        password: password.value,
    }

    console.log("Payload Yang Terkirim ke BE", JSON.stringify(payload));

    mutate(
        payload,
        {
            onSuccess: (data) => {
                alert.value = {
                    type: "success",
                    message: "Login successful! Redirecting..."
                };



                setTimeout(() => {

                    router.replace("/").then(() => {

                    });
                }, 800);
            },
            onError: (err: any) => {
                const backendMessage = err.response?.data?.message;
                alert.value = {
                    type: "error",
                    message: backendMessage || "Invalid email or password. Please try again.",
                };
            },
        }
    );
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

/* Mengatur agar auto-fill browser tidak merusak style input dark mode */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
    -webkit-text-fill-color: #e2e8f0;
    -webkit-box-shadow: 0 0 0px 1000px #0f172a inset;
    transition: background-color 5000s ease-in-out 0s;
}
</style>