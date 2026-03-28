import { apiClient } from "@/shared/lib/axios";


export interface TodoQueryParams {
    search?: string;
    assignee_id?: string | null;
    sort_by?: string;
    sort_order?: "asc" | "desc";
    page?: number;
    per_page?: number;
}

export const getTodosApi = async (params: TodoQueryParams) => {
    return await apiClient.get("/todos", { params });
};

export const createTodoApi = async (data: any) => {
    return await apiClient.post("/todos", data);
};

export const updateTodoApi = async (id: string, data: any) => {
    return await apiClient.put(`/todos/${id}`, data);
};

export const deleteTodoApi = async (id: string) => {
    return await apiClient.delete(`/todos/${id}`);
};