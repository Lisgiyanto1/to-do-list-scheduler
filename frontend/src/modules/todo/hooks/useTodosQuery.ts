import { useMutation, useQuery, useQueryClient } from "@tanstack/vue-query";
import { computed, onUnmounted, ref, watch } from "vue";
import { createTodoApi, getTodosApi, updateTodoApi } from "../api/todo.api";
import { useTodoStore } from "../store/todo.store";


export const useTodosQuery = () => {
    const store = useTodoStore();


    const debouncedSearch = ref(store.search);
    let timeoutId: any = null;

    watch(() => store.search, (newVal) => {
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            debouncedSearch.value = newVal;
        }, 400);
    });

    onUnmounted(() => {
        if (timeoutId) clearTimeout(timeoutId);
    });


    const params = computed(() => ({
        search: debouncedSearch.value || undefined,
        assignee_id: store.selectedAssigneeId || undefined,
        sort_by: store.sortBy,
        sort_order: store.sortOrder,
        page: store.page,
        per_page: store.perPage,
    }));

    return useQuery({

        queryKey: computed(() => ["todos", params.value]),
        queryFn: () => getTodosApi(params.value),
        placeholderData: (prev) => prev,
        staleTime: 5000,
    });
};


export const useTodoMutations = () => {
    const queryClient = useQueryClient();

    const createMutation = useMutation({
        mutationFn: createTodoApi,

        onSuccess: (response) => {
            console.log("Response dari server:", response);
            queryClient.invalidateQueries({ queryKey: ["todos"] });
        },
    });

    const updateMutation = useMutation({
        mutationFn: ({ id, data }: { id: string; data: any }) => updateTodoApi(id, data),
        onMutate: async ({ id, data }) => {
            await queryClient.cancelQueries({ queryKey: ["todos"] });
            const previousTodos = queryClient.getQueryData(["todos"]);

            queryClient.setQueriesData({ queryKey: ["todos"] }, (oldData: any) => {
                if (!oldData?.data?.data) return oldData;
                return {
                    ...oldData,
                    data: {
                        ...oldData.data,
                        data: oldData.data.data.map((todo: any) =>
                            todo.id === id ? { ...todo, ...data } : todo
                        )
                    }
                };
            });
            return { previousTodos };
        },
        onError: (err, newTodo, context: any) => {
            if (context?.previousTodos) {
                queryClient.setQueryData(["todos"], context.previousTodos);
            }
        },
        onSettled: () => {
            queryClient.invalidateQueries({ queryKey: ["todos"] });
        },
    });

    return { createMutation, updateMutation };
};