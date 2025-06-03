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
  
  export default api;