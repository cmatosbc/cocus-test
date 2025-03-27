// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './components/App.vue';
import axios from 'axios';

// Configure axios defaults
axios.defaults.baseURL = '';

// Set up token if it exists
const token = localStorage.getItem('token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Add a request interceptor to automatically add the JWT token
axios.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Handle unauthorized responses
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      router.push('/login');
    }
    return Promise.reject(error);
  }
);

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/login'
    },
    {
      path: '/login',
      component: () => import('./components/LoginForm.vue')
    },
    {
      path: '/register',
      component: () => import('./components/RegisterForm.vue')
    },
    {
      path: '/dashboard',
      component: () => import('./components/Dashboard.vue'),
      meta: { requiresAuth: true }
    }
  ]
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else if ((to.path === '/login' || to.path === '/register') && token) {
    next('/dashboard');
  } else {
    next();
  }
});

const app = createApp(App);
app.use(router);
app.mount('#app');
