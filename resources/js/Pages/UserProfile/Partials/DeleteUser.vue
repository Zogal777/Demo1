<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ user: Object });

const confirmingUserDeletion = ref(false);
const form = useForm({});

const deleteUser = () => {
  form.delete(route('users.destroy', props.user.id), {
    onSuccess: () => (confirmingUserDeletion.value = false),
  });
};
</script>

<template>
  <section class="space-y-6">
    <header>
      <h2 class="text-lg font-medium text-gray-900">Delete User</h2>
      <p class="mt-1 text-sm text-gray-600">
        Once deleted, all data will be permanently removed.
      </p>
    </header>

    <DangerButton @click="confirmingUserDeletion = true">
      Delete User
    </DangerButton>

    <Modal :show="confirmingUserDeletion" @close="confirmingUserDeletion = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Are you sure?
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          This user will be permanently deleted. This action cannot be undone.
        </p>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="confirmingUserDeletion = false">
            Cancel
          </SecondaryButton>

          <DangerButton class="ml-3" @click="deleteUser" :disabled="form.processing">
            Delete
          </DangerButton>
        </div>
      </div>
    </Modal>
  </section>
</template>
