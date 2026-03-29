import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { ref, toRaw } from "vue";
import { updateTodoApi } from "../api/todo.api";
import { useTodoStore } from "../store/todo.store";

const queue = ref<Record<string, any>>({});
let timeout: ReturnType<typeof setTimeout> | null = null;

export const useUpdateTodoBatched = () => {
    const queryClient = useQueryClient();
    const store = useTodoStore();

    const mutation = useMutation({
        mutationFn: async (variables: Record<string, any>) => {
            const promises = Object.entries(variables).map(([id, payload]) => {
                // Gunakan toRaw agar Proxy Vue tidak ikut terkirim
                // Hilangkan field yang undefined agar tidak mengacaukan Laravel
                const cleanPayload = Object.fromEntries(
                    Object.entries(toRaw(payload)).filter(([_, v]) => v !== undefined)
                );
                return updateTodoApi(id, cleanPayload);
            });
            return Promise.all(promises);
        },

        onMutate: async () => {
            await queryClient.cancelQueries({ queryKey: ["todos"] });
        },

        onError: (err: any, variables) => {
            // Cek detail error dari Laravel di console
            console.error("SERVER ERROR 500:", err.response?.data || err.message);
            
            if (variables) {
                Object.entries(variables).forEach(([id, payload]) => {
                    Object.keys(payload as object).forEach((field) => {
                        store.setFieldLoading(id, field, false);
                    });
                });
            }
            
            const msg = err.response?.data?.message || "Internal Server Error";
            alert("Gagal update: " + msg);
        },

        onSettled: (_data, _error, variables) => {
            if (variables) {
                Object.entries(variables).forEach(([id, payload]) => {
                    Object.keys(payload as object).forEach((field) => {
                        store.setFieldLoading(id, field, false);
                    });
                });
            }
            queryClient.invalidateQueries({ queryKey: ["todos"] });
        },
    });

    const enqueue = (id: string, payload: any) => {
        Object.keys(payload).forEach(field => {
            store.setFieldLoading(id, field, true);
        });

        queue.value[id] = {
            ...(queue.value[id] || {}),
            ...payload,
        };

        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(() => {
            const currentUpdates = { ...queue.value };
            queue.value = {};
            if (Object.keys(currentUpdates).length > 0) {
                mutation.mutate(currentUpdates);
            }
        }, 800);
    };

    return { enqueue, isPending: mutation.isPending };
};