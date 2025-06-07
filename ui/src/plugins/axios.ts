import axios from 'axios';
import router from '../router';
import { isLoading } from '../stores/loading';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
});

// Show loader before request
api.interceptors.request.use(config => {
  isLoading.value = true;

  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

// Hide loader after response
api.interceptors.response.use(
  response => {
    isLoading.value = false;
    return response;
  },
  error => {
    isLoading.value = false;

    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      router.push('/login');
    }

    return Promise.reject(error);
  }
);

let isRefreshing = false;
api.interceptors.request.use(async (config) => {
  if (!isRefreshing) {
    isLoading.value = true;
    isRefreshing = true;
    try {
      const token = localStorage.getItem('token');
      if (token) {
        const response = await axios.get('http://localhost:8000/api/auth/refresh', {
          headers: { Authorization: `Bearer ${token}` },
        });
        console.log(response.data);
        const newToken = response.data.new_token;
        if (newToken) {
          localStorage.setItem('token', newToken);
          config.headers.Authorization = `Bearer ${newToken}`;
        }
      }
    } catch (error) {
      console.error('Token refresh failed:', error);
      localStorage.removeItem('token');
    } finally {
      isRefreshing = false;
      isLoading.value = false;
    }
  } else {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});
export default api;