<template>
    <div class="container">
        <h1>User Listing</h1>
        <div
            id="tableExample"
            data-list='{"valueNames":["name","email","age"],"page":5,"pagination":true}'
        >
            <div class="table-responsive scrollbar">
                <table class="table table-bordered table-striped fs--1 mb-0">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th class="sort" data-sort="name">First Name</th>
                            <th class="sort" data-sort="name">Last Name</th>
                            <th class="sort desc" data-sort="email">Email</th>
                            <th class=""></th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <tr v-for="user in users.data">
                            <td class="name">{{ user.firstName }}</td>
                            <td class="name">{{ user.lastName }}</td>
                            <td class="email">{{ user.email }}</td>
                            <td class="ops">
                                <div class="d-flex gap-3">
                                    <button
                                        class="btn btn-primary"
                                        @click.prevent="edit(1)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        class="btn btn-danger"
                                        @click.prevent="removeUser(user.id)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row align-items-center mt-3">
                <div class="pagination d-none">
                    <li class="active">
                        <button
                            class="page"
                            type="button"
                            data-i="1"
                            data-page="5"
                        >
                            1
                        </button>
                    </li>
                    <li>
                        <button
                            class="page"
                            type="button"
                            data-i="2"
                            data-page="5"
                        >
                            2
                        </button>
                    </li>
                    <li>
                        <button
                            class="page"
                            type="button"
                            data-i="3"
                            data-page="5"
                        >
                            3
                        </button>
                    </li>
                </div>
                <div class="col"></div>
                <div class="col-auto d-flex">
                    <button
                        class="btn btn-sm btn-primary disabled"
                        type="button"
                        data-list-pagination="prev"
                        disabled=""
                    >
                        <span>Previous</span>
                    </button>
                    <button
                        class="btn btn-sm btn-primary px-4 ms-2"
                        type="button"
                        data-list-pagination="next"
                    >
                        <span>Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted } from "vue";
import useUsers from "../composables/users";
const { users, getUsers, deleteUser } = useUsers();
onMounted(() => {
    getUsers();
});

const removeUser = async (id) => {
    if (!window.confirm("Are you sure you want to remove this user?")) {
        return;
    }

    await deleteUser(id);

    await getUsers();
};
</script>
