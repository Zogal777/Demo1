<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CreateClientInfo from './Partials/CreateClientInfo.vue'
import ClientContacts from './Partials/ClientContacts.vue'

const form = useForm({
  business_name: '',
  registration_num: '',
  tax_num: '',
  address: '',
  bank_account: '',
  contacts: [],
})

const contactsRef = ref(null)

const submit = () => {
  if (!contactsRef.value.validateContacts()) return
  form.post(route('clients.store'))
}

const cancelCreate = () => {
  form.reset()
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Add New Client
      </h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

        <div class="bg-white shadow sm:rounded-lg p-6">
          <CreateClientInfo :form="form" />
        </div>

        <div class="bg-white shadow sm:rounded-lg p-6">
          <ClientContacts :form="form" ref="contactsRef" :isEdit="false" />
        </div>

        <div class="flex justify-end gap-3">
          <Link
              :href="route('clients.index')"
              @click="cancelCreate"
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

      </div>
    </div>
  </AuthenticatedLayout>
</template>
