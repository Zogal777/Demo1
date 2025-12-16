<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  form: Object
});
</script>

<template>
  <section>
    <header class="mb-4">
      <h2 class="text-lg font-medium text-gray-900">Client Information</h2>
      <p class="mt-1 text-sm text-gray-600">Update client details.</p>
    </header>

    <div class="space-y-4">
      <div>
        <InputLabel for="business_name" value="Business Name" />
        <TextInput id="business_name" v-model="props.form.business_name" class="mt-1 block w-full" required />
        <InputError class="mt-2" :message="props.form.errors.business_name" />
      </div>

      <div>
        <InputLabel for="registration_num" value="Registration Number" />
        <TextInput id="registration_num" v-model="props.form.registration_num" class="mt-1 block w-full" />
        <InputError class="mt-2" :message="props.form.errors.registration_num" />
      </div>

      <div>
        <InputLabel for="tax_num" value="PVN" />
        <TextInput id="tax_num" v-model="props.form.tax_num" class="mt-1 block w-full" />
        <InputError class="mt-2" :message="props.form.errors.tax_num" />
      </div>
      <div>
        <InputLabel for="address" value="Address" />
        <TextInput id="address" v-model="props.form.address" class="mt-1 block w-full" />
        <InputError class="mt-2" :message="props.form.errors.address" />
      </div>

      <div>
        <InputLabel for="bank_account" value="Bank Account" />
        <TextInput id="bank_account" v-model="props.form.bank_account" class="mt-1 block w-full" />
        <InputError class="mt-2" :message="props.form.errors.bank_account" />
      </div>

    </div>
  </section>
</template>
