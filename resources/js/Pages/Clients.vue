<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  clients: Array,
})

const currentPage = ref(1)

const ROW_HEIGHT = 44
const HEADER_HEIGHT = 44
const ROWS = 11
const rowsPerPage = ROWS

const totalPages = computed(() =>
    Math.ceil(props.clients.length / rowsPerPage)
)

const paginatedClients = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage
  return props.clients.slice(start, start + rowsPerPage)
})

const tableHeight = computed(() =>
    HEADER_HEIGHT + Math.min(paginatedClients.value.length, ROWS) * ROW_HEIGHT
)

const prevPage = () => currentPage.value > 1 && currentPage.value--
const nextPage = () => currentPage.value < totalPages.value && currentPage.value++
const goToPage = p => (currentPage.value = p)
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Clients" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Clients</h2>
    </template>

    <div class="flex-1 flex justify-center pt-12 overflow-hidden">
      <div class="w-full max-w-6xl bg-white p-6 shadow rounded flex flex-col">

        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Clients List</h3>
          <Link
              :href="route('clients.create')"
              class="px-4 py-1.5 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            + Add Client
          </Link>
        </div>

        <div class="border rounded overflow-hidden" :style="{ height: tableHeight + 'px' }">
          <table class="w-full text-sm border-collapse text-center table-fixed">
            <thead class="bg-gray-100 border-b h-[44px]">
            <tr>
              <th class="border px-3 py-2">ID</th>
              <th class="border px-3 py-2">Business Name</th>
              <th class="border px-3 py-2">Registration No.</th>
              <th class="border px-3 py-2">PVN</th>
              <th class="border px-3 py-2">Address</th>
              <th class="border px-3 py-2">Bank Account</th>
              <th class="border px-3 py-2 w-32">Actions</th>
            </tr>
            </thead>

            <tbody>
            <tr
                v-for="c in paginatedClients"
                :key="c.id"
                class="h-[44px] hover:bg-gray-50"
            >
              <td class="border px-3 align-middle">{{ c.id }}</td>
              <td class="border px-3 align-middle truncate">{{ c.business_name }}</td>
              <td class="border px-3 align-middle">{{ c.registration_num }}</td>
              <td class="border px-3 align-middle">{{ c.tax_num }}</td>
              <td class="border px-3 align-middle truncate">{{ c.address }}</td>
              <td class="border px-3 align-middle truncate">{{ c.bank_account }}</td>
              <td class="border px-3 align-middle">
                <Link
                    :href="route('clients.edit', c.id)"
                    class="px-3 py-1.5 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm"
                >
                  Edit
                </Link>
              </td>
            </tr>
            </tbody>
          </table>
        </div>

        <div v-if="totalPages > 1" class="pt-4 flex justify-center gap-1 text-sm">
          <button @click="prevPage" :disabled="currentPage === 1"
                  class="px-2 py-1 border rounded disabled:opacity-40">&lt;</button>
          <button
              v-for="p in totalPages"
              :key="p"
              @click="goToPage(p)"
              class="px-3 py-1 border rounded"
              :class="p === currentPage
              ? 'bg-gray-800 text-white border-gray-800'
              : 'hover:bg-gray-100'"
          >
            {{ p }}
          </button>
          <button @click="nextPage" :disabled="currentPage === totalPages"
                  class="px-2 py-1 border rounded disabled:opacity-40">&gt;</button>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
