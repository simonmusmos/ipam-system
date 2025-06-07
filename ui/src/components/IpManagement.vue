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
                  <!-- Filters -->
                  <div class="mb-6 p-4 rounded-lg shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                      <!-- User Dropdown -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700">User</label>
                        <select v-model="filters.user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                          <option value="">All Users</option>
                          <option v-for="user in usersCache" :key="user.id" :value="user.id">
                            {{ user.name }}
                          </option>
                        </select>
                      </div>
                      <!-- Address -->
                      <div>
                        <label class="block text-sm font-medium text-gray-700">IP Address</label>
                        <input type="text" v-model="filters.address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Label</label>
                        <input type="text" v-model="filters.label" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                      </div>
                      <!-- Search and Apply -->
                      <div class="md:col-span-4 flex justify-end space-x-2 mt-2">
                        <button @click="fetchIpAddresses()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"> Apply </button>
                        <button @click="resetFilters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"> Reset </button>
                      </div>
                    </div>
                  </div>
                  <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">IP Address</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Label</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Comment</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Created By</th>
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
                        <td class="px-3 py-4 text-sm text-gray-500">{{ usersCache[ip.user_id]?.name || '' }}</td>
                        <td class="px-3 py-4 text-sm text-gray-500">{{ formatDate(ip.created_at) }}</td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-left text-sm font-medium">
                          <button v-if="ip.can_edit" @click="openEditModal(ip)" class="text-indigo-600 hover:text-indigo-900 mr-4 transition-colors duration-200"> Edit </button>
                          <button v-if="ip.can_delete" @click="openDeleteModal(ip)" class="text-red-600 hover:text-red-900 mr-4 transition-colors duration-200"> Delete </button>
                          <button @click="openLogsModal(ip)" class="text-green-600 hover:text-green-900 transition-colors duration-200"> View Logs </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-600"> Page <strong>{{ pagination.current_page }}</strong> of <strong>{{ pagination.last_page }}</strong>
                    </div>
                    <nav class="inline-flex rounded-md shadow-sm isolate" aria-label="Pagination">
                      <button @click="fetchIpAddresses(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 disabled:opacity-50"> ‹ Prev </button>
                      <template v-for="page in visiblePages" :key="page">
                        <button @click="fetchIpAddresses(page)" :class="[
                              'relative inline-flex items-center px-3 py-2 text-sm font-medium border',
                              page === pagination.current_page
                                ? 'z-10 bg-indigo-600 border-indigo-600 text-white'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                            ]">
                          {{ page }}
                        </button>
                      </template>
                      <button @click="fetchIpAddresses(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 disabled:opacity-50"> Next › </button>
                    </nav>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Create/Edit Modal -->
        </div>
      </div>
    </div>
  </div>
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
                  <p class="mt-1 text-xs text-gray-500"> Detected Type: <span class="font-medium">{{ detectedType }}</span>
                  </p>
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
  <!-- Audit Logs Modal -->
  <TransitionRoot appear :show="isLogsModalOpen" as="template">
    <Dialog as="div" class="relative z-10" @close="isLogsModalOpen = false">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-black/25 backdrop-blur-sm transition-opacity" />
      </TransitionChild>
      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
          <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 scale-95 translate-y-4" enter-to="opacity-100 scale-100 translate-y-0" leave="ease-in duration-200" leave-from="opacity-100 scale-100 translate-y-0" leave-to="opacity-0 scale-95 translate-y-4">
            <DialogPanel class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all" style="max-width: 70rem">
              <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4"> Audit Logs </h3>
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Action</th>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">User</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="log in auditLogs" :key="log.id" class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">
                      <!-- Main Description -->
                      <div class="font-medium text-gray-900">{{ log.description }}</div>
                      <!-- Changes displayed underneath -->
                      <ul v-if="log.changes" class="mt-1 ml-4 list-none text-xs text-gray-400">
                        <li v-for="change in parseChanges(log.changes)" :key="change">{{ change }}</li>
                      </ul>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">{{ usersCache[log.user_id]?.name || '' }}</td>
                    <td class="px-3 py-4 text-sm text-gray-500">{{ formatDate(log.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
              <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-600"> Page <strong>{{ logs_pagination.current_page }}</strong> of <strong>{{ logs_pagination.last_page }}</strong>
                </div>
                <nav class="inline-flex rounded-md shadow-sm isolate" aria-label="Pagination">
                  <button @click="openLogsModal(selectedIp, logs_pagination.current_page - 1)" :disabled="logs_pagination.current_page === 1" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 disabled:opacity-50"> ‹ Prev </button>
                  <template v-for="page in visibleLogPages" :key="page">
                    <button @click="openLogsModal(selectedIp, page)" :class="[
                                  'relative inline-flex items-center px-3 py-2 text-sm font-medium border',
                                  page === logs_pagination.current_page
                                    ? 'z-10 bg-indigo-600 border-indigo-600 text-white'
                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                ]">
                      {{ page }}
                    </button>
                  </template>
                  <button @click="openLogsModal(selectedIp, logs_pagination.current_page + 1)" :disabled="logs_pagination.current_page === logs_pagination.last_page" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 disabled:opacity-50"> Next › </button>
                </nav>
              </div>
              <div class="mt-6 flex justify-end space-x-3">
                <button type="button" @click="isLogsModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"> Cancel </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
  <Loader />
</template>
<script setup lang="ts">
  import {
    ref,
    onMounted,
    computed
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
  const auditLogs = ref([]);
  const isCreateModalOpen = ref(false);
  const isDeleteModalOpen = ref(false);
  const isLogsModalOpen = ref(false);
  const isLoading = ref(false);
  const selectedIp = ref(null);
  const formData = ref({
    address: '',
    label: '',
    comment: ''
  });
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
  });
  const logs_pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 5,
  });
  const usersCache = ref < Record < number,
    {
      id: number;name: string
    } >> ({});
  const fetchUsers = async () => {
    try {
      const response = await api.get('http://localhost:8000/api/auth/users');
      for (const user of response.data) {
        usersCache.value[user.id] = user;
      }
    } catch (error) {
      console.error('Failed to fetch users:', error);
    }
  };
  const serverErrors = ref < Record < string,
    string[] >> ({});
  const fetchIpAddresses = async (page = 1) => {
    try {
      isLoading.value = true;
      const response = await api.get(`http://localhost:8000/api/ip-addresses`, {
        params: {
          page,
          user: filters.value.user_id || undefined,
          address: filters.value.address || undefined,
          label: filters.value.label || undefined,
        },
      });
      console.log(response);
      ipAddresses.value = response.data.data;
      pagination.value.current_page = response.data.current_page;
      pagination.value.last_page = response.data.last_page;
      pagination.value.per_page = response.data.per_page;
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
  const openDeleteModal = (ip: any) => {
    selectedIp.value = ip;
    isDeleteModalOpen.value = true;
  };
  const openLogsModal = async (ip: any, page = 1) => {
    try {
      isLoading.value = true;
      selectedIp.value = ip;
      const response = await api.get(`http://localhost:8000/api/logs?page=${page}&per_page=${logs_pagination.value.per_page}&model_id=${ip.id}&model=IpAddress`);
      auditLogs.value = response.data.data;
      logs_pagination.value.current_page = response.data.current_page;
      logs_pagination.value.last_page = response.data.last_page;
      logs_pagination.value.per_page = response.data.per_page;
    } catch (error) {
      console.error('Error fetching IP addresses:', error);
    } finally {
      isLoading.value = false;
      isLogsModalOpen.value = true;
    }
  };
  const handleSubmit = async () => {
    try {
      isLoading.value = true;
      if (selectedIp.value) {
        const response = await api.put("http://localhost:8000/api/ip-addresses/" + selectedIp.value.id, formData.value);
      } else {
        const response = await api.post("http://localhost:8000/api/ip-addresses", formData.value);
      }
      isCreateModalOpen.value = false;
    } catch (error: any) {
      serverErrors.value = error.response?.data?.errors || {};
      console.log(serverErrors);
      console.error('Error saving IP address:', error);
    } finally {
      isLoading.value = false;
      await fetchIpAddresses();
    }
  };
  const handleDelete = async () => {
    if (!selectedIp.value) {
      return false;
    }
    try {
      isLoading.value = true;
      const response = await api.delete("http://localhost:8000/api/ip-addresses/" + selectedIp.value.id);
    } catch (error) {
      console.error('Error deleting IP address:', error);
    } finally {
      isDeleteModalOpen.value = false;
      isLoading.value = false;
      await fetchIpAddresses();
    }
  };
  onMounted(async () => {
    await fetchUsers(); // Ensures users are loaded first
    await fetchIpAddresses(); // Then loads IPs after users
  });
  const formatDate = (isoString: string): string => {
    dayjs.extend(utc);
    dayjs.extend(timezone);
    return dayjs.utc(isoString).tz('Asia/Manila').format('MMMM DD, YYYY h:mm A');
  };
  const visiblePages = computed(() => {
    const pages = [];
    const total = pagination.value.last_page;
    const current = pagination.value.current_page;
    let start = Math.max(current - 2, 1);
    let end = Math.min(current + 2, total);
    if (current <= 3) {
      end = Math.min(5, total);
    } else if (current >= total - 2) {
      start = Math.max(total - 4, 1);
    }
    for (let i = start; i <= end; i++) {
      pages.push(i);
    }
    return pages;
  });
  const visibleLogPages = computed(() => {
    const pages = [];
    const total = logs_pagination.value.last_page;
    const current = logs_pagination.value.current_page;
    let start = Math.max(current - 2, 1);
    let end = Math.min(current + 2, total);
    if (current <= 3) {
      end = Math.min(5, total);
    } else if (current >= total - 2) {
      start = Math.max(total - 4, 1);
    }
    for (let i = start; i <= end; i++) {
      pages.push(i);
    }
    return pages;
  });
  const parseChanges = (changes: string) => {
    return changes.split(', ');
  };
  // Live-detect IP type in frontend for display
  const detectedType = computed(() => {
    const ip = formData.value.address.trim();
    if (!ip) return '';
    const ipv4Regex = /^(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)){3}$/;
    const ipv6Regex = /^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|::1|::)$/;
    if (ipv4Regex.test(ip)) return 'IPv4';
    if (ipv6Regex.test(ip)) return 'IPv6';
    return 'Invalid IP';
  });
  const filters = ref({
    user_id: '',
    address: '',
    label: '',
  });
  const resetFilters = () => {
    filters.value = {
      user_id: '',
      address: '',
      label: '',
    };
    fetchIpAddresses();
  };
</script>
<style>
  .backdrop-blur-sm {
    backdrop-filter: blur(4px);
  }
</style>