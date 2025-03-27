<template>
  <div class="login-form">
    <h2 class="text-2xl font-bold mb-6">Login</h2>
    <form @submit.prevent="handleSubmit" class="space-y-4">
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
        />
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
          Logging in...
        </span>
        <span v-else>Login</span>
      </button>
      <div class="text-center mt-4 text-sm text-gray-600">
        Don't have an account? 
        <router-link to="/register" class="text-indigo-600 hover:text-indigo-500">Register here</router-link>
      </div>
    </form>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
  name: 'LoginForm',
  setup() {
    const router = useRouter()
    const email = ref('')
    const password = ref('')
    const error = ref(null)
    const isLoading = ref(false)

    const handleSubmit = async () => {
      error.value = null
      isLoading.value = true

      try {
        const response = await axios.post('/login', {
          email: email.value, 
          password: password.value
        }, {
          headers: {
            'Content-Type': 'application/json'
          }
        })

        // Store the token
        localStorage.setItem('token', response.data.token)
        
        // Set default Authorization header with Bearer prefix
        const bearerToken = `Bearer ${response.data.token}`
        axios.defaults.headers.common['Authorization'] = bearerToken

        // Redirect to dashboard
        router.push('/dashboard')
      } catch (err) {
        if (err.response?.data?.message) {
          error.value = err.response.data.message
        } else if (err.response?.data?.error) {
          error.value = err.response.data.error
        } else {
          error.value = 'An error occurred during login'
        }
      } finally {
        isLoading.value = false
      }
    }

    return {
      email,
      password,
      error,
      isLoading,
      handleSubmit
    }
  }
}
</script>