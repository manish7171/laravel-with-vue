<script setup>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";

const props = defineProps({});
const emit = defineEmits(["list-users"]);

const form = reactive({
    firstname: "",
    lastname: "",
    email: "",
});
const state = reactive({
    modal_new_user: null,
});

const storeUserErrors = ref({});

function formOnSubmit() {
    axios
        .post("/api/user/", { ...form })
        .then((response) => {
            closeNewUserModal();
            emit("list-users");
        })
        .catch((e) => {
            if (e.response.status === 422) {
                storeUserErrors.value = e.response.data.errors;
            }
        });
}

function closeNewUserModal() {
    state.modal_new_user.hide();
}

onMounted(() => {
    // Initial Modals
    state.modal_new_user = new bootstrap.Modal("#createNewUserModal", {});
});
</script>

<template>
    <div
        class="modal fade"
        id="createNewUserModal"
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
</template>
