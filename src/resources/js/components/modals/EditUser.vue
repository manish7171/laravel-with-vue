<script setup>
import { watch, ref, reactive, onMounted, defineProps } from "vue";
import axios from "axios";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const editedUser = ref({ ...props.user }); // Create a reactive copy of the user data

// Watch for changes in the props and update the editedUser accordingly
watch(
    () => props.user,
    (newValue) => {
        console.log("prop value changed", prop.value);
        editedUser.value = { ...newValue };
    },
);

const emit = defineEmits(["list-users"]);

//const editFirstname = ref(props.user.firstname);
//const editLastname = ref(props.user.lastname);
//const editEmail = ref(props.user.email);
//const editId = ref(props.user.id);
//
//const editForm = reactive({
//    id: props.user.id,
//    firstname: props.user.firstname,
//    lastname: props.user.lastname,
//    email: props.user.email,
//});

const state = reactive({
    modal_edit_user: null,
});

const updateErrors = ref({});

const updateFormOnSubmit = async (event) => {
    const data = { ...editedUser };
    console.log(data);
    // axios
    //     .put("/api/user/" + data.id, data)
    //     .then((response) => {
    //         listUsers();
    //         closeEditUserModal();
    //     })
    //     .catch((e) => {
    //         if (e.response.status === 422) {
    //             updateErrors.value = e.response.data.errors;
    //         }
    //     });
};

//function closeEditUserModal() {
//    state.modal_edit_user.hide();
//}

onMounted(() => {
    //console.log(editFirstname);
    //console.log(editForm);
    // Initial Modals
    //state.modal_edit_user = new bootstrap.Modal("#modal_edit_user", {});
    //editForm.value = {
    //    id: user.id,
    //    firstname: user.firstname,
    //    lastname: user.lastname,
    //    email: user.email,
    //};
});
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
                    {{ editedUser }}
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
                                v-model="editedUser.firstname"
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
                                v-model="editedUser.lastname"
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
                                v-model="editedUser.email"
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
                                v-model="editedUser.id"
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
