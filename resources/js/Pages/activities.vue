<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'

const props = defineProps({
  activities: Array,
})

const currentPage = ref(1)

const ROW_HEIGHT = 44
const HEADER_HEIGHT = 44
const ROWS = 11
const rowsPerPage = ROWS

const totalPages = computed(() =>
    Math.ceil(props.activities.length / rowsPerPage)
)

const paginatedActivities = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage
  return props.activities.slice(start, start + rowsPerPage)
})

const tableHeight = computed(() =>
    HEADER_HEIGHT + Math.min(paginatedActivities.value.length, ROWS) * ROW_HEIGHT
)

function restore(activity) {
  let msg = 'Restore this change?'

  if (activity.action === 'created') {
    msg = 'Delete the created record?'
  } else if (activity.action === 'deleted') {
    msg = 'Restore the deleted record?'
  } else if (activity.action === 'updated') {
    msg = 'Revert changes to previous state?'
  }

  if (!confirm(msg)) return
  router.post(route('activities.restore', activity.id))
}

const prevPage = () => currentPage.value > 1 && currentPage.value--
const nextPage = () => currentPage.value < totalPages.value && currentPage.value++
const goToPage = p => (currentPage.value = p)

function actionLabel(action) {
  return {
    created: 'Created',
    updated: 'Updated',
    deleted: 'Deleted',
    restored: 'Restored',
  }[action] || action
}

function actionColor(action) {
  return {
    created: 'bg-green-100 text-green-800',
    updated: 'bg-yellow-100 text-yellow-800',
    deleted: 'bg-red-100 text-red-800',
    restored: 'bg-blue-100 text-blue-800',
  }[action] || 'bg-gray-100 text-gray-800'
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Activity Log" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Activity Log
      </h2>
    </template>

    <div class="flex-1 flex justify-center pt-12 overflow-hidden">
      <div class="w-full max-w-6xl bg-white p-6 shadow rounded flex flex-col">

        <h3 class="text-lg font-semibold mb-4">
          System Activities
        </h3>

        <div
            class="border rounded overflow-hidden"
            :style="{ height: tableHeight + 'px' }"
        >
          <table class="w-full text-sm border-collapse text-center table-fixed">
            <thead class="bg-gray-100 border-b h-[44px]">
            <tr>
              <th class="border px-3 py-2">User</th>
              <th class="border px-3 py-2">Action</th>
              <th class="border px-3 py-2">Entity</th>
              <th class="border px-3 py-2">Changes</th>
              <th class="border px-3 py-2">Date & Time</th>
              <th class="border px-3 py-2 w-32">Actions</th>
            </tr>
            </thead>

            <tbody>
            <tr
                v-for="a in paginatedActivities"
                :key="a.id"
                class="h-[44px] hover:bg-gray-50"
            >
              <td class="border px-3 align-middle truncate">
                {{ a.user_name }}
              </td>

              <td class="border px-3 align-middle">
                  <span
                      class="px-2 py-0.5 rounded text-xs font-medium"
                      :class="actionColor(a.action)"
                  >
                    {{ actionLabel(a.action) }}
                  </span>
              </td>

              <td class="border px-3 align-middle truncate">
                {{ a.entity }} #{{ a.entity_id }}
              </td>

              <!-- CHANGES -->
              <td class="border px-3 align-middle text-center">
                <Link
                    v-if="a.action === 'updated'"
                    :href="route('activities.changes', a.id)"
                    class="px-3 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300"
                >
                  See
                </Link>
                <span v-else class="text-gray-400">—</span>
              </td>

              <td class="border px-3 align-middle">
                {{ a.created_at }}
              </td>

              <td class="border px-3 align-middle">
                <button
                    v-if="a.can_restore"
                    @click="restore(a)"
                    class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 text-sm"
                >
                  Restore
                </button>
                <span v-else class="text-gray-400">—</span>
              </td>
            </tr>

            <tr v-if="!paginatedActivities.length">
              <td colspan="6" class="py-4 text-gray-400">
                No activity records
              </td>
            </tr>
            </tbody>
          </table>
        </div>

        <div
            v-if="totalPages > 1"
            class="pt-4 flex justify-center gap-1 text-sm"
        >
          <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="px-2 py-1 border rounded disabled:opacity-40"
          >
            &lt;
          </button>

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

          <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-2 py-1 border rounded disabled:opacity-40"
          >
            &gt;
          </button>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
