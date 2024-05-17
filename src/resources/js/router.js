import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/frontend",
        name: "frontend",
        component: () => import("./pages/Users.vue"),
    },
    {
        path: "/frontend/testvue",
        name: "testvue",
        component: () => import("./pages/HomeRoute.vue"),
    },
];
export default createRouter({
    history: createWebHistory(),
    routes,
});
