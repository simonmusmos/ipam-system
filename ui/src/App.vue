<script setup>
import { ref } from 'vue';
import LoginForm from './components/LoginForm.vue';
import RegisterForm from './components/RegisterForm.vue';

const showLogin = ref(true);

const toggleForm = () => {
  showLogin.value = !showLogin.value;
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h1 class="text-center text-3xl font-extrabold text-gray-900 mb-8">
        {{ showLogin ? 'Welcome Back' : 'Create Account' }}
      </h1>
      <Transition name="fade" mode="out-in">
        <LoginForm v-if="showLogin" @switch-form="toggleForm" />
        <RegisterForm v-else @switch-form="toggleForm" />
      </Transition>
    </div>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>