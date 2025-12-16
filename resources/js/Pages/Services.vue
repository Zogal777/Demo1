<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
  services: Array,
})

const currentPage = ref(1)

const ROW_HEIGHT = 44
const HEADER_HEIGHT = 44
const ROWS = 11
const rowsPerPage = ROWS

const totalPages = computed(() =>
    Math.ceil(props.services.length / rowsPerPage)
)

const paginatedServices = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage
  return props.services.slice(start, start + rowsPerPage)
})

const tableHeight = computed(() =>
    HEADER_HEIGHT + Math.min(paginatedServices.value.length, ROWS) * ROW_HEIGHT
)

const editService = id => router.get(route('services.edit', id))
const prevPage = () => currentPage.value > 1 && currentPage.value--
const nextPage = () => currentPage.value < totalPages.value && currentPage.value++
const goToPage = p => (currentPage.value = p)
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Services" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Services</h2>
    </template>

    <div class="flex-1 flex justify-center pt-12 overflow-hidden">
      <div class="w-full max-w-6xl bg-white p-6 shadow rounded flex flex-col">

        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Services List</h3>
          <a :href="route('services.create')" class="px-4 py-1.5 bg-gray-800 text-white rounded hover:bg-gray-700">
            + Create Service
          </a>
        </div>

        <div class="border rounded overflow-hidden" :style="{ height: tableHeight + 'px' }">
          <table class="w-full text-sm border-collapse text-center table-fixed">
            <thead class="bg-gray-100 border-b h-[44px]">
            <tr>
              <th class="border px-3 py-2">ID</th>
              <th class="border px-3 py-2">Item Name</th>
              <th class="border px-3 py-2">Description</th>
              <th class="border px-3 py-2">Price (€)</th>
              <th class="border px-3 py-2 w-32">Actions</th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="s in paginatedServices" :key="s.id" class="h-[44px] hover:bg-gray-50">
              <td class="border px-3 align-middle">{{ s.id }}</td>
              <td class="border px-3 align-middle truncate">{{ s.item_name }}</td>
              <td class="border px-3 align-middle truncate">{{ s.description }}</td>
              <td class="border px-3 align-middle">{{ s.price }}</td>
              <td class="border px-3 align-middle">
                <button @click="editService(s.id)" class="px-3 py-1.5 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm">
                  Edit
                </button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>

        <div v-if="totalPages > 1" class="pt-4 flex justify-center gap-1 text-sm">
          <button @click="prevPage" :disabled="currentPage === 1" class="px-2 py-1 border rounded disabled:opacity-40">&lt;</button>
          <button
              v-for="p in totalPages"
              :key="p"
              @click="goToPage(p)"
              class="px-3 py-1 border rounded"
              :class="p === currentPage ? 'bg-gray-800 text-white border-gray-800' : 'hover:bg-gray-100'"
          >
            {{ p }}
          </button>
          <button @click="nextPage" :disabled="currentPage === totalPages" class="px-2 py-1 border rounded disabled:opacity-40">&gt;</button>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
