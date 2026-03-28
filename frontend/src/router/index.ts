import { createRouter, createWebHistory } from "vue-router";
// Import Layout di sini
import Kanban from "@/modules/kanban/Kanban.vue";
import Layout from "@/modules/todo/layouts/Layout.vue";

const LoginPage = () => import("@/modules/auth/pages/LoginPage.vue");
const RegisterPage = () => import("@/modules/auth/pages/RegisterPage.vue");
const TodoTable = () => import("@/modules/todo/pages/TodoTable.vue");

const routes = [
    {
        path: "/login",
        name: "Login",
        component: LoginPage,
        meta: { requiresGuest: true }
    },
    {
        path: "/register",
        name: "Register",
        component: RegisterPage,
        meta: { requiresGuest: true }
    },
    {
        path: "/",
        component: Layout,
        meta: { requiresAuth: true },
        children: [
            {
                path: "",
                name: "Home",
                component: TodoTable,
            },
            {
                path: "kanban",
                name: "kanban",
                component: Kanban,
            }
        ]
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from) => {
    const token = localStorage.getItem('token');

    if (to.meta.requiresAuth && !token) {
        return { name: 'Login' };
    }


    if (to.meta.requiresGuest && token) {
        return { path: '/' };
    }

    return true;
});

export default router;