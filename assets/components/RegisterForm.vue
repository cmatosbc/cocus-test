<template>
  <div class="register-form">
    <h2 class="text-2xl font-bold mb-6">Register</h2>
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div class="form-group">
        <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
        <input
          type="text"
          id="name"
          v-model="name"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          placeholder="Enter your name"
        />
      </div>
      <div class="form-group">
        <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
        <input
          type="email"
          id="email"
          v-model="email"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          placeholder="Enter your email"
        />
      </div>
      <div class="form-group">
        <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
        <input
          type="password"
          id="password"
          v-model="password"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          placeholder="Enter your password"
          minlength="6"
        />
        <p class="mt-1 text-sm text-gray-500">Password must be at least 6 characters long</p>
      </div>
      <div v-if="error" class="text-red-600 text-sm mt-2">
        {{ error }}
      </div>
      <button 
        type="submit" 
        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        :disabled="isLoading"
      >
        <span v-if="isLoading">
          <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Registering...
        </span>
        <span v-else>Register</span>
      </button>
      <div class="text-center mt-4 text-sm text-gray-600">
        Already have an account? 
        <router-link to="/login" class="text-indigo-600 hover:text-indigo-500">Login here</router-link>
      </div>
    </form>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
  name: 'RegisterForm',
  setup() {
    const router = useRouter()
    const name = ref('')
    const email = ref('')
    const password = ref('')
    const error = ref(null)
    const isLoading = ref(false)

    const handleSubmit = async () => {
      error.value = null
      isLoading.value = true

      try {
        const response = await axios.post('/api/register', {
          name: name.value,
          email: email.value,
          password: password.value
        });

        // Store token and set axios default header
        localStorage.setItem('token', response.data.token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        router.push('/dashboard');
      } catch (err) {
        if (err.response?.data?.errors) {
          error.value = Array.isArray(err.response.data.errors) 
            ? err.response.data.errors.join(', ') 
            : err.response.data.errors
        } else if (err.response?.data?.error) {
          error.value = err.response.data.error
        } else {
          error.value = 'Registration failed. Please try again.'
        }
      } finally {
        isLoading.value = false
      }
    }

    return {
      name,
      email,
      password,
      error,
      isLoading,
      handleSubmit
    }
  }
}
</script>