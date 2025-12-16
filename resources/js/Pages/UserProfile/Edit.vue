<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUser from '@/Pages/UserProfile/Partials/DeleteUser.vue';
import UpdateUserProfileInformation from '@/Pages/UserProfile/Partials/UpdateUserProfileInformation.vue';
import UpdateUserPassword from '@/Pages/UserProfile/Partials/UpdateUserPassword.vue';

const props = defineProps({
  user: Object,               // ✅ ДОБАВИЛИ
  mustVerifyEmail: Boolean,
  status: String,
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
  <Head title="Edit User" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Profile — {{ props.user.name }} (ID: {{ props.user.id }})
      </h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

          <div class="bg-white shadow sm:rounded-lg p-6 lg:col-span-2" id="profile-info-section">
            <UpdateUserProfileInformation
                :user="props.user"
                :must-verify-email="props.mustVerifyEmail"
                :status="props.status"
            />
          </div>

          <div class="bg-white shadow sm:rounded-lg p-6 lg:col-span-2">
            <UpdateUserPassword :user="props.user" />
          </div>

          <div class="bg-white shadow sm:rounded-lg p-6 lg:col-span-2">
            <DeleteUser :user="props.user" />
          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
