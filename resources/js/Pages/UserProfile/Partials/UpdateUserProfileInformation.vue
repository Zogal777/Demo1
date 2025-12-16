<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  user: Object,
  mustVerifyEmail: Boolean,
  status: String,
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  date_of_birth: props.user.date_of_birth,
});

const updateProfile = () => {
  form.patch(route('users.update', props.user.id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900">
        Profile Information
      </h2>
      <p class="mt-1 text-sm text-gray-600">
        Update user's name, email and date of birth.
      </p>
    </header>

    <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
      <div>
        <InputLabel for="name" value="Name" />
        <TextInput id="name" v-model="form.name" class="mt-1 block w-full" />
        <InputError :message="form.errors.name" />
      </div>

      <div>
        <InputLabel for="email" value="Email" />
        <TextInput id="email" v-model="form.email" class="mt-1 block w-full" />
        <InputError :message="form.errors.email" />
      </div>

      <div>
        <InputLabel for="date_of_birth" value="Date of Birth" />
        <TextInput
            id="date_of_birth"
            type="date"
            v-model="form.date_of_birth"
            class="mt-1 block w-full"
        />
        <InputError :message="form.errors.date_of_birth" />
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
