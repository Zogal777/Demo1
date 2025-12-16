<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'

const props = defineProps({
  invoices: Array,
  filters: Object,
})

const filters = ref({
  company: props.filters?.company ?? '',
  client: props.filters?.client ?? '',
  invoice_date: props.filters?.invoice_date ?? '',
})

function search() {
  router.get(route('invoices.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}

function reset() {
  filters.value = { company: '', client: '', invoice_date: '' }
  search()
}

const currentPage = ref(1)

const ROW_HEIGHT = 44
const HEADER_HEIGHT = 44
const ROWS = 11
const rowsPerPage = ROWS

const totalPages = computed(() =>
    Math.ceil(props.invoices.length / rowsPerPage)
)

const paginatedInvoices = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage
  return props.invoices.slice(start, start + rowsPerPage)
})

const tableHeight = computed(() =>
    HEADER_HEIGHT + Math.min(paginatedInvoices.value.length, ROWS) * ROW_HEIGHT
)

const totalWithout = inv =>
    inv.items?.reduce((s, i) => s + i.price * i.quantity, 0) || 0

const totalPvn = inv =>
    inv.items?.reduce((s, i) => s + i.price * i.quantity * (i.pvn / 100), 0) || 0

const totalWith = inv => totalWithout(inv) + totalPvn(inv)

const downloadPdf = id =>
    window.open(route('invoices.pdf', id), '_blank')

const prevPage = () => currentPage.value > 1 && currentPage.value--
const nextPage = () => currentPage.value < totalPages.value && currentPage.value++
const goToPage = p => (currentPage.value = p)
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Invoices" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Invoices</h2>
    </template>

    <div class="flex-1 flex justify-center pt-12 overflow-hidden">
      <div class="w-full max-w-6xl bg-white p-6 shadow rounded flex flex-col">

        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Invoice List</h3>
          <Link
              :href="route('invoices.create')"
              class="px-4 py-1.5 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            + Create Invoice
          </Link>
        </div>

        <!-- FILTERS -->
        <div class="flex gap-3 mb-4 flex-wrap">
          <input v-model="filters.company" @input="search" placeholder="Company"
                 class="border rounded px-2 py-1 text-sm w-40" />
          <input v-model="filters.client" @input="search" placeholder="Client"
                 class="border rounded px-2 py-1 text-sm w-40" />
          <input type="date" v-model="filters.invoice_date" @change="search"
                 class="border rounded px-2 py-1 text-sm" />
          <button @click="reset"
                  class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300">
            Reset
          </button>
        </div>

        <!-- TABLE -->
        <div class="border rounded overflow-hidden" :style="{ height: tableHeight + 'px' }">
          <table class="w-full text-sm border-collapse text-center table-fixed">
            <thead class="bg-gray-100 border-b h-[44px]">
            <tr>
              <th class="border px-3 py-2">ID</th>
              <th class="border px-3 py-2">Company</th>
              <th class="border px-3 py-2">Client</th>
              <th class="border px-3 py-2">Invoice Date</th>
              <th class="border px-3 py-2">Payment Date</th>
              <th class="border px-3 py-2">Sum</th>
              <th class="border px-3 py-2">PVN</th>
              <th class="border px-3 py-2">Total</th>
              <th class="border px-3 py-2 w-32">Actions</th>
            </tr>
            </thead>

            <tbody>
            <tr
                v-for="inv in paginatedInvoices"
                :key="inv.id"
                class="h-[44px] hover:bg-gray-50"
            >
              <td class="border px-3 align-middle">{{ inv.id }}</td>
              <td class="border px-3 align-middle truncate">{{ inv.company_name }}</td>
              <td class="border px-3 align-middle truncate">{{ inv.client_name }}</td>
              <td class="border px-3 align-middle">{{ inv.invoice_date }}</td>
              <td class="border px-3 align-middle">{{ inv.payment_date }}</td>
              <td class="border px-3 align-middle">{{ totalWithout(inv).toFixed(2) }} €</td>
              <td class="border px-3 align-middle">{{ totalPvn(inv).toFixed(2) }} €</td>
              <td class="border px-3 align-middle font-semibold">
                {{ totalWith(inv).toFixed(2) }} €
              </td>
              <td class="border px-3 align-middle">
                <div class="flex justify-center gap-2">
                  <button
                      @click="downloadPdf(inv.id)"
                      class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 text-sm"
                  >
                    PDF
                  </button>
                  <Link
                      :href="route('invoices.edit', inv.id)"
                      class="px-3 py-1.5 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm"
                  >
                    Edit
                  </Link>
                </div>
              </td>
            </tr>

            <tr v-if="!paginatedInvoices.length">
              <td colspan="9" class="py-4 text-gray-400">
                No invoices found
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
