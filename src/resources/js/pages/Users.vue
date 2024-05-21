<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import useUsers from "../composables/users";
import router from "../router";
import { useRoute } from "vue-router";
import { Bootstrap5Pagination } from "laravel-vue-pagination";
import CreateNewUserModal from "../components/modals/NewUser.vue";
import DeleteUserModal from "../components/modals/DeleteUser.vue";
import EditUserModal from "../components/modals/EditUser.vue";

const route = useRoute();
const { users, isLoading, getUsers, usersCount } = useUsers();

const updateErrors = ref({});

// for general quick search input
const quickSearchQuery = ref("");

// column name we are searching for
const columnNameSearchQuery = ref("");

// value for column name for eg. first_name = test
const columnValueSearchQuery = ref("");

// ref to column search input fields
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
    id: null,
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
        active: isActive,
    };
});
</script>
<template>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/frontend">UPFRONT</a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse"
                aria-controls="navbarCollapse"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a
                            class="nav-link active"
                            aria-current="page"
                            href="/frontend"
                            >Home</a
                        >
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="d-flex justify-content-between">
            <!-- Button trigger modal -->
            <button
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modal_new_user"
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
                            <th>#</th>
                            <th>
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
                                    <div
                                        class="d-flex gap-2 mt-3 mb-3 justify-content-between"
                                    >
                                        <div class="align-self-center">
                                            First Name
                                        </div>
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                :class="
                                                    sortButtonClasses(
                                                        isActiveBtn(
                                                            'fname_asc',
                                                        ),
                                                    )
                                                "
                                                @click="sortBy('fname_asc')"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="bi bi-sort-down"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"
                                                    ></path>
                                                </svg>
                                                <span class="visually-hidden"
                                                    >Button</span
                                                >
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                :class="
                                                    sortButtonClasses(
                                                        isActiveBtn(
                                                            'fname_desc',
                                                        ),
                                                    )
                                                "
                                                @click="sortBy('fname_desc')"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="bi bi-sort-up"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        d="M3.5 12.5a.5.5 0 0 1-1 0V3.707L1.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L3.5 3.707zm3.5-9a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"
                                                    ></path>
                                                </svg>
                                                <span class="visually-hidden"
                                                    >Button</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th>
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
                                    <div
                                        class="d-flex gap-2 mt-3 mb-3 justify-content-between"
                                    >
                                        <div class="align-self-center">
                                            Last Name
                                        </div>
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                :class="
                                                    sortButtonClasses(
                                                        isActiveBtn(
                                                            'lname_asc',
                                                        ),
                                                    )
                                                "
                                                @click="sortBy('lname_asc')"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="bi bi-sort-down"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"
                                                    ></path>
                                                </svg>
                                                <span class="visually-hidden"
                                                    >Button</span
                                                >
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                :class="
                                                    sortButtonClasses(
                                                        isActiveBtn(
                                                            'lname_desc',
                                                        ),
                                                    )
                                                "
                                                @click="sortBy('lname_desc')"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="bi bi-sort-up"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        d="M3.5 12.5a.5.5 0 0 1-1 0V3.707L1.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L3.5 3.707zm3.5-9a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"
                                                    ></path>
                                                </svg>
                                                <span class="visually-hidden"
                                                    >Button</span
                                                >
                                            </button>
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
                                    <div
                                        class="d-flex gap-2 mt-3 mb-3 justify-content-between"
                                    >
                                        <div class="align-self-center">
                                            Email
                                        </div>
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                :class="
                                                    sortButtonClasses(
                                                        isActiveBtn(
                                                            'email_asc',
                                                        ),
                                                    )
                                                "
                                                @click="sortBy('email_asc')"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="bi bi-sort-down"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"
                                                    ></path>
                                                </svg>
                                                <span class="visually-hidden"
                                                    >Button</span
                                                >
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                :class="
                                                    sortButtonClasses(
                                                        isActiveBtn(
                                                            'email_desc',
                                                        ),
                                                    )
                                                "
                                                @click="sortBy('email_desc')"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="bi bi-sort-up"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        d="M3.5 12.5a.5.5 0 0 1-1 0V3.707L1.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L3.5 3.707zm3.5-9a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"
                                                    ></path>
                                                </svg>
                                                <span class="visually-hidden"
                                                    >Button</span
                                                >
                                            </button>
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
                                    <div
                                        class="d-flex gap-2 mt-3 mb-3 justify-content-between"
                                    >
                                        <div class="align-self-center">
                                            Date
                                        </div>
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                :class="
                                                    sortButtonClasses(
                                                        isActiveBtn('date_asc'),
                                                    )
                                                "
                                                @click="sortBy('date_asc')"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="bi bi-sort-down"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"
                                                    ></path>
                                                </svg>
                                                <span class="visually-hidden"
                                                    >Button</span
                                                >
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                :class="
                                                    sortButtonClasses(
                                                        isActiveBtn(
                                                            'date_desc',
                                                        ),
                                                    )
                                                "
                                                @click="sortBy('date_desc')"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="bi bi-sort-up"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        d="M3.5 12.5a.5.5 0 0 1-1 0V3.707L1.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L3.5 3.707zm3.5-9a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"
                                                    ></path>
                                                </svg>
                                                <span class="visually-hidden"
                                                    >Button</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class=""></th>
                        </tr>
                    </thead>
                    <tbody class="" v-if="usersCount > 0 && !isLoading">
                        <tr v-for="(user, index) in users.data">
                            <td>{{ index + 1 }}</td>
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
                            <td></td>
                            <td colspan="2">
                                <div class="d-flex align-items-center">
                                    <strong role="status">Loading...</strong>
                                    <div
                                        class="spinner-border ms-auto"
                                        aria-hidden="true"
                                    ></div>
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tbody class="list" v-else>
                        <tr>
                            <td></td>
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
        id="modal_new_user"
        @list-users="listUsers"
    ></CreateNewUserModal>

    <!-- Delete User Modal-->
    <DeleteUserModal
        id="modal_delete_user"
        :deleteUser="deleteForm"
        @list-users="listUsers"
        @close-delete-modal="closeDeleteUserModal"
    >
    </DeleteUserModal>
    <!-- Edit User Modal-->
    <EditUserModal
        id="modal_edit_user"
        :editUser="editForm"
        @list-users="listUsers"
        @close-edit-modal="closeEditUserModal"
    >
    </EditUserModal>
</template>
