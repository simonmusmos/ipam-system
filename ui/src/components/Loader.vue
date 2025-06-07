<script setup>
  import {
    isLoading
  } from '../stores/loading';
</script>
<template>
  <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-white/40 backdrop-blur-md">
    <div class="loader"></div>
  </div>
</template>
<style>
  .loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #6366f1;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>