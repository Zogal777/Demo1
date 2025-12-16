<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ user: Object });

const form = useForm({
  password: '',
  password_confirmation: '',
});

const updatePassword = () => {
  form.patch(route('users.updatePassword', props.user.id), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
};
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900">
        Update Password
      </h2>
      <p class="mt-1 text-sm text-gray-600">
        Set a new password for this user.
      </p>
    </header>

    <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
      <div>
        <InputLabel value="New Password" />
        <TextInput
            type="password"
            v-model="form.password"
            class="mt-1 block w-full"
        />
        <InputError :message="form.errors.password" />
      </div>

      <div>
        <InputLabel value="Confirm Password" />
        <TextInput
            type="password"
            v-model="form.password_confirmation"
            class="mt-1 block w-full"
        />
      </div>

      <div class="flex items-center gap-4">
        <PrimaryButton :disabled="form.processing">
          Save
        </PrimaryButton>

        <Transition
            enter-active-class="transition ease-in-out duration-300"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out duration-300"
            leave-to-class="opacity-0"
        >
          <p
              v-if="form.recentlySuccessful"
              class="text-sm text-gray-600"
          >
            Saved.
          </p>
        </Transition>
      </div>
    </form>
  </section>
</template>
