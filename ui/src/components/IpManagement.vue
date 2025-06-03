<template>
    <Navbar />

    <div class="flex min-h-screen bg-gray-50 pt-16">
      <!-- Sidebar -->
      <Sidebar />
      <!-- Main content -->
      <div class="flex-1 p-8 overflow-y-auto">
        <div class="min-h-screen bg-gray-50 py-8">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
              <div class="sm:flex-auto">
                <h1 class="text-3xl font-bold text-gray-900">IP Address Management</h1>
                <p class="mt-2 text-sm text-gray-600">Manage your IP addresses with ease</p>
              </div>
              <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 ease-in-out transform hover:scale-105"> Add IP Address </button>
              </div>
            </div>
            <!-- Table -->
            <div class="mt-8 flex flex-col">
              <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle">
                  <div class="overflow-hidden shadow-sm ring-1 ring-black ring-opacity-5 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">IP Address</th>
                          <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Label</th>
                          <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Comment</th>
                          <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Created At</th>
                          <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Actions</span>
                          </th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="ip in ipAddresses" :key="ip.id" class="hover:bg-gray-50 transition-colors duration-150">
                          <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">{{ ip.address }}</td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">{{ ip.label }}</td>
                          <td class="px-3 py-4 text-sm text-gray-500">{{ ip.comment }}</td>
                          <td class="px-3 py-4 text-sm text-gray-500">{{ formatDate(ip.created_at) }}</td>
                          <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-left text-sm font-medium">
                            <button v-if="ip.can_manage" @click="openEditModal(ip)" class="text-indigo-600 hover:text-indigo-900 mr-4 transition-colors duration-200"> Edit </button>
                            <button v-if="ip.can_manage" @click="openDeleteModal(ip)" class="text-red-600 hover:text-red-900 transition-colors duration-200"> Delete </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- Create/Edit Modal -->
            <TransitionRoot appear :show="isCreateModalOpen" as="template">
              <Dialog as="div" class="relative z-10" @close="isCreateModalOpen = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                  <div class="fixed inset-0 bg-black/25 backdrop-blur-sm transition-opacity" />
                </TransitionChild>
                <div class="fixed inset-0 overflow-y-auto">
                  <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 scale-95 translate-y-4" enter-to="opacity-100 scale-100 translate-y-0" leave="ease-in duration-200" leave-from="opacity-100 scale-100 translate-y-0" leave-to="opacity-0 scale-95 translate-y-4">
                      <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                          {{ selectedIp ? 'Edit IP Address' : 'Add IP Address' }}
                        </h3>
                        <form @submit.prevent="handleSubmit" class="space-y-4">
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                            <input type="text" :disabled="selectedIp != null" v-model="formData.address" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all duration-200 ease-in-out px-4 py-2" placeholder="Enter IP address" />
                            <div v-if="serverErrors.address" class="mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-red-600 text-sm pl-4">{{ serverErrors.address[0] }}</p>
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                            <input type="text" v-model="formData.label" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all duration-200 ease-in-out px-4 py-2" placeholder="Enter label" />
                            <div v-if="serverErrors.label" class="mt-2 mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-red-600 text-sm pl-4">{{ serverErrors.label[0] }}</p>
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea v-model="formData.comment" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all duration-200 ease-in-out px-4 py-2" placeholder="Enter comment"></textarea>
                          </div>
                          <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" :disabled="isLoading" @click="isCreateModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"> Cancel </button>
                            <button type="submit" :disabled="isLoading" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                              
                              <span v-if="isLoading">{{ selectedIp ? 'Updating..' : 'Creating..' }}</span>
                              <span v-else>{{ selectedIp ? 'Update' : 'Create' }}</span>
                            </button>
                          </div>
                        </form>
                      </DialogPanel>
                    </TransitionChild>
                  </div>
                </div>
              </Dialog>
            </TransitionRoot>
            <!-- Delete Confirmation Modal -->
            <TransitionRoot appear :show="isDeleteModalOpen" as="template">
              <Dialog as="div" class="relative z-10" @close="isDeleteModalOpen = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                  <div class="fixed inset-0 bg-black/25 backdrop-blur-sm transition-opacity" />
                </TransitionChild>
                <div class="fixed inset-0 overflow-y-auto">
                  <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 scale-95 translate-y-4" enter-to="opacity-100 scale-100 translate-y-0" leave="ease-in duration-200" leave-from="opacity-100 scale-100 translate-y-0" leave-to="opacity-0 scale-95 translate-y-4">
                      <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4"> Delete IP Address </h3>
                        <div class="mt-2">
                          <p class="text-sm text-gray-500"> Are you sure you want to delete this IP address? This action cannot be undone. </p>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                          <button type="button" @click="isDeleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"> Cancel </button>
                          <button type="button" @click="handleDelete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200"> Delete </button>
                        </div>
                      </DialogPanel>
                    </TransitionChild>
                  </div>
                </div>
              </Dialog>
            </TransitionRoot>
          </div>
        </div>
      </div>
    </div>

    <Loader />
  </template>
  <script setup lang="ts">
    import {
      ref,
      onMounted
    } from 'vue';
    import {
      Dialog,
      DialogPanel,
      TransitionChild,
      TransitionRoot
    } from '@headlessui/vue';
    import dayjs from 'dayjs';
    import utc from 'dayjs/plugin/utc';
    import timezone from 'dayjs/plugin/timezone';
    import api from '../plugins/axios';
    import Sidebar from './Sidebar.vue';
    import Navbar from './Navbar.vue';
    import Loader from './Loader.vue';

    const ipAddresses = ref([]);
    const isCreateModalOpen = ref(false);
    const isDeleteModalOpen = ref(false);
    const isLoading = ref(false);
    const selectedIp = ref(null);
    const formData = ref({
      address: '',
      label: '',
      comment: ''
    });
    const serverErrors = ref<Record<string, string[]>>({});
    
    const fetchIpAddresses = async () => {
      try {
        isLoading.value = true;
        const response = await api.get('http://localhost:8000/api/ip-addresses');
        console.log(response);
        ipAddresses.value = response.data.data;
        console.log(ipAddresses.value);
      } catch (error) {
        console.error('Error fetching IP addresses:', error);
      } finally {
        isLoading.value = false;
      }
    };
    const openCreateModal = () => {
      formData.value = {
        address: '',
        label: '',
        comment: ''
      };
      isCreateModalOpen.value = true;
    };
    const openEditModal = (ip: any) => {
      selectedIp.value = ip;
      formData.value = {
        ...ip
      };
      isCreateModalOpen.value = true;
    };
    const openDeleteModal = (ip) => {
      selectedIp.value = ip;
      isDeleteModalOpen.value = true;
    };
    const handleSubmit = async () => {
      try {
        isLoading.value = true;
        if (selectedIp.value) {
          const response = await api.put("http://localhost:8000/api/ip-addresses/" + selectedIp.value.id, formData.value);
        } else {
          const response = await api.post("http://localhost:8000/api/ip-addresses", formData.value);
        }
        // if (response.ok) {
          isCreateModalOpen.value = false;
          await fetchIpAddresses();
        // }
      } catch (error: any) {
        serverErrors.value = error.response?.data?.errors || {};
        console.log(serverErrors);
        console.error('Error saving IP address:', error);
      } finally {
        isLoading.value = false;
      }
    };
    const handleDelete = async () => {
      try {
        const response = await fetch("http://localhost:8000/api/ip-addresses/${selectedIp.value.id}", {
          method: 'DELETE',
        });
        if (response.ok) {
          isDeleteModalOpen.value = false;
          await fetchIpAddresses();
        }
      } catch (error) {
        console.error('Error deleting IP address:', error);
      }
    };
    onMounted(fetchIpAddresses);
    
    const formatDate = (isoString: string): string => {
      dayjs.extend(utc);
      dayjs.extend(timezone);
      return dayjs.utc(isoString).tz('Asia/Manila').format('MMMM DD, YYYY h:mm A');
    };
  </script>
  <style>
    .backdrop-blur-sm {
      backdrop-filter: blur(4px);
    }
  </style>