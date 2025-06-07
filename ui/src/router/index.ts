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

// router.beforeEach(async (to, from, next) => {
//   const token = localStorage.getItem('token');

//   const publicPages = ['/login', '/register'];
//   const authRequired = !publicPages.includes(to.path);
//   console.log(to.path);
//   // console.log(authRequired);
//   // if (authRequired) {
//   //   return next('/login');
//   // }

//   if (token && authRequired) {
//     try {
//       const res = await api.get('http://localhost:8000/api/auth/validate-token');
//       if (res.data.new_token) {
//         localStorage.setItem('token', res.data.new_token);
//       }
//     } catch (error) {
//       localStorage.removeItem('token');
//       if (to.path !== '/login') return next('/login');
//     }
//   }

//   return next();
// });

export default router;