<script setup>
import { ref, nextTick } from 'vue'
import {Head, useForm, Link, router} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  service: Object
})

const form = useForm({
  item_name: props.service.item_name,
  description: props.service.description,
  price: props.service.price,
})

/* UPDATE */
const updateService = () => {
  form.patch(route('services.update', props.service.id), {
    preserveScroll: true,
  })
}

/* CANCEL */
const cancelEdit = () => {
  form.reset()
}

/* DELETE MODAL */
const confirmingServiceDeletion = ref(false)

const confirmServiceDeletion = () => {
  confirmingServiceDeletion.value = true
  nextTick(() => {})
}

const deleteService = () => {
  form.delete(route('services.destroy', props.service.id), {
    preserveScroll: true,
    onSuccess: closeModal,
    onFinish: () => form.reset(),
  })
}

const closeModal = () => {
  confirmingServiceDeletion.value = false
  form.clearErrors()
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Service" />

    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Edit Service — {{ service.item_name }}
      </h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-4xl space-y-6">

        <!-- FORM -->
        <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">

          <div>
            <label>Item Name</label>
            <input v-model="form.item_name" class="input w-full" />
          </div>

          <div>
            <label>Description</label>
            <textarea v-model="form.description" class="input w-full"></textarea>
          </div>

          <div>
            <label>Price (€)</label>
            <input type="number" v-model="form.price" class="input w-full" />
          </div>
        </div>

        <div class="flex justify-end gap-3">

          <DangerButton @click="confirmServiceDeletion">
            Delete Service
          </DangerButton>

          <Link
              :href="route('services.index')"
              @click="cancelEdit"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Cancel
          </Link>

          <button
              @click="updateService"
              :disabled="form.processing"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Update Service
          </button>

        </div>


      </div>
    </div>

    <!-- DELETE MODAL -->
    <Modal :show="confirmingServiceDeletion" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Are you sure?
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          This service will be permanently deleted. This action cannot be undone.
        </p>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal">
            Cancel
          </SecondaryButton>

          <DangerButton
              class="ml-3"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="deleteService"
          >
            Delete
          </DangerButton>
        </div>
      </div>
    </Modal>

  </AuthenticatedLayout>
</template>

<style scoped>
.input {
  border: 1px solid #ccc;
  padding: 6px;
  border-radius: 6px;
}
</style>
