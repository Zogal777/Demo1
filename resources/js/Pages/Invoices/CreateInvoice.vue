<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const form = useForm({
  invoice_date: new Date().toISOString().slice(0, 10),
  payment_date: new Date().toISOString().slice(0, 10),

  company_name: '',
  company_address: '',
  company_bank: '',

  client_name: '',
  client_address: '',
  client_bank: '',

  items: [
    { name: '', description: '', quantity: 1, price: 0, pvn: 21 }
  ]
})

const companyList = ref([])
const clientList = ref([])
const serviceList = ref([])

const companyFocused = ref(false)
const clientFocused = ref(false)
const itemFocused = ref(null)

/* ===================== COMPANY SEARCH ===================== */
async function searchCompany(val) {
  if (!companyFocused.value || !val || val.length < 1) {
    companyList.value = []
    return
  }
  const res = await axios.get(route('clients.search'), { params: { q: val } })
  companyList.value = res.data
}

function selectCompany(c) {
  form.company_name = c.business_name
  form.company_address = c.address
  form.company_bank = c.bank_account
  companyList.value = []
  companyFocused.value = false
}

/* ===================== CLIENT SEARCH ===================== */
async function searchClient(val) {
  if (!clientFocused.value || !val || val.length < 1) {
    clientList.value = []
    return
  }
  const res = await axios.get(route('clients.search'), { params: { q: val } })
  clientList.value = res.data
}

function selectClient(c) {
  form.client_name = c.business_name
  form.client_address = c.address
  form.client_bank = c.bank_account
  clientList.value = []
  clientFocused.value = false
}

/* ===================== SERVICE SEARCH (ITEMS) ===================== */
/**
 * ВАЖНО: правильный роут из твоего web.php:
 * invoices.search.services
 */
async function searchService(val) {
  if (!val || val.length < 1) {
    serviceList.value = []
    return
  }

  const res = await axios.get(route('invoices.search.services'), {
    params: { q: val }
  })
  serviceList.value = res.data
}

function selectService(s, index) {
  form.items[index].name = s.item_name
  form.items[index].description = s.description || ''
  form.items[index].price = s.price || 0

  itemFocused.value = null
  serviceList.value = []
}

/* ===================== CLICK OUTSIDE ===================== */
function clickOutsideCompany(e) {
  if (!e.target.closest('.company-box')) {
    companyFocused.value = false
    companyList.value = []
  }
}

function clickOutsideClient(e) {
  if (!e.target.closest('.client-box')) {
    clientFocused.value = false
    clientList.value = []
  }
}

/**
 * КЛЮЧЕВОЕ ИСПРАВЛЕНИЕ:
 * Мы теперь держим .item-box на div вокруг input+dropdown,
 * поэтому closest('.item-box') корректно работает и не закрывает список раньше времени.
 */
function clickOutsideItem(e) {
  if (!e.target.closest('.item-box')) {
    itemFocused.value = null
    serviceList.value = []
  }
}

onMounted(() => {
  document.addEventListener('click', clickOutsideCompany)
  document.addEventListener('click', clickOutsideClient)
  document.addEventListener('click', clickOutsideItem)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', clickOutsideCompany)
  document.removeEventListener('click', clickOutsideClient)
  document.removeEventListener('click', clickOutsideItem)
})

/* ===================== ITEMS ===================== */
function addItem() {
  form.items.push({ name: '', description: '', quantity: 1, price: 0, pvn: 21 })
}

function removeItem(i) {
  form.items.splice(i, 1)
  if (itemFocused.value === i) {
    itemFocused.value = null
    serviceList.value = []
  }
}

/* ===================== TOTALS ===================== */
const totalWithoutTax = computed(() =>
    form.items.reduce((s, i) => s + Number(i.price) * Number(i.quantity), 0)
)

const totalTax = computed(() =>
    form.items.reduce(
        (s, i) => s + (Number(i.price) * Number(i.quantity)) * (Number(i.pvn) / 100),
        0
    )
)

const totalWithTax = computed(() => totalWithoutTax.value + totalTax.value)

/* ===================== SAVE ===================== */
function saveInvoice() {
  const payload = {
    invoice_date: form.invoice_date,
    payment_date: form.payment_date,

    company_name: form.company_name,
    company_address: form.company_address,
    company_bank: form.company_bank,

    client_name: form.client_name,
    client_address: form.client_address,
    client_bank: form.client_bank,

    items: form.items.map(i => ({
      name: i.name,
      description: i.description,
      quantity: Number(i.quantity),
      price: Number(i.price),
      pvn: Number(i.pvn),
    })),

    total_without_pvn: totalWithoutTax.value,
    total_pvn: totalTax.value,
    total_sum: totalWithTax.value
  }

  form.post(route('invoices.store'), { data: payload })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Create Invoice" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Create Invoice
      </h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-6xl space-y-6">

        <!-- Invoice Info -->
        <div class="bg-white shadow p-6 rounded">
          <h3 class="font-semibold mb-4">Invoice Info</h3>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label>Invoice Date</label>
              <input type="date" v-model="form.invoice_date" class="input w-full" />
            </div>

            <div>
              <label>Payment Date</label>
              <input type="date" v-model="form.payment_date" class="input w-full" />
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <!-- COMPANY -->
          <div class="bg-white shadow p-6 rounded relative company-box">
            <h3 class="font-semibold mb-4">Company Info</h3>

            <label>Company</label>
            <input
                v-model="form.company_name"
                @focus="companyFocused = true"
                @input="searchCompany(form.company_name)"
                class="input w-full"
            />

            <ul
                v-if="companyFocused && companyList.length"
                class="absolute left-0 right-0 bg-white border rounded shadow mt-1 z-20"
            >
              <li
                  v-for="c in companyList"
                  :key="c.id"
                  class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                  @mousedown.prevent="selectCompany(c)"
              >
                {{ c.business_name }}
              </li>
            </ul>

            <label class="mt-2">Address</label>
            <input v-model="form.company_address" class="input w-full" />

            <label class="mt-2">Bank Account</label>
            <input v-model="form.company_bank" class="input w-full" />
          </div>

          <!-- CLIENT -->
          <div class="bg-white shadow p-6 rounded relative client-box">
            <h3 class="font-semibold mb-4">Client Info</h3>

            <label>Client</label>
            <input
                v-model="form.client_name"
                @focus="clientFocused = true"
                @input="searchClient(form.client_name)"
                class="input w-full"
            />

            <ul
                v-if="clientFocused && clientList.length"
                class="absolute left-0 right-0 bg-white border rounded shadow mt-1 z-20"
            >
              <li
                  v-for="c in clientList"
                  :key="c.id"
                  class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                  @mousedown.prevent="selectClient(c)"
              >
                {{ c.business_name }}
              </li>
            </ul>

            <label class="mt-2">Address</label>
            <input v-model="form.client_address" class="input w-full" />

            <label class="mt-2">Bank Account</label>
            <input v-model="form.client_bank" class="input w-full" />
          </div>
        </div>

        <!-- ITEMS -->
        <div class="bg-white shadow p-6 rounded">
          <h3 class="font-semibold mb-2">Items</h3>

          <table class="table-auto w-full border text-sm">
            <thead class="bg-gray-100">
            <tr>
              <th class="border px-2 py-1">Name</th>
              <th class="border px-2 py-1">Description</th>
              <th class="border px-2 py-1">Qty</th>
              <th class="border px-2 py-1">Price</th>
              <th class="border px-2 py-1">PVN</th>
              <th class="border px-2 py-1">Total</th>
              <th class="border px-2 py-1"></th>
            </tr>
            </thead>

            <tbody>
            <tr
                v-for="(item, index) in form.items"
                :key="index"
                class="hover:bg-gray-50"
            >
              <!-- NAME + DROPDOWN -->
              <td class="border p-1">
                <!-- КЛЮЧ: item-box на wrapper div (а не на tr), чтобы clickOutside работал -->
                <div class="relative item-box">
                  <input
                      v-model="item.name"
                      @focus="itemFocused = index"
                      @input="searchService(item.name)"
                      class="input w-full"
                  />

                  <ul
                      v-if="itemFocused === index && serviceList.length"
                      class="absolute left-0 right-0 bg-white border rounded shadow mt-1 z-30"
                  >
                    <li
                        v-for="s in serviceList"
                        :key="s.id"
                        class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                        @mousedown.prevent="selectService(s, index)"
                    >
                      {{ s.item_name }}
                    </li>
                  </ul>
                </div>
              </td>

              <td class="border p-1">
                <input v-model="item.description" class="input w-full" />
              </td>

              <td class="border p-1">
                <input type="number" v-model="item.quantity" class="input w-full" />
              </td>

              <td class="border p-1">
                <input type="number" v-model="item.price" class="input w-full" />
              </td>

              <td class="border p-1 text-center">
                {{ item.pvn }} %
              </td>

              <td class="border p-1 text-center">
                {{
                  (
                      Number(item.price) * Number(item.quantity) +
                      (Number(item.price) * Number(item.quantity)) * (Number(item.pvn) / 100)
                  ).toFixed(2)
                }} €
              </td>

              <td class="border p-1 text-center">
                <button class="text-red-600" @click="removeItem(index)">🗑</button>
              </td>
            </tr>

            <tr v-if="!form.items.length">
              <td colspan="7" class="text-center py-4 text-gray-400">
                No items
              </td>
            </tr>
            </tbody>
          </table>

          <button
              @click="addItem"
              class="mt-3 bg-gray-800 text-white px-3 py-1 rounded"
          >
            + Add item
          </button>

          <div class="mt-6 text-right text-sm">
            <div>Total without tax: <b>{{ totalWithoutTax.toFixed(2) }} €</b></div>
            <div>PVN: <b>{{ totalTax.toFixed(2) }} €</b></div>
            <div class="text-lg">Total: <b>{{ totalWithTax.toFixed(2) }} €</b></div>
          </div>
        </div>

        <div class="flex justify-end gap-3">
          <button
              @click="router.visit(route('invoices.index'))"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Cancel
          </button>

          <button
              @click="saveInvoice"
              :disabled="form.processing"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Create Invoice
          </button>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.input {
  border: 1px solid #ccc;
  padding: 6px;
  border-radius: 6px;
}
</style>
