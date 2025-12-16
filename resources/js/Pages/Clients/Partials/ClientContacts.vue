<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, onMounted } from 'vue';

const props = defineProps({
  form: Object,
  isEdit: { type: Boolean, default: false } // true для Edit
});

const showContacts = ref(false);

// Показываем контакты сразу, если edit и есть хотя бы один контакт
onMounted(() => {
  if (props.isEdit && props.form.contacts.length > 0) {
    showContacts.value = true;
  }
});

const addContact = () => {
  props.form.contacts.push({ type: 'email', value: '', comment: '' });
  showContacts.value = true;
};

const removeContact = (index) => {
  props.form.contacts.splice(index, 1);
  if (props.form.contacts.length === 0) showContacts.value = false;
};

const validateContacts = () => {
  let valid = true;

  props.form.contacts.forEach((contact, index) => {
    if (!['email', 'phone'].includes(contact.type)) {
      props.form.errors['contacts.' + index + '.type'] = 'Invalid contact type';
      valid = false;
    } else {
      props.form.errors['contacts.' + index + '.type'] = null;
    }

    if (contact.type === 'email') {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(contact.value)) {
        props.form.errors['contacts.' + index + '.value'] = 'Invalid email address';
        valid = false;
      } else {
        props.form.errors['contacts.' + index + '.value'] = null;
      }
    }

    if (contact.type === 'phone') {
      const phoneRegex = /^[0-9+\-()\s]{5,20}$/;
      if (!phoneRegex.test(contact.value)) {
        props.form.errors['contacts.' + index + '.value'] = 'Invalid phone number';
        valid = false;
      } else {
        props.form.errors['contacts.' + index + '.value'] = null;
      }
    }
  });

  return valid;
};

defineExpose({ validateContacts });
</script>

<template>
  <section>
    <header class="mb-4">
      <h2 class="text-lg font-medium text-gray-900">Contact Persons</h2>
      <p class="mt-1 text-sm text-gray-600">Add or edit contact persons for the client.</p>
    </header>

    <div class="space-y-4">
      <div v-if="showContacts" v-for="(contact, index) in props.form.contacts" :key="index" class="border p-4 rounded">
        <div class="mb-2">
          <InputLabel value="Contact type" />
          <select v-model="contact.type" class="border p-2 rounded w-full">
            <option value="email">Email</option>
            <option value="phone">Phone</option>
          </select>
        </div>

        <div class="mb-2">
          <InputLabel value="Value" />
          <TextInput
              v-model="contact.value"
              class="w-full"
              :type="contact.type === 'email' ? 'email' : 'text'"
              placeholder="Enter value"
          />
          <InputError :message="props.form.errors['contacts.' + index + '.value']" />
        </div>

        <div class="mb-2">
          <InputLabel value="Comment" />
          <TextInput v-model="contact.comment" class="w-full" placeholder="Optional" />
        </div>

        <button
            type="button"
            @click="removeContact(index)"
            class="mt-2 px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Delete
        </button>
      </div>

      <button
          type="button"
          @click="addContact"
          class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-gray-700"
      >
        + Add Contact
      </button>
    </div>
  </section>
</template>
