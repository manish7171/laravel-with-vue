<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import useUsers from "../composables/users";
import router from "../router";
import { useRoute } from "vue-router";
import { Bootstrap5Pagination } from "laravel-vue-pagination";
import axios from "axios";
import CreateNewUserModal from "../components/modals/NewUser.vue";

const route = useRoute();
const { users, isLoading, getUsers, usersCount } = useUsers();

//const testStoreErrors = ref({});
//const storeUserErrors = ref({});

const updateErrors = ref({});

// for general quick search input
const quickSearchQuery = ref("");

//column name we are searching for
const columnNameSearchQuery = ref("");

//value for column name for eg. first_name = test
const columnValueSearchQuery = ref("");

//ref to column search input fields
const searchInputFirstname = ref("");
const searchInputLastname = ref("");
const searchInputEmail = ref("");
const searchInputDate = ref("");

const editForm = reactive({
    id: "",
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

const listUsers = async () => {
    const page = route.query.page ?? 1;
    const sortby = route.query.sort ?? "";
    const searchValue = route.query.search ?? quickSearchQuery.value;
    const searchColumnName =
        route.query.search_col ?? columnNameSearchQuery.value;
    const searchColumnValue =
        route.query.search_col_val ?? columnValueSearchQuery.value;

    await getUsers(
        page,
        sortby,
        searchColumnName,
        searchColumnValue,
        searchValue,
    );
};

const updateFormOnSubmit = async (event) => {
    const data = { ...editForm };
    axios
        .put("/api/user/" + data.id, data)
        .then((response) => {
            listUsers();
            closeEditUserModal();
        })
        .catch((e) => {
            if (e.response.status === 422) {
                updateErrors.value = e.response.data.errors;
            }
        });
};

const deleteFormOnSubmit = async () => {
    const data = { ...deleteForm };
    await axios.delete("/api/user/" + data.id);
    listUsers();
    closeDeleteUserModal();
};

const handleQuickSearch = debounce((event) => {
    columnNameSearchQuery.value = "";
    columnValueSearchQuery.value = "";
    quickSearchQuery.value = event.target.value;
    search();
}, 300);

const handleColumnSearch = debounce((event, columnName) => {
    columnNameSearchQuery.value = columnName;
    columnValueSearchQuery.value = "";
    columnValueSearchQuery.value = event.target.value;
    search();
}, 300);

onMounted(() => {
    // Initial Modals
    // state.modal_new_user = new bootstrap.Modal("#modal_new_user", {});
    state.modal_edit_user = new bootstrap.Modal("#modal_edit_user", {});
    state.modal_delete_user = new bootstrap.Modal("#modal_delete_user", {});

    // load input search input values on refresh
    const columnSearchInput = route.query.search_col ?? "";
    const columnSearchInputValue = route.query.search_col_val ?? "";

    if (columnSearchInput !== "" || columnSearchInputValue !== "") {
        loadInputSearchValues(columnSearchInput, columnSearchInputValue);
    } else {
        // Only load quick search input values on refresh
        // when column search value is not present
        quickSearchQuery.value = route.query.search ?? "";
    }

    listUsers();
});

function loadInputSearchValues(columnName, columnInputValue) {
    switch (columnName) {
        case "fname":
            searchInputFirstname.value = columnInputValue;
            break;
        case "lname":
            searchInputLastname.value = columnInputValue;
            break;
        case "email":
            searchInputEmail.value = columnInputValue;
            break;
        case "date":
            searchInputDate.value = columnInputValue;
            break;
        default:
            break;
    }
}

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

function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

const quickSearchFocus = () => {
    // When Quick Search Input is focused then Column Search Inputs should be empty

    searchInputFirstname.value = "";
    searchInputLastname.value = "";
    searchInputEmail.value = "";
    searchInputDate.value = "";
};

const searchColumnFocus = (columnName) => {
    // When Column Seach Input is focused, Quick search should be empty
    quickSearchQuery.value = "";

    switch (columnName) {
        case "fname":
            searchInputLastname.value = "";
            searchInputEmail.value = "";
            searchInputDate.value = "";
            break;
        case "lname":
            searchInputFirstname.value = "";
            searchInputEmail.value = "";
            searchInputDate.value = "";
            break;
        case "email":
            searchInputFirstname.value = "";
            searchInputLastname.value = "";
            searchInputDate.value = "";
            break;
        case "date":
            searchInputFirstname.value = "";
            searchInputLastname.value = "";
            searchInputEmail.value = "";
            break;
        default:
            break;
    }
};

const sortBy = async (sortby) => {
    const searchBy = route.query.search ?? "";
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
    await getUsers(
        page,
        sortby,
        searchColumnName,
        searchColumnValue,
        searchValue,
    );
};

const paginate = async (page) => {
    const searchBy = route.query.search ?? "";
    const sortby = route.query.sort ?? "";
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

    await getUsers(
        page,
        sortby,
        searchColumnName,
        searchColumnValue,
        searchValue,
    );
};

const isActiveBtn = (name) => {
    return route.query.sort === name;
};

const sortButtonClasses = computed(() => (isActive) => {
    return {
        "text-white bg-dark": isActive,
    };
});
</script>
<template>
    <div class="container">
        <h1>User Listing</h1>
        <!--<TestModal id="exampleModal" @list-users="listUsers"></TestModal>-->
        <!-- Modal -->

        <div class="d-flex justify-content-between">
            <!-- Button trigger modal -->
            <button
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#createNewUserModal"
            >
                New User
            </button>
            <div>
                <input
                    type="text"
                    class="form-control float-end"
                    @input="handleQuickSearch"
                    placeholder="Quick Search.."
                    v-model="quickSearchQuery"
                    @focus="quickSearchFocus"
                    aria-label="Search"
                    aria-describedby="search-btn"
                />
            </div>
        </div>
        <div style="margin: 10px 0"></div>
        <div>
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
                                            v-model="searchInputFirstname"
                                            @focus="searchColumnFocus('fname')"
                                            placeholder="Search.."
                                            aria-label="Search"
                                            aria-describedby="search-column-input"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col align-self-center">
                                        First Name
                                    </div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        :class="
                                                            sortButtonClasses(
                                                                isActiveBtn(
                                                                    'fname_asc',
                                                                ),
                                                            )
                                                        "
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
                                                        :class="
                                                            sortButtonClasses(
                                                                isActiveBtn(
                                                                    'fname_desc',
                                                                ),
                                                            )
                                                        "
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
                                            v-model="searchInputLastname"
                                            @focus="searchColumnFocus('lname')"
                                            placeholder="Search.."
                                            aria-label="Search"
                                            aria-describedby="search-column-input"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col align-self-center">
                                        Last Name
                                    </div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        :class="
                                                            sortButtonClasses(
                                                                isActiveBtn(
                                                                    'lname_asc',
                                                                ),
                                                            )
                                                        "
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
                                                        :class="
                                                            sortButtonClasses(
                                                                isActiveBtn(
                                                                    'lname_desc',
                                                                ),
                                                            )
                                                        "
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
                                            v-model="searchInputEmail"
                                            @focus="searchColumnFocus('email')"
                                            placeholder="Search.."
                                            aria-label="Search"
                                            aria-describedby="search-column-input"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col align-self-center">
                                        Email
                                    </div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        :class="
                                                            sortButtonClasses(
                                                                isActiveBtn(
                                                                    'email_asc',
                                                                ),
                                                            )
                                                        "
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
                                                        :class="
                                                            sortButtonClasses(
                                                                isActiveBtn(
                                                                    'email_desc',
                                                                ),
                                                            )
                                                        "
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
                                            v-model="searchInputDate"
                                            @focus="searchColumnFocus('date')"
                                            placeholder="Search.."
                                            aria-label="Search"
                                            aria-describedby="search-column-input"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col align-self-center">
                                        Created Date
                                    </div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <span>
                                                    <button
                                                        :class="
                                                            sortButtonClasses(
                                                                isActiveBtn(
                                                                    'date_asc',
                                                                ),
                                                            )
                                                        "
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
                                                        :class="
                                                            sortButtonClasses(
                                                                isActiveBtn(
                                                                    'date_desc',
                                                                ),
                                                            )
                                                        "
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
                            <td>Loading...</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tbody class="list" v-else>
                        <tr>
                            <td>No results found!</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
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
    <CreateNewUserModal
        id="createNewUserModal"
        @list-users="listUsers"
    ></CreateNewUserModal>

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
