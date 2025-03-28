<template>
  <div class="dashboard">
    <nav class="mb-6 flex justify-between items-center">
      <h1 class="text-2xl font-bold">Dashboard</h1>
      <button @click="logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
        Logout
      </button>
    </nav>

    <div v-if="loading" class="text-center py-4">
      Loading...
    </div>

    <div v-else-if="error" class="text-red-600 py-4">
      {{ error }}
    </div>

    <div v-else>
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Profile Information</h2>
        <div class="space-y-4">
          <div class="flex">
            <span class="font-medium w-24">Name:</span>
            <span>{{ profile.name }}</span>
          </div>
          <div class="flex">
            <span class="font-medium w-24">Email:</span>
            <span>{{ profile.email }}</span>
          </div>
          <div class="flex">
            <span class="font-medium w-24">Role:</span>
            <span>{{ profile.roles.join(', ') }}</span>
          </div>
        </div>
      </div>

      <NoteManager />
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import NoteManager from './NoteManager.vue';

export default {
  name: 'Dashboard',
  components: {
    NoteManager
  },
  setup() {
    const router = useRouter();
    const profile = ref(null);
    const loading = ref(true);
    const error = ref(null);

    const fetchProfile = async () => {
      try {
        const response = await axios.get('/api/users/me');
        profile.value = response.data;
      } catch (err) {
        error.value = 'Failed to load profile data';
        console.error('Error fetching profile:', err);
      } finally {
        loading.value = false;
      }
    };

    const logout = () => {
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
      router.push('/login');
    };

    onMounted(() => {
      fetchProfile();
    });

    return {
      profile,
      loading,
      error,
      logout
    };
  }
};
</script>

<style scoped>
.dashboard {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}
</style>