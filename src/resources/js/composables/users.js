import { ref } from "vue";
import axios from "axios";

export default function useUsers() {
    const users = ref({});
    const isLoading = ref(false);
    const usersCount = ref(0);

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

    return {
        users,
        isLoading,
        getUsers,
        usersCount,
    };
}
