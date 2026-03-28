import { apiClient } from "@/shared/lib/axios";

export const loginApi = async (payload: {
    email: string;
    password: string;
}) => {
    const { data } = await apiClient.post("/login", payload);
    return data;
};

export const registerApi = async (payload: any) => {
    const { data } = await apiClient.post("/register", payload);
    return data;
};