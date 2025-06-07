<template>
  <div class="w-full max-w-md p-8 bg-white rounded-xl shadow-lg">
    <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Login</h2>
    <!-- Success Message -->
    <div v-if="registrationSuccess" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
      <p class="text-green-600 text-sm text-center">Registration successful! Please login with your credentials.</p>
    </div>
    <!-- API Error Message -->
    <div v-if="apiError" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
      <p class="text-red-600 text-sm text-center">{{ apiError }}</p>
    </div>
    <form @submit.prevent="onSubmit" class="space-y-6">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
        <input type="email" name="email" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all duration-200 ease-in-out" placeholder="Enter your email" v-model="email" />
        <div v-if="clientErrors.email" class=" mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ clientErrors.email }}</p>
        </div>
        <div v-if="serverErrors.email" class="mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ serverErrors.email[0] }}</p>
        </div>
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
        <input type="password" name="password" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all duration-200 ease-in-out" placeholder="Enter your password" v-model="password" />
        <div v-if="clientErrors.password" class=" mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ clientErrors.password }}</p>
        </div>
        <div v-if="serverErrors.password" class="mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-600 text-sm pl-4">{{ serverErrors.password[0] }}</p>
        </div>
      </div>
      <button type="submit" :disabled="isLoading" class="w-full py-3 px-6 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transform transition-all duration-200 ease-in-out hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
        <span v-if="isLoading">Logging in...</span>
        <span v-else>Login</span>
      </button>
    </form>
    <p class="mt-6 text-center text-sm text-gray-600"> Don't have an account? <button @click="$emit('switch-form')" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors duration-200"> Register </button>
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
  import {
    useRouter
  } from 'vue-router';
  const router = useRouter();
  const registrationSuccess = inject('registrationSuccess');
  const apiError = ref('');
  const isLoading = ref(false);
  const serverErrors = ref < Record < string,
    string[] >> ({}); // Laravel validation errors
  const schema = yup.object({
    email: yup.string().required('Email is required').email('Invalid email format'),
    password: yup.string().required('Password is required'),
  });
  const {
    handleSubmit,
    errors: clientErrors
  } = useForm({
    validationSchema: schema,
  });
  const {
    value: email
  } = useField('email');
  const {
    value: password
  } = useField('password');
  const onSubmit = handleSubmit(async (values) => {
    try {
      isLoading.value = true;
      apiError.value = '';
      const response = await axios.post('http://localhost:8000/api/auth/login', {
        email: values.email,
        password: values.password,
      });
      console.log(response.status);
      if (response.status == 200) {
        console.log('Login successful:', response.data);
        // Automatically switch to login form after successful registration
        localStorage.setItem('token', response.data.token);
        router.push('/ip-addresses');
      } else {
        apiError.value = 'Invalid email or password. Please try again.';
      }
    } catch (error: any) {
      console.log(error);
      serverErrors.value = error.response?.data?.errors || {};
      apiError.value = 'Invalid email or password. Please try again.';
    } finally {
      isLoading.value = false;
    }
  });
  const emit = defineEmits(['switch-form']);
</script>