<script setup>
import { Head, usePage } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteProfile from './Partials/DeleteProfile.vue';
import UpdateProfilePassword from './Partials/UpdateProfilePassword.vue';
import UpdateProfileInformation from './Partials/UpdateProfileInformation.vue';
import UpdateClientsForm from './Partials/UpdateClientsForm.vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  mustVerifyEmail: Boolean,
  status: String,
  clients: Array,
  userClients: Array,
});

const profileSectionHeight = ref('400px');

const updateProfileHeight = () => {
  const profileSection = document.querySelector('#profile-info-section');
  if (profileSection) {
    profileSectionHeight.value = profileSection.offsetHeight + 'px';
  }
};

onMounted(() => {
  updateProfileHeight();
  window.addEventListener('resize', updateProfileHeight);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateProfileHeight);
});
</script>

<template>
  <Head title="Profile" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Profile
      </h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

          <div class="bg-white shadow sm:rounded-lg p-6" id="profile-info-section">
            <UpdateProfileInformation
                :must-verify-email="props.mustVerifyEmail"
                :status="props.status"
            />
          </div>

          <div
              class="bg-white shadow sm:rounded-lg p-6 overflow-y-auto"
              :style="{ maxHeight: profileSectionHeight }"
          >
            <UpdateClientsForm
                :clients="props.clients"
                :user-clients="props.userClients"
            />
          </div>

        </div>

        <div class="bg-white shadow sm:rounded-lg p-6">
          <UpdateProfilePassword />
        </div>

        <div class="bg-white shadow sm:rounded-lg p-6">
          <DeleteProfile />
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.bg-white.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.bg-white.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 6px;
}

.bg-white.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: #a0aec0;
  border-radius: 6px;
  border: 2px solid #f1f1f1;
}

.bg-white.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: #a0aec0 #f1f1f1;
}
</style>
