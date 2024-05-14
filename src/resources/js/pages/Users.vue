<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import useUsers from "../composables/users";
import router from "../router";
import { useRoute } from "vue-router";
import { Bootstrap5Pagination } from "laravel-vue-pagination";
// ...

const route = useRoute();
//import NewUser from "../components/modals/NewUser.vue";
const {
    users,
    isLoading,
    getUsers,
    usersCount,
    storeUser,
    storeUserErrors,
    updateUser,
    updateErrors,
    deleteUser,
} = useUsers();

// for general quick search input
const quickSearchQuery = ref("");

//column name we are searching for
const columnNameSearchQuery = ref("");

//value for column name for eg. first_name = test
const columnValueSearchQuery = ref("");

function search() {
    const sortby = route.query.sort ?? "";
    const page = route.query.page ?? 1;
    const searchValue = quickSearchQuery.value;
    const searchColumnName = columnNameSearchQuery.value;
    const searchColumnValue = columnValueSearchQuery.value;

    router.push({
        path: "/frontend",
        query: {
            page: page,
            sort: sortby,
            search_col: searchColumnName,
            search_col_val: searchColumnValue,
            search: searchValue,
        },
    });

    getUsers(page, sortby, searchColumnName, searchColumnValue, searchValue);
}

const form = reactive({
    firstname: "",
    lastname: "",
    email: "",
});

const editForm = reactive({
    firstname: "",
    lastname: "",
    email: "",
});

const deleteForm = reactive({
    id: "",
});

const state = reactive({
    modal_new_user: null,
    modal_edit_user: null,
    modal_delete_user: null,
});

const formOnSubmit = async (event) => {
    await storeUser({ ...form });
    if (Object.values(storeUserErrors.value).length === 0) {
        closeNewUserModal();
        await getUsers();
    }
};

const updateFormOnSubmit = async () => {
    await updateUser({ ...editForm });
    if (Object.values(updateErrors.value).length === 0) {
        closeEditUserModal();
        await getUsers();
    }
};

const deleteFormOnSubmit = async () => {
    await deleteUser({ ...deleteForm });
    closeDeleteUserModal();
    await getUsers();
};

onMounted(() => {
    state.modal_new_user = new bootstrap.Modal("#modal_new_user", {});
    state.modal_edit_user = new bootstrap.Modal("#modal_edit_user", {});
    state.modal_delete_user = new bootstrap.Modal("#modal_delete_user", {});
    quickSearchQuery.value = route.query.search ?? "";
    columnNameSearchQuery.value = route.query.search_col ?? "";
    columnValueSearchQuery.value = route.query.search_col_val ?? "";

    getUsers(
        route.query.page ?? 1,
        route.query.sort ?? "",
        route.query.search_col ?? "",
        route.query.search_col_val ?? "",
        route.query.search ?? "",
    );
});

function openNewUserModal() {
    state.modal_new_user.show();
}

function filterUsersById(id) {
    return users.value.data.filter((item) => item.id === id);
}

function openEditUserModal(id) {
    const editUser = filterUsersById(id);

    editForm.firstname = editUser[0].firstName;
    editForm.lastname = editUser[0].lastName;
    editForm.email = editUser[0].email;
    editForm.id = editUser[0].id;

    state.modal_edit_user.show();
}

function openDeleteUserModal(id) {
    deleteForm.id = id;

    state.modal_delete_user.show();
}

function closeNewUserModal() {
    state.modal_new_user.hide();
}

function closeEditUserModal() {
    state.modal_edit_user.hide();
}

function closeDeleteUserModal() {
    state.modal_delete_user.hide();
}

const handleQuickSearch = debounce((event) => {
    search();
}, 300);

const handleColumnSearch = debounce((event, columnName) => {
    columnNameSearchQuery.value = columnName;
    columnValueSearchQuery.value = "";
    columnValueSearchQuery.value = event.target.value;
    search();
}, 300);

function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

async function sortBy(sortby) {
    const searchBy = route.query.search ?? "";
    const page = route.query.page ?? 1;
    const searchColumnName = columnNameSearchQuery.value;
    const searchColumnValue = columnValueSearchQuery.value;

    router.push({
        path: "/frontend",
        query: {
            page: page,
            sort: sortby,
            search_col: searchColumnName,
            search_col_val: searchColumnValue,
            search: searchValue,
        },
    });
    await getUsers(
        page,
        sortby,
        searchColumnName,
        searchColumnValue,
        searchValue,
    );

    // router.push({
    //     path: "/frontend",
    //     query: { page: page, sort: sortby, search: searchBy },
    // });
    // await getUsers(page, sortby, searchBy);
}

const paginate = async (page) => {
    const searchBy = route.query.search ?? "";
    const sortby = route.query.sort ?? "";
    const searchColumnName = columnNameSearchQuery.value;
    const searchColumnValue = columnValueSearchQuery.value;

    router.push({
        path: "/frontend",
        query: {
            page: page,
            sort: sortby,
            search_col: searchColumnName,
            search_col_val: searchColumnValue,
            search: searchValue,
        },
    });
    await getUsers(
        page,
        sortby,
        searchColumnName,
        searchColumnValue,
        searchValue,
    );
};
</script>
<template>
    <div class="container">
        <h1>User Listing</h1>
        <div class="d-flex justify-content-between">
            <button
                type="button"
                class="btn btn-primary"
                @click="openNewUserModal"
            >
                Create New User
            </button>
            <div>
                <input
                    type="text"
                    class="form-control float-end"
                    @input="handleQuickSearch"
                    placeholder="Quick Search.."
                    v-model="quickSearchQuery"
                    aria-label="Search"
                    aria-describedby="search-btn"
                />
            </div>
        </div>
        <div style="margin: 10px 0"></div>
        <div
            id="tableExample"
            data-list='{"valueNames":["name","email","age"],"page":5,"pagination":true}'
        >
            <div class="table-responsive">
                <table class="table table-bordered table-striped fs--1 mb-0">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th class="sort" data-sort="name">
                                <div class="row">
                                    <div class="col">
                                        <input
                                            type="text"
                                            class="form-control"
                                            @input="
                                                handleColumnSearch(
                                                    $event,
                                                    'fname',
                                                )
                                            "
                                            placeholder="Search.."
                                            aria-label="Search"
                                            aria-describedby="search-column-input"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">First Name</div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        @click="
                                                            sortBy('fname_asc')
                                                        "
                                                        style="rotate: 90deg"
                                                    >
                                                        &#8250;
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        @click="
                                                            sortBy('fname_desc')
                                                        "
                                                        style="rotate: 90deg"
                                                    >
                                                        &#8249;
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class="sort" data-sort="name">
                                <div class="row">
                                    <div class="col">
                                        <input
                                            type="text"
                                            class="form-control"
                                            @input="
                                                handleColumnSearch(
                                                    $event,
                                                    'lname',
                                                )
                                            "
                                            placeholder="Search.."
                                            aria-label="Search"
                                            aria-describedby="search-column-input"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">Last Name</div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        @click="
                                                            sortBy('lname_asc')
                                                        "
                                                        style="rotate: 90deg"
                                                    >
                                                        &#8250;
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        @click="
                                                            sortBy('lname_desc')
                                                        "
                                                        style="rotate: 90deg"
                                                    >
                                                        &#8249;
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class="sort desc" data-sort="email">
                                <div class="row">
                                    <div class="col">
                                        <input
                                            type="text"
                                            class="form-control"
                                            @input="
                                                handleColumnSearch(
                                                    $event,
                                                    'email',
                                                )
                                            "
                                            placeholder="Search.."
                                            aria-label="Search"
                                            aria-describedby="search-column-input"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">Email</div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        @click="
                                                            sortBy('email_asc')
                                                        "
                                                        style="rotate: 90deg"
                                                    >
                                                        &#8250;
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        @click="
                                                            sortBy('email_desc')
                                                        "
                                                        style="rotate: 90deg"
                                                    >
                                                        &#8249;
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class="">
                                <div class="row">
                                    <div class="col">
                                        <input
                                            type="text"
                                            class="form-control"
                                            @input="
                                                handleColumnSearch(
                                                    $event,
                                                    'date',
                                                )
                                            "
                                            placeholder="Search.."
                                            aria-label="Search"
                                            aria-describedby="search-column-input"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">Created Date</div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        @click="
                                                            sortBy('date_asc')
                                                        "
                                                        style="rotate: 90deg"
                                                    >
                                                        &#8250;
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        @click="
                                                            sortBy('date_desc')
                                                        "
                                                        style="rotate: 90deg"
                                                    >
                                                        &#8249;
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class=""></th>
                        </tr>
                    </thead>
                    <tbody class="list" v-if="usersCount > 0 && !isLoading">
                        <tr v-for="user in users.data">
                            <td class="name">{{ user.firstName }}</td>
                            <td class="name">{{ user.lastName }}</td>
                            <td class="email">{{ user.email }}</td>
                            <td class="email">{{ user.createdAt }}</td>
                            <td class="ops">
                                <div class="d-flex gap-3">
                                    <button
                                        class="btn btn-primary"
                                        @click="openEditUserModal(user.id)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        class="btn btn-danger"
                                        @click="openDeleteUserModal(user.id)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="list" v-else-if="isLoading">
                        <tr>
                            <td colspan="5">Loading...</td>
                        </tr>
                    </tbody>
                    <tbody class="list" v-else>
                        <tr>
                            <td colspan="5">No results found!</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Bootstrap5Pagination
                :data="users"
                @pagination-change-page="paginate"
            />
        </div>
    </div>

    <!-- New User Modal-->
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
                                :class="[
                                    storeUserErrors.firstname
                                        ? 'is-invalid'
                                        : '',
                                ]"
                                id="firstname"
                                name="firstname"
                                v-model="form.firstname"
                                aria-describedby="desc-firstname"
                                required
                            />
                            <div
                                v-if="storeUserErrors.firstname"
                                class="invalid-feedback"
                            >
                                {{ storeUserErrors.firstname[0] }}
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
                                :class="[
                                    storeUserErrors.lastname
                                        ? 'is-invalid'
                                        : '',
                                ]"
                                id="lastname"
                                name="lastname"
                                v-model="form.lastname"
                                aria-describedby="desc-lastname"
                                required
                            />
                            <div
                                v-if="storeUserErrors.lastname"
                                class="invalid-feedback"
                            >
                                {{ storeUserErrors.lastname[0] }}
                            </div>
                            <div id="desc-email" class="form-text d-none">
                                Type your last name.
                            </div>
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                :class="[
                                    storeUserErrors.email ? 'is-invalid' : '',
                                ]"
                                id="email"
                                name="email"
                                v-model="form.email"
                                aria-describedby="desc-email"
                                required
                            />
                            <div
                                v-if="storeUserErrors.email"
                                class="invalid-feedback"
                            >
                                {{ storeUserErrors.email[0] }}
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

    <!-- Edit User Modal-->

    <div
        class="modal fade"
        id="modal_edit_user"
        tabindex="-1"
        aria-labelledby="modal_edit_user"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <form @submit.prevent="updateFormOnSubmit">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="firstname" class="form-label"
                                >First Name</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                :class="[
                                    updateErrors.firstname ? 'is-invalid' : '',
                                ]"
                                id="firstname"
                                name="firstname"
                                v-model="editForm.firstname"
                                aria-describedby="desc-firstname"
                                required
                            />
                            <div
                                v-if="updateErrors.firstname"
                                class="invalid-feedback"
                            >
                                {{ updateErrors.firstname[0] }}
                            </div>

                            <div id="desc-email" class="form-text d-none">
                                Edit your first name.
                            </div>
                            <label for="lastname" class="form-label"
                                >Last Name</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                :class="[
                                    updateErrors.lastname ? 'is-invalid' : '',
                                ]"
                                id="lastname"
                                name="lastname"
                                v-model="editForm.lastname"
                                aria-describedby="desc-lastname"
                                required
                            />
                            <div
                                v-if="updateErrors.lastname"
                                class="invalid-feedback"
                            >
                                {{ updateErrors.lastname[0] }}
                            </div>
                            <div id="desc-email" class="form-text d-none">
                                Edit your last name.
                            </div>
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                :class="[
                                    updateErrors.email ? 'is-invalid' : '',
                                ]"
                                id="email"
                                name="email"
                                v-model="editForm.email"
                                aria-describedby="desc-email"
                                required
                            />
                            <div
                                v-if="updateErrors.email"
                                class="invalid-feedback"
                            >
                                {{ updateErrors.email[0] }}
                            </div>
                            <div id="desc-email" class="form-text d-none">
                                Edit your email address.
                            </div>
                            <input
                                type="hidden"
                                class="form-control"
                                :class="[
                                    updateErrors.email ? 'is-invalid' : '',
                                ]"
                                id="id"
                                name="id"
                                v-model="editForm.id"
                                aria-describedby="desc-email"
                                required
                            />
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
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal-->
    <div
        class="modal fade"
        id="modal_delete_user"
        tabindex="-1"
        aria-labelledby="modal_delete_user"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete User</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <form @submit.prevent="deleteFormOnSubmit">
                    <div class="modal-body">
                        <div class="mb-3">
                            Are you sure you want to delete this user?
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
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
