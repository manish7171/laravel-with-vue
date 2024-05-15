<script setup>
import { ref, reactive, onMounted, onUpdated, watch } from "vue";
import axios from "axios";

const props = defineProps({
    deleteUser: {
        type: Object,
        required: true,
    },
});
const emit = defineEmits(["list-users"]);

const deleteUser = ref({ ...props.deleteUser }); // Create a reactive copy of the user data

const deleteFormOnSubmit = async () => {
    const data = { ...deleteUser };
    console.log(data);
    //await axios.delete("/api/user/" + data.id);
    //listUsers();
    //closeDeleteUserModal();
};

watch(
    () => props.deleteUser,
    (newValue) => {
        console.log("prop value changed", prop.value);
        deleteUser.value = { ...newValue };
    },
);

onUpdated(() => {
    console.log("onupdate");
});

onMounted(() => {
    // Initial Modals
    //state.modal_new_user = new bootstrap.Modal("#createNewUserModal", {});
});
</script>

<template>
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
