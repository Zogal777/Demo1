<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  activity: Object,
})

function formatValue(val) {
  if (val === null || val === undefined) return '—'
  if (typeof val === 'boolean') return val ? 'Yes' : 'No'
  return String(val)
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Activity Changes" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Changes Details
      </h2>
    </template>

    <div class="flex-1 flex justify-center pt-12">
      <div class="w-full max-w-4xl bg-white p-6 shadow rounded">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-lg font-semibold">
              {{ activity.entity }} #{{ activity.entity_id }}
            </h3>
            <p class="text-sm text-gray-500">
              Updated by {{ activity.user_name }} · {{ activity.created_at }}
            </p>
          </div>

          <Link
              :href="route('activities.index')"
              class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm"
          >
            ← Back
          </Link>
        </div>

        <!-- CHANGES TABLE -->
        <div class="border rounded overflow-hidden">
          <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-100">
            <tr>
              <th class="border px-3 py-2 text-left w-1/4">Field</th>
              <th class="border px-3 py-2 text-left">Before</th>
              <th class="border px-3 py-2 text-left">After</th>
            </tr>
            </thead>

            <tbody>
            <tr
                v-for="(oldValue, field) in activity.before"
                :key="field"
                class="hover:bg-gray-50"
            >
              <td class="border px-3 py-2 font-medium">
                {{ field }}
              </td>

              <td class="border px-3 py-2 text-red-600">
                {{ formatValue(oldValue) }}
              </td>

              <td class="border px-3 py-2 text-green-600">
                {{ formatValue(activity.after[field]) }}
              </td>
            </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
