<template>
  <button @click="logout">Logout</button>
</template>

<script>
export default {
  name: 'Logout',
  methods: {
    logout() {
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
      this.$router.push('/login');
    }
  }
}
</script>
