<script setup>
import { ref, nextTick } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import EditClientInfo from './Partials/EditClientInfo.vue'
import ClientContacts from './Partials/ClientContacts.vue'
import DangerButton from '@/Components/DangerButton.vue'
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  client: Object
})

const form = useForm({
  business_name: props.client.business_name,
  registration_num: props.client.registration_num,
  tax_num: props.client.tax_num,
  address: props.client.address ?? '',
  bank_account: props.client.bank_account ?? '',
  contacts: props.client.contacts?.length
      ? props.client.contacts.map(c => ({
        type: c.type,
        value: c.value,
        comment: c.comment ?? ''
      }))
      : [],
})

const contactsRef = ref(null)

const submit = () => {
  if (!contactsRef.value.validateContacts()) return
  form.patch(route('clients.update', props.client.id), {
    preserveScroll: true,
  })
}

const cancelEdit = () => {
  form.reset()
}

/* ===== DELETE ===== */
const confirmingClientDeletion = ref(false)

const confirmClientDeletion = () => {
  confirmingClientDeletion.value = true
  nextTick(() => {})
}

const deleteClient = () => {
  form.delete(route('clients.destroy', props.client.id), {
    preserveScroll: true,
    onSuccess: closeModal,
    onFinish: () => form.reset(),
  })
}

const closeModal = () => {
  confirmingClientDeletion.value = false
  form.clearErrors()
  form.reset()
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Edit Client — {{ client.business_name }}
      </h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

        <div class="bg-white shadow sm:rounded-lg p-6">
          <EditClientInfo :form="form" />
        </div>

        <div class="bg-white shadow sm:rounded-lg p-6">
          <ClientContacts
              :form="form"
              ref="contactsRef"
              :isEdit="true"
          />
        </div>

        <div class="flex justify-end gap-3">
          <DangerButton @click="confirmClientDeletion">
            Delete Client
          </DangerButton>

          <Link
              :href="route('clients.index')"
              @click="cancelEdit"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Cancel
          </Link>

          <button
              @click="submit"
              :disabled="form.processing"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Save Client
          </button>
        </div>

        <!-- DELETE MODAL -->
        <Modal :show="confirmingClientDeletion" @close="closeModal">
          <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
              Are you sure?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
              This client will be permanently deleted. This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end">
              <SecondaryButton @click="closeModal">
                Cancel
              </SecondaryButton>

              <DangerButton
                  class="ml-3"
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                  @click="deleteClient"
              >
                Delete
              </DangerButton>
            </div>
          </div>
        </Modal>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
