import { ref } from "vue";
import axios from "axios";

export default function useUsers() {
    const users = ref({});
    const errors = ref({});
    const usersCount = ref(0);
    const updateErrors = ref({});

    const getUsers = async (page = 1, sort = "", search = "") => {
        axios
            .get(
                "/api/users?page=" +
                    page +
                    "&sort=" +
                    sort +
                    "&search=" +
                    search,
            )
            .then((response) => {
                usersCount.value = response.data.meta.total;
                users.value = response.data;
            });
    };

    const deleteUser = async (data) => {
        axios.delete("/api/user/" + data.id);
    };

    const storeUser = async (data) => {
        axios
            .post("/api/user/", data)
            .then((response) => {
                return response;
            })
            .catch((e) => {
                if (e.response.status === 422) {
                    errors.value = e.response.data.errors;
                }
                return e;
            });
    };

    const updateUser = async (data) => {
        axios.put("/api/user/" + data.id, data).catch((e) => {
            if (e.response.status === 422) {
                updateErrors.value = e.response.data.errors;
            }
            return e;
        });
    };
    return {
        users,
        getUsers,
        usersCount,
        storeUser,
        errors,
        updateUser,
        updateErrors,
        deleteUser,
    };
}
