<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import axios from 'axios'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  invoice: Object
})

const form = useForm({
  invoice_date: props.invoice.invoice_date,
  payment_date: props.invoice.payment_date,

  company_name: props.invoice.company_name,
  company_address: props.invoice.company_address,
  company_bank: props.invoice.company_bank,

  client_name: props.invoice.client_name,
  client_address: props.invoice.client_address,
  client_bank: props.invoice.client_bank,

  items: props.invoice.items.map(i => ({
    name: i.item,
    description: i.description,
    quantity: i.quantity,
    price: i.price,
    pvn: i.pvn
  }))
})

const companyList = ref([])
const clientList = ref([])
const serviceList = ref([])

const companyFocused = ref(false)
const clientFocused = ref(false)
const itemFocused = ref(null)

/* ===================== COMPANY ===================== */
async function searchCompany(val) {
  if (!companyFocused.value || !val) {
    companyList.value = []
    return
  }

  const res = await axios.get(route('clients.search'), {
    params: { q: val }
  })
  companyList.value = res.data
}

function selectCompany(c) {
  form.company_name = c.business_name
  form.company_address = c.address
  form.company_bank = c.bank_account
  companyFocused.value = false
  companyList.value = []
}

/* ===================== CLIENT ===================== */
async function searchClient(val) {
  if (!clientFocused.value || !val) {
    clientList.value = []
    return
  }

  const res = await axios.get(route('clients.search'), {
    params: { q: val }
  })
  clientList.value = res.data
}

function selectClient(c) {
  form.client_name = c.business_name
  form.client_address = c.address
  form.client_bank = c.bank_account
  clientFocused.value = false
  clientList.value = []
}

/* ===================== SERVICES (ITEMS) ===================== */
/**
 * ВАЖНО:
 * правильный роут из web.php
 */
async function searchService(val) {
  if (!val || val.length < 1) {
    serviceList.value = []
    return
  }

  const res = await axios.get(
      route('invoices.search.services'),
      { params: { q: val } }
  )

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
function handleClickOutside(e) {
  if (!e.target.closest('.company-box')) {
    companyFocused.value = false
    companyList.value = []
  }

  if (!e.target.closest('.client-box')) {
    clientFocused.value = false
    clientList.value = []
  }

  if (!e.target.closest('.item-box')) {
    itemFocused.value = null
    serviceList.value = []
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() =>
    document.removeEventListener('click', handleClickOutside)
)

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
    form.items.reduce(
        (s, i) => s + Number(i.price) * Number(i.quantity),
        0
    )
)

const totalTax = computed(() =>
    form.items.reduce(
        (s, i) =>
            s +
            Number(i.price) *
            Number(i.quantity) *
            (Number(i.pvn) / 100),
        0
    )
)

const totalWithTax = computed(
    () => totalWithoutTax.value + totalTax.value
)

/* ===================== UPDATE ===================== */
function updateInvoice() {
  form.patch(route('invoices.update', props.invoice.id), {
    preserveScroll: true,
    data: {
      ...form.data(),
      total_without_pvn: totalWithoutTax.value,
      total_pvn: totalTax.value,
      total_sum: totalWithTax.value
    }
  })
}

/* ===================== DELETE ===================== */
const confirmingInvoiceDeletion = ref(false)

function confirmInvoiceDeletion() {
  confirmingInvoiceDeletion.value = true
  nextTick(() => {})
}

function deleteInvoice() {
  form.delete(route('invoices.destroy', props.invoice.id), {
    preserveScroll: true,
    onSuccess: closeModal
  })
}

function closeModal() {
  confirmingInvoiceDeletion.value = false
  form.clearErrors()
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Invoice" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Edit Invoice — {{ invoice.invoice_number }}
      </h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-6xl space-y-6">

        <!-- INVOICE INFO -->
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

        <!-- COMPANY / CLIENT -->
        <div class="grid grid-cols-2 gap-6">

          <!-- COMPANY -->
          <div class="bg-white shadow p-6 rounded company-box relative">
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
                class="absolute left-0 right-0 bg-white border shadow rounded mt-1 z-20"
            >
              <li
                  v-for="c in companyList"
                  :key="c.id"
                  @mousedown.prevent="selectCompany(c)"
                  class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
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
          <div class="bg-white shadow p-6 rounded client-box relative">
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
                class="absolute left-0 right-0 bg-white border shadow rounded mt-1 z-20"
            >
              <li
                  v-for="c in clientList"
                  :key="c.id"
                  @mousedown.prevent="selectClient(c)"
                  class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
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
              <td class="border p-1">
                <div class="relative item-box">
                  <input
                      v-model="item.name"
                      @focus="itemFocused = index"
                      @input="searchService(item.name)"
                      class="input w-full"
                  />

                  <ul
                      v-if="itemFocused === index && serviceList.length"
                      class="absolute left-0 right-0 bg-white border shadow rounded mt-1 z-30"
                  >
                    <li
                        v-for="s in serviceList"
                        :key="s.id"
                        @mousedown.prevent="selectService(s, index)"
                        class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
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

              <td class="border p-1 text-center">{{ item.pvn }} %</td>

              <td class="border p-1 text-center">
                {{
                  (
                      Number(item.price) * Number(item.quantity) +
                      Number(item.price) *
                      Number(item.quantity) *
                      (Number(item.pvn) / 100)
                  ).toFixed(2)
                }} €
              </td>

              <td class="border p-1 text-center">
                <button class="text-red-600" @click="removeItem(index)">🗑</button>
              </td>
            </tr>
            </tbody>
          </table>

          <button @click="addItem" class="mt-3 bg-gray-800 text-white px-3 py-1 rounded">
            + Add item
          </button>

          <div class="mt-6 text-right text-sm">
            <div>Total without tax: <b>{{ totalWithoutTax.toFixed(2) }} €</b></div>
            <div>PVN: <b>{{ totalTax.toFixed(2) }} €</b></div>
            <div class="text-lg">Total: <b>{{ totalWithTax.toFixed(2) }} €</b></div>
          </div>
        </div>

        <!-- ACTIONS -->
        <div class="flex justify-end gap-3">
          <DangerButton @click="confirmInvoiceDeletion">
            Delete Invoice
          </DangerButton>

          <button
              @click="router.visit(route('invoices.index'))"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Cancel
          </button>

          <button
              @click="updateInvoice"
              :disabled="form.processing"
              class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
          >
            Update Invoice
          </button>
        </div>

      </div>
    </div>

    <!-- DELETE MODAL -->
    <Modal :show="confirmingInvoiceDeletion" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Are you sure?
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          This invoice will be permanently deleted. This action cannot be undone.
        </p>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal">
            Cancel
          </SecondaryButton>

          <DangerButton
              class="ml-3"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="deleteInvoice"
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
