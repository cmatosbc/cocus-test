// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import App from './components/App.vue';
import LoginForm from './components/LoginForm.vue';
import RegisterForm from './components/RegisterForm.vue';

// Configure axios defaults
axios.defaults.baseURL = '/api';

// Set up token if it exists
const token = localStorage.getItem('token');
if (token) {
  const bearerToken = token.startsWith('Bearer ') ? token : `Bearer ${token}`;
  axios.defaults.headers.common['Authorization'] = bearerToken;
}

// Create router
const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/',
      redirect: '/login'
    },
    {
      path: '/login',
      name: 'login',
      component: LoginForm,
      meta: { requiresAuth: false, redirectIfAuth: true }
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterForm,
      meta: { requiresAuth: false, redirectIfAuth: true }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('./components/Dashboard.vue'),
      meta: { requiresAuth: true }
    }
  ]
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const isAuthenticated = !!token;

  // If route requires auth and user is not authenticated
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
    return;
  }

  // If user is authenticated and tries to access login/register
  if (to.meta.redirectIfAuth && isAuthenticated) {
    next('/dashboard');
    return;
  }

  next();
});

// Create Vue app
const app = createApp(App);

// Add router
app.use(router);

// Mount app
app.mount('#app');
