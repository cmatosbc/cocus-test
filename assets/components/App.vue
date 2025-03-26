<template>
  <div id="app">
    <header class="bg-gray-800 text-white p-4">
      <h1 class="text-2xl">Symfony + Vue Notes App</h1>
    </header>
    <main class="container mx-auto p-4">
      <div v-if="!isAuthenticated" class="max-w-md mx-auto">
        <h2 class="text-xl mb-4">Please log in or register to continue</h2>
        <div class="space-x-4">
          <button @click="showLoginForm = true" class="bg-blue-500 text-white px-4 py-2 rounded">Login</button>
          <button @click="showRegisterForm = true" class="bg-green-500 text-white px-4 py-2 rounded">Register</button>
        </div>
      </div>
      <div v-else>
        <h2 class="text-xl mb-4">Welcome, {{ user.name }}!</h2>
        <!-- Notes management will go here -->
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      isAuthenticated: false,
      showLoginForm: false,
      showRegisterForm: false,
      user: null
    }
  }
}
</script>

<style>
/* We'll add Tailwind CSS classes for styling */
</style>
