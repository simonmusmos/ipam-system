import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import IpManagementView from '../components/IpManagement.vue';
import AuthView from '../components/AuthView.vue';
import AuditLogs from '../components/AuditLogs.vue';
import Dashboard from '../components/Dashboard.vue';

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
  {
    path: '/logs',
    name: 'AuditLogs',
    component: AuditLogs,
    meta: { requiresAuth: true },
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
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