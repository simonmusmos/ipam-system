<template>
  <div class="w-full max-w-md p-8 bg-white rounded-xl shadow-lg">
    <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Register</h2>
    <form @submit.prevent="onSubmit" class="space-y-6">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
        <input v-model="name" type="text" name="name" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none" placeholder="Enter your name" />
        <div v-if="clientErrors.name" class=" mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ clientErrors.name }}</p>
        </div>
        <div v-if="serverErrors.name" class="mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ serverErrors.name[0] }}</p>
        </div>
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
        <input v-model="email" type="email" name="email" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none" placeholder="Enter your email" />
        <div v-if="clientErrors.email" class=" mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ clientErrors.email }}</p>
        </div>
        <div v-if="serverErrors.email" class="mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ serverErrors.email[0] }}</p>
        </div>
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
        <input v-model="password" type="password" name="password" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none" placeholder="Create a password" />
        <div v-if="clientErrors.password" class=" mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ clientErrors.password }}</p>
        </div>
        <div v-if="serverErrors.password" class="mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ serverErrors.password[0] }}</p>
        </div>
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
        <input v-model="password_confirmation" type="password" name="password_confirmation" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none" placeholder="Confirm your password" />
        <div v-if="clientErrors.password_confirmation" class="mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ clientErrors.password_confirmation }}</p>
        </div>
        <div v-if="serverErrors.password_confirmation" class="mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ serverErrors.password_confirmation[0] }}</p>
        </div>
      </div>
      <button type="submit" :disabled="isLoading" class="w-full py-3 px-6 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transform transition-all duration-200 ease-in-out hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
        <span v-if="isLoading">Registering...</span>
        <span v-else>Register</span>
      </button>
    </form>
    <p class="mt-6 text-center text-sm text-gray-600"> Already have an account? <button @click="$emit('switch-form')" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors duration-200"> Login </button>
    </p>
  </div>
</template>
<script setup lang="ts">
  import {
    ref,
    inject
  } from 'vue';
  import {
    useForm,
    useField
  } from 'vee-validate';
  import * as yup from 'yup';
  import axios from 'axios';
  const registrationSuccess = inject('registrationSuccess');
  const apiError = ref('');
  const isLoading = ref(false);
  const serverErrors = ref < Record < string,
    string[] >> ({}); // Laravel validation errors
  // Yup validation schema
  const schema = yup.object({
    name: yup.string().required('Name is required'),
    email: yup.string().required('Email is required').email('Email is not valid'),
    password: yup.string().required('Password is required').min(6, 'Password must be at least 6 characters'),
    password_confirmation: yup.string().required('Please confirm your password').oneOf([yup.ref('password')], 'Passwords must match'),
  });
  // Setup VeeValidate form
  const {
    handleSubmit,
    errors: clientErrors
  } = useForm({
    validationSchema: schema,
  });
  // Form fields
  const {
    value: name
  } = useField('name');
  const {
    value: email
  } = useField('email');
  const {
    value: password
  } = useField('password');
  const {
    value: password_confirmation
  } = useField('password_confirmation');
  // Emit switch-form
  const emit = defineEmits<{
    (e: 'switch-form'): void
  }>();
  // Submit handler
  const onSubmit = handleSubmit(async (values) => {
    isLoading.value = true;
    apiError.value = '';
    serverErrors.value = {};
    console.log("here");
    try {
      await axios.post('http://localhost:8000/api/auth/register', {
        name: values.name,
        email: values.email,
        password: values.password,
        password_confirmation: values.password_confirmation,
      });
      (registrationSuccess as any).value = true;

      emit('switch-form');
      
    } catch (error: any) {
      serverErrors.value = error.response?.data?.errors || {};
      apiError.value = error.response?.data?.message || 'Registration failed. Please try again.';
    } finally {
      isLoading.value = false;
    }
  });
</script>