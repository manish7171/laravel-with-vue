<template>
    <div class="container">
        <h1>User Listing</h1>
        <div
            id="tableExample"
            data-list='{"valueNames":["name","email","age"],"page":5,"pagination":true}'
        >
            <div class="table-responsive scrollbar">
                <button
                    type="button"
                    class="btn btn-primary"
                    @click="openNewUserModal"
                >
                    Create New User
                </button>
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
    <div
        class="modal fade"
        id="modal_new_user"
        tabindex="-1"
        aria-labelledby="modal_new_user"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <form @submit.prevent="formOnSubmit">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="firstname" class="form-label"
                                >First Name</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                :class="[errors.firstname ? 'is-invalid' : '']"
                                id="firstname"
                                name="firstname"
                                v-model="form.firstname"
                                aria-describedby="desc-firstname"
                                required
                            />
                            <div
                                v-if="errors.firstname"
                                class="invalid-feedback"
                            >
                                {{ errors.firstname[0] }}
                            </div>

                            <div id="desc-email" class="form-text d-none">
                                Type your first name.
                            </div>
                            <label for="lastname" class="form-label"
                                >Last Name</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                :class="[errors.lastname ? 'is-invalid' : '']"
                                id="lastname"
                                name="lastname"
                                v-model="form.lastname"
                                aria-describedby="desc-lastname"
                                required
                            />
                            <div
                                v-if="errors.lastname"
                                class="invalid-feedback"
                            >
                                {{ errors.lastname[0] }}
                            </div>
                            <div id="desc-email" class="form-text d-none">
                                Type your last name.
                            </div>
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                :class="[errors.email ? 'is-invalid' : '']"
                                id="email"
                                name="email"
                                v-model="form.email"
                                aria-describedby="desc-email"
                                required
                            />
                            <div v-if="errors.email" class="invalid-feedback">
                                {{ errors.email[0] }}
                            </div>
                            <div id="desc-email" class="form-text d-none">
                                Type your email address.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script setup>
import { reactive, onMounted } from "vue";

import useUsers from "../composables/users";
import Confirm from "../components/modals/Confirm.vue";

const { users, getUsers, deleteUser, storeUser, errors } = useUsers();

const form = reactive({
    firstname: "",
    lastname: "",
    email: "",
});

const state = reactive({
    modal_new_user: null,
    modal_edit_user: null,
    modal_delete_user: null,
});

const formOnSubmit = async (event) => {
    await storeUser({ ...form });
    if (Object.values(errors.value).length === 0) {
        closeNewUserModal();
        getUsers();
    }
};

onMounted(() => {
    state.modal_new_user = new bootstrap.Modal("#modal_new_user", {});

    getUsers();
});
function openNewUserModal() {
    state.modal_new_user.show();
}

function closeNewUserModal() {
    state.modal_new_user.hide();
}
const removeUser = async (id) => {
    if (!window.confirm("Are you sure you want to remove this user?")) {
        return;
    }

    await deleteUser(id);

    await getUsers();
};
</script>
