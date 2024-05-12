import { ref } from "vue";

export default function useUsers() {
    const users = ref([]);
    const getUsers = async () => {
        axios.get("/api/users").then((response) => {
            users.value = response.data;
        });
    };

    return { users, getUsers };
}
