<script setup>
import { onMounted } from "vue";

const props = defineProps({
    id: String,
    submit_form: Function,
});
</script>
<template>
    <div
        :id="props.id"
        class="modal fade"
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
                <form @submit.prevent="props.submit_form">
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
