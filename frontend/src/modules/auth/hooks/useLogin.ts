import { useAuthStore } from "@/modules/auth/store/auth.store";
import { useMutation } from "@tanstack/vue-query";
import { loginApi } from "../api/auth.api";

// useLogin.ts
export const useLogin = () => {
    const authStore = useAuthStore();
    return useMutation({
        mutationFn: loginApi,
        onSuccess: (response) => {
            // DEBUG: Cek struktur asli dari backend Anda di console
            console.log("Full Response:", response);

            // Coba ambil token dari beberapa kemungkinan path
            const token = response.token || response.data?.token || response.access_token;

            if (token) {
                authStore.setToken(token);
            } else {
                console.error("Token tidak ditemukan! Periksa console 'Full Response' di atas.");
            }
        },
    });
};