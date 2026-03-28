import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { ref } from "vue";
import { updateTodoApi } from "../api/todo.api";

/**
 * GLOBAL QUEUE
 */
const queue = ref<Record<string, any>>({});
let timeout: ReturnType<typeof setTimeout> | null = null;

export const useUpdateTodoBatched = () => {
    const queryClient = useQueryClient();

    const mutation = useMutation({
        mutationFn: async (updates: Record<string, any>) => {
            console.log("DEBUG: SENDING TO API:", updates);

            const promises = Object.entries(updates).map(([id, payload]) =>
                updateTodoApi(id, payload)
            );

            return Promise.all(promises);
        },

        onMutate: async () => {
            await queryClient.cancelQueries({ queryKey: ["todos"] });
        },

        onError: (err) => {
            console.error("DEBIG: UPDATE ERROR,", err);
            alert("Update Gagal");
        },

        onSettled: () => {
            queryClient.invalidateQueries({ queryKey: ["todos"] });
        },
    });

    /**
     * ENQUEUE
     */
    const enqueue = (id: string, payload: any) => {
        console.log("QUEUE UPDATE", id, payload);

        queue.value[id] = {
            ...(queue.value[id] || {}),
            ...payload,
        };

        if (timeout) clearTimeout(timeout);

        timeout = setTimeout(() => {
            console.log("⏱ TIMER FIRED");

            const updates = { ...queue.value };
            queue.value = {};

            if (Object.keys(updates).length > 0) {
                mutation.mutate(updates);
            }
        }, 800);
    };

    return {
        enqueue,
        isPending: mutation.isPending,
    };
};