import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import LoginView from '../components/LoginForm.vue';
import RegisterView from '../components/RegisterForm.vue';
import IpManagementView from '../components/IpManagement.vue';
import AuthView from '../components/AuthView.vue';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: '/ip-addresses',
  },
  {
    path: '/login',
    name: 'Login',
    component: AuthView,
    meta: { guest: true },
  },
  {
    path: '/ip-addresses',
    name: 'IpAddresses',
    component: IpManagementView,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// 🔐 Navigation Guard for auth
router.beforeEach((to, _, next) => {
  const token = localStorage.getItem('token');

  if (to.meta.requiresAuth && !token) {
    next({ name: 'Login' });
  } else if (to.meta.guest && token) {
    next({ name: 'IpAddresses' });
  } else {
    next();
  }
});

export default router;