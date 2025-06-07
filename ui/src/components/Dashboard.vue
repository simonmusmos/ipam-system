<template>
  <Navbar />
  <div class="min-h-screen bg-gray-50 pt-16">
    <Sidebar />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16">
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
          <p class="mt-2 text-sm text-gray-600">Overview of your IP management system</p>
        </div>
      </div>
      <!-- Stats Cards -->
      <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white overflow-hidden shadow-sm ring-black ring-opacity-5 rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total IP Addresses</dt>
                  <dd class="text-3xl font-semibold text-gray-900">{{ stats['total_ip_addresses'] }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm ring-black ring-opacity-5 rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                  <dd class="text-3xl font-semibold text-gray-900">{{ stats['total_users'] }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm ring-black ring-opacity-5 rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Activities Today</dt>
                  <dd class="text-3xl font-semibold text-gray-900">{{ stats['activities_today'] }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Charts Grid -->
      <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- IP Trend Chart -->
        <div class="bg-white p-6 rounded-lg shadow-sm ring-black ring-opacity-5">
          <h3 class="text-lg font-medium text-gray-900 mb-4">IP Address Trend</h3>
          <div class="h-80">
            <Line :data="ipTrendData" :options="chartOptions" />
          </div>
        </div>
        <!-- IP Type Distribution -->
        <div class="bg-white p-6 rounded-lg shadow-sm ring-black ring-opacity-5">
          <h3 class="text-lg font-medium text-gray-900 mb-4">IP Type Distribution</h3>
          <div class="h-80">
            <Pie :data="ipTypeData" :options="chartOptions" />
          </div>
        </div>
        <!-- Activity Chart -->
        <div class="bg-white p-6 rounded-lg shadow-sm ring-black ring-opacity-5 lg:col-span-2">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Weekly Activity</h3>
          <div class="h-80">
            <Bar :data="activityData" :options="chartOptions" />
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
    onMounted
  } from 'vue';
  import {
    Line,
    Pie,
    Bar
  } from 'vue-chartjs';
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    LinearScale,
    PointElement,
    CategoryScale,
    ArcElement,
    BarElement
  } from 'chart.js';
  import api from '../plugins/axios';
  import Sidebar from './Sidebar.vue';
  import Navbar from './Navbar.vue';
  import Loader from './Loader.vue';
  ChartJS.register(Title, Tooltip, Legend, LineElement, LinearScale, PointElement, CategoryScale, ArcElement, BarElement);
  const isLoading = ref(false);
  const stats = ref({
    'total_ip_addresses': 0,
    'total_users': 0,
    'activities_today': 0
  });
  var ipTrendData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'IP Addresses',
      data: [0, 0, 0, 0, 0, 0],
      borderColor: '#4F46E5',
      tension: 0.4,
      fill: false
    }]
  };
  var ipTypeData = {
    labels: ['IPv4', 'IPv6'],
    datasets: [{
      data: [0, 0],
      backgroundColor: ['#4F46E5', '#10B981']
    }]
  };
  var activityData = {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    datasets: [{
      label: 'Activities',
      data: [0, 0, 0, 0, 0, 0, 0],
      backgroundColor: '#4F46E5'
    }]
  };
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
  };
  const fetchIPDashboardData = async () => {
    try {
      isLoading.value = true;
      const response = await api.get('http://localhost:8000/api/ip-addresses/dashboard');
      stats.value.total_ip_addresses = response.data.stats.total_ip_addresses;
      ipTypeData = {
        labels: ['IPv4', 'IPv6'],
        datasets: [{
          data: [response.data.charts.ip_types?.ipv4 || 0, response.data.charts.ip_types?.ipv6 || 0],
          backgroundColor: ['#4F46E5', '#10B981']
        }]
      };
      ipTrendData = {
        labels: response.data.charts.ip_trend.labels,
        datasets: [{
          label: 'IP Addresses',
          data: response.data.charts.ip_trend.data,
          borderColor: '#4F46E5',
          tension: 0.4,
          fill: false
        }]
      };
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
    } finally {
      isLoading.value = false;
    }
  };
  const fetchUserDashboardData = async () => {
    try {
      isLoading.value = true;
      const response = await api.get('http://localhost:8000/api/auth/dashboard');
      stats.value.total_users = response.data.stats.total_users;
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
    } finally {
      isLoading.value = false;
    }
  };
  const fetchAuditDashboardData = async () => {
    try {
      isLoading.value = true;
      const response = await api.get('http://localhost:8000/api/logs/dashboard');
      stats.value.activities_today = response.data.stats.activities_today;
      let weekBreakdown = response.data.charts.week_breakdown;
      activityData = {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Activities',
          data: [weekBreakdown.Monday, weekBreakdown.Tuesday, weekBreakdown.Wednesday, weekBreakdown.Thursday, weekBreakdown.Friday, weekBreakdown.Saturday, weekBreakdown.Sunday],
          backgroundColor: '#4F46E5'
        }]
      };
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
    } finally {
      isLoading.value = false;
    }
  };
  onMounted(async () => {
    await fetchIPDashboardData();
    await fetchUserDashboardData();
    await fetchAuditDashboardData();
  });
</script>