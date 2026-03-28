import axios from "axios";

export const apiClient = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest"
    }
});


apiClient.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("token");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);


apiClient.interceptors.response.use(
    (response) => {

        return response.data;
    },
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem("token");
            if (!window.location.pathname.includes('/login')) {
                window.location.replace("/login");
            }
        }
        return Promise.reject(error);
    }
);