<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  clients: {
    type: Array,
    required: true,
  },
  userClients: {
    type: Array,
    required: true,
  },
});

const form = useForm({
  clients: [...props.userClients],
});

const containerHeight = ref('400px');

const updateContainerHeight = () => {
  const profileSection = document.querySelector('#profile-info-section');
  if (profileSection) {
    containerHeight.value = profileSection.offsetHeight + 'px';
  }
};

onMounted(() => {
  updateContainerHeight();
  window.addEventListener('resize', updateContainerHeight);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateContainerHeight);
});

const saveClients = () => {
  form.patch(route('profile.updateClients'), { preserveScroll: true });
};
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900">
        Clients
      </h2>
      <p class="mt-1 text-sm text-gray-600">
        Choose your clients.
      </p>
    </header>

    <form @submit.prevent="saveClients" class="mt-6 space-y-4">
      <div
          :style="{ maxHeight: containerHeight }"
          class="border border-gray-200 rounded overflow-y-auto scrollbar-thin transition-all duration-300"
      >
        <table class="min-w-full">
          <thead>
          <tr class="bg-gray-100 sticky top-0">
            <th class="py-2 px-4 text-left">Select</th>
            <th class="py-2 px-4 text-left">Business Name</th>
            <th class="py-2 px-4 text-left">Registration Number</th>
            <th class="py-2 px-4 text-left">PVN</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="client in props.clients" :key="client.id" class="border-t">
            <td class="py-2 px-4">
              <input
                  type="checkbox"
                  :value="client.id"
                  v-model="form.clients"
                  class="rounded border-gray-300"
              />
            </td>
            <td class="py-2 px-4">{{ client.business_name }}</td>
            <td class="py-2 px-4">{{ client.registration_num }}</td>
            <td class="py-2 px-4">{{ client.tax_num }}</td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center gap-4 mt-4">
        <PrimaryButton :disabled="form.processing">Save Clients</PrimaryButton>
        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
      </div>
    </form>
  </section>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 6px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: #a0aec0;
  border-radius: 6px;
  border: 2px solid #f1f1f1;
}

.scrollbar-thin {
  scrollbar-width: thin;
  scrollbar-color: #a0aec0 #f1f1f1;
}
</style>
