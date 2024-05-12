import { ref } from "vue";
import axios from "axios";

export default function useUsers() {
    const users = ref({});
    const errors = ref({});
    const getUsers = async () => {
        axios.get("/api/users").then((response) => {
            users.value = response.data;
        });
    };

    const deleteUser = async (id) => {
        axios.delete("/api/user/" + id);
    };

    const storeUser = async (data) => {
        axios.post("/api/user/", data).catch((e) => {
            if (e.response.status === 422) {
                errors.value = e.response.data.errors;
            }
            return e;
        });
    };
    return { users, getUsers, deleteUser, storeUser, errors };
}
