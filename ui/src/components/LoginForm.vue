<script setup>
import { ref } from 'vue';
import { useForm } from 'vee-validate';
import * as yup from 'yup';

const schema = yup.object({
  email: yup.string().required('Email is required').email('Invalid email format'),
  password: yup.string().required('Password is required').min(6, 'Password must be at least 6 characters'),
});

const { handleSubmit, errors, meta } = useForm({
  validationSchema: schema,
});

const onSubmit = handleSubmit((values) => {
  console.log('Login form submitted:', values);
});

const emit = defineEmits(['switch-form']);
</script>

<template>
  <div class="w-full max-w-md p-8 bg-white rounded-xl shadow-lg">
    <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Login</h2>
    <form @submit="onSubmit" class="space-y-6">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
        <input
          type="email"
          name="email"
          class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all duration-200 ease-in-out"
          placeholder="Enter your email"
        />
        <span v-if="meta.touched.email && errors.email" class="mt-1 text-red-500 text-sm block">{{ errors.email }}</span>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
        <input
          type="password"
          name="password"
          class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all duration-200 ease-in-out"
          placeholder="Enter your password"
        />
        <span v-if="meta.touched.password && errors.password" class="mt-1 text-red-500 text-sm block">{{ errors.password }}</span>
      </div>

      <button
        type="submit"
        class="w-full py-3 px-6 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transform transition-all duration-200 ease-in-out hover:scale-[1.02] active:scale-[0.98]"
      >
        Login
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
      Don't have an account?
      <button
        @click="$emit('switch-form')"
        class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors duration-200"
      >
        Register
      </button>
    </p>
  </div>
</template>