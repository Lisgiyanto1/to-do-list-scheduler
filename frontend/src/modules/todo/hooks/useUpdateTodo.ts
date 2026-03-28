import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { updateTodoApi } from "../api/todo.api";

export const useUpdateTodo = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: ({ id, payload }: any) =>
            updateTodoApi(id, payload),

        onMutate: async (newTodo) => {
            await queryClient.cancelQueries({ queryKey: ["todos"] });

            const previous = queryClient.getQueryData(["todos"]);

            queryClient.setQueryData(["todos"], (old: any) => {
                return {
                    ...old,
                    data: {
                        ...old.data,
                        data: [newTodo, ...(old.data?.data || [])]
                    }
                };
            });

            return { previous };
        },
        /**
         * ROLLBACK jika gagal
         */
        onError: (err, variables, context) => {
            queryClient.setQueryData(["todos"], context?.previous);
        },

        /**
         * SYNC ulang dari server
         */
        onSettled: () => {
            queryClient.invalidateQueries({ queryKey: ["todos"] });
        }
    });
};