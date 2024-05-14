import { ref } from "vue";
import axios from "axios";

export default function useUsers() {
    const users = ref({});
    const isLoading = ref(false);
    const storeUserErrors = ref({});
    const usersCount = ref(0);
    const updateErrors = ref({});

    const getUsers = async (
        page = 1,
        sort = "",
        searchColumn = "",
        searchColumnValue = "",
        search = "",
    ) => {
        isLoading.value = true;
        axios
            .get(
                "/api/users?page=" +
                    page +
                    "&sort=" +
                    sort +
                    "&search_col=" +
                    searchColumn +
                    "&search_col_val=" +
                    searchColumnValue +
                    "&search=" +
                    search,
            )
            .then((response) => {
                usersCount.value = response.data.meta.total;
                users.value = response.data;
                isLoading.value = false;
            });
    };

    const deleteUser = async (data) => {
        axios.delete("/api/user/" + data.id);
    };

    const storeUser = async (data) => {
        axios.post("/api/user/", data).catch((e) => {
            //return e;
            if (e.response.status === 422) {
                storeUserErrors.value = e.response.data.errors;
            }
        });
    };

    const updateUser = async (data) => {
        axios.put("/api/user/" + data.id, data).catch((e) => {
            if (e.response.status === 422) {
                updateErrors.value = e.response.data.errors;
            }
        });
    };
    return {
        users,
        isLoading,
        getUsers,
        usersCount,
        storeUser,
        storeUserErrors,
        updateUser,
        updateErrors,
        deleteUser,
    };
}
