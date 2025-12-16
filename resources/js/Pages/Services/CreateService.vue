<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'

const form = useForm({
  item_name: '',
  description: '',
  price: 0
})

function saveService() {
  form.post(route('services.store'))
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Create Service" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Create Service</h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-4xl bg-white p-6 shadow rounded space-y-4">

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

        <div class="flex justify-end gap-3">

          <button
              @click="router.visit(route('services.index'))"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Cancel
          </button>

          <button
              @click="saveService"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Save Service
          </button>

        </div>

      </div>
    </div>

  </AuthenticatedLayout>
</template>

<style scoped>
.input { border: 1px solid #ccc; padding: 6px; border-radius: 6px; }
</style>
