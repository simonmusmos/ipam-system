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
                <h1 class="text-3xl font-bold text-gray-900">Audit Logs</h1>
                <p class="mt-2 text-sm text-gray-600">View logs all logs here</p>
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
                          <select
                            v-model="filters.user_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                          >
                            <option value="">All Users</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                              {{ user.name }}
                            </option>
                          </select>
                        </div>

                        <!-- Date Range -->
                        <div>
                          <label class="block text-sm font-medium text-gray-700">From</label>
                          <input type="date" v-model="filters.start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700">To</label>
                          <input type="date" v-model="filters.end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>

                        <!-- Search and Apply -->
                        <div class="md:col-span-4 flex justify-end space-x-2 mt-2">
                          <button @click="fetchAuditLogs()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Apply
                          </button>
                          <button @click="resetFilters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Reset
                          </button>
                        </div>
                      </div>
                    </div>
                    <table class="min-w-full divide-y divide-gray-300">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Action</th>
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
                          <td class="px-3 py-4 text-sm text-gray-500">{{ formatDate(log.created_at) }}</td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="mt-6 flex items-center justify-between">
                      <div class="text-sm text-gray-600">
                        Page <strong>{{ pagination.current_page }}</strong> of <strong>{{ pagination.last_page }}</strong>
                      </div>

                      <nav class="inline-flex rounded-md shadow-sm isolate" aria-label="Pagination">
                        <button
                          @click="fetchAuditLogs(pagination.current_page - 1)"
                          :disabled="pagination.current_page === 1"
                          class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 disabled:opacity-50"
                        >
                          ‹ Prev
                        </button>

                        <template v-for="page in visiblePages" :key="page">
                          <button
                            @click="fetchAuditLogs(page)"
                            :class="[
                              'relative inline-flex items-center px-3 py-2 text-sm font-medium border',
                              page === pagination.current_page
                                ? 'z-10 bg-indigo-600 border-indigo-600 text-white'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                            ]"
                          >
                            {{ page }}
                          </button>
                        </template>

                        <button
                          @click="fetchAuditLogs(pagination.current_page + 1)"
                          :disabled="pagination.current_page === pagination.last_page"
                          class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 disabled:opacity-50"
                        >
                          Next ›
                        </button>
                      </nav>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Loader />
  </template>
  <script setup lang="ts">
    import {
      ref,
      onMounted,
      computed
    } from 'vue';
    import dayjs from 'dayjs';
    import utc from 'dayjs/plugin/utc';
    import timezone from 'dayjs/plugin/timezone';
    import api from '../plugins/axios';
    import Sidebar from './Sidebar.vue';
    import Navbar from './Navbar.vue';
    import Loader from './Loader.vue';

    const auditLogs = ref([]);
    const isLoading = ref(false);
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      per_page: 10,
    });
    
    const fetchAuditLogs = async (page = 1) => {
      try {
        isLoading.value = true;
        const response = await api.get('http://localhost:8000/api/logs', {
          params: {
            page,
            user_id: filters.value.user_id || undefined,
            start_date: filters.value.start_date || undefined,
            end_date: filters.value.end_date || undefined,
          },
        });

        auditLogs.value = response.data.data;
        pagination.value.current_page = response.data.current_page;
        pagination.value.last_page = response.data.last_page;
        pagination.value.per_page = response.data.per_page;
      } catch (error) {
        console.error('Error fetching Audit Logs:', error);
      } finally {
        isLoading.value = false;
      }
    };
    
    onMounted(() => {
      fetchUsers();
      fetchAuditLogs();
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

    const parseChanges = (changes: string) => {
      return changes.split(', ');
    };

    const users = ref([]);
    const filters = ref({
      user_id: '',
      start_date: '',
      end_date: '',
    });

    const resetFilters = () => {
      filters.value = {
        user_id: '',
        start_date: '',
        end_date: '',
      };
      fetchAuditLogs();
    };

    const fetchUsers = async () => {
      try {
        const response = await api.get('http://localhost:8000/api/auth/users'); // adjust this URL based on your gateway
        users.value = response.data.data || response.data;
      } catch (error) {
        console.error('Error fetching users:', error);
      }
    };
  </script>
  <style>
    .backdrop-blur-sm {
      backdrop-filter: blur(4px);
    }
  </style>