import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/frontend",
        name: "frontend",
        component: () => import("./pages/Users.vue"),
    },
];
export default createRouter({
    history: createWebHistory(),
    routes,
});
