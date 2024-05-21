<script setup>
import { ref, toRefs, reactive, onMounted, onUpdated, watch } from "vue";
import axios from "axios";

const props = defineProps({
    deleteUser: {
        type: Object,
        required: true,
    },
});
const { deleteUser } = toRefs(props);

const emit = defineEmits(["list-users", "close-edit-modal"]);

const deleteFormOnSubmit = async () => {
    await axios.delete("/api/user/" + deleteUser.value.id);
    emit("list-users");
    emit("close-delete-modal");
};
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
