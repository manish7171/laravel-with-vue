<script setup>
import { watch, ref, toRefs, reactive, onMounted, defineProps } from "vue";
import axios from "axios";

const props = defineProps({
    editUser: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["list-users", "close-edit-modal"]);

const { editUser } = toRefs(props);

const updateErrors = ref({});

const updateFormOnSubmit = async (event) => {
    const userId = editUser.value.id;
    const firstname = editUser.value.firstname;
    const lastname = editUser.value.lastname;
    const email = editUser.value.email;
    const data = { firstname: firstname, lastname: lastname, email: email };
    axios
        .put("/api/user/" + userId, data)
        .then((response) => {
            emit("list-users");
            emit("close-edit-modal");
        })
        .catch((e) => {
            if (e.response.status === 422) {
                updateErrors.value = e.response.data.errors;
            }
        });
};
</script>

<template>
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
                                v-model="editUser.firstname"
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
                                v-model="editUser.lastname"
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
                                v-model="editUser.email"
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
                                v-model="editUser.id"
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
</template>
