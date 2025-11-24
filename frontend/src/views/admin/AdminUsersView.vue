<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-white">User Management</h1>
      <button 
        @click="openModal()"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors flex items-center"
      >
        <span class="mr-2">+</span> Add User
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex gap-4">
      <div class="flex-1">
        <input 
          v-model="searchQuery"
          @input="debouncedSearch"
          type="text" 
          placeholder="Search users..." 
          class="w-full bg-gray-900 border border-gray-700 rounded-md px-4 py-2 text-white focus:outline-none focus:border-indigo-500"
        >
      </div>
      <select 
        v-model="roleFilter"
        @change="fetchUsers"
        class="bg-gray-900 border border-gray-700 rounded-md px-4 py-2 text-white focus:outline-none focus:border-indigo-500"
      >
        <option value="">All Roles</option>
        <option value="admin">Admin</option>
        <option value="instructor">Instructor</option>
        <option value="student">Student</option>
      </select>
    </div>

    <!-- Users Table -->
    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-800">
        <thead class="bg-gray-800">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">User</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Role</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Joined</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
          <tr v-if="loading" class="animate-pulse">
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading users...</td>
          </tr>
          <tr v-else-if="users.length === 0">
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No users found.</td>
          </tr>
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-800/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-indigo-900/50 flex items-center justify-center text-indigo-400 font-bold border border-indigo-900">
                  {{ getInitials(user.name) }}
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-white">{{ user.name }}</div>
                  <div class="text-sm text-gray-400">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex gap-1">
                <span 
                  v-for="role in user.roles" 
                  :key="role"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-red-900/30 text-red-400 border border-red-900/50': role === 'admin',
                    'bg-purple-900/30 text-purple-400 border border-purple-900/50': role === 'instructor',
                    'bg-green-900/30 text-green-400 border border-green-900/50': role === 'student'
                  }"
                >
                  {{ role }}
                </span>
                <span v-if="!user.roles || user.roles.length === 0" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-700 text-gray-400">
                  User
                </span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="user.is_active ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400'"
              >
                {{ user.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openModal(user)" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</button>
              <button @click="deleteUser(user)" class="text-red-400 hover:text-red-300">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-between items-center text-gray-400 text-sm">
      <div>
        Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
      </div>
      <div class="flex gap-2">
        <button 
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-3 py-1 bg-gray-800 rounded hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Previous
        </button>
        <button 
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-3 py-1 bg-gray-800 rounded hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Modal -->
    <UserModal 
      :is-open="isModalOpen" 
      :user="selectedUser"
      @close="isModalOpen = false"
      @save="handleSave"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '@/services/api'
import { useToast } from 'vue-toastification'
import UserModal from '@/components/admin/UserModal.vue'
import { debounce } from 'lodash'

const toast = useToast()
const users = ref([])
const loading = ref(false)
const searchQuery = ref('')
const roleFilter = ref('')
const isModalOpen = ref(false)
const selectedUser = ref(null)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0
})

const fetchUsers = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      search: searchQuery.value,
      role: roleFilter.value
    }
    const response = await api.get('/admin/users', { params })
    users.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to
    }
  } catch (error) {
    toast.error('Failed to load users')
    console.error(error)
  } finally {
    loading.value = false
  }
}

const debouncedSearch = debounce(() => {
  fetchUsers(1)
}, 300)

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchUsers(page)
  }
}

const openModal = (user = null) => {
  selectedUser.value = user
  isModalOpen.value = true
}

const handleSave = async (userData) => {
  try {
    if (selectedUser.value) {
      await api.put(`/admin/users/${selectedUser.value.id}`, userData)
      toast.success('User updated successfully')
    } else {
      await api.post('/admin/users', userData)
      toast.success('User created successfully')
    }
    fetchUsers(pagination.value.current_page)
  } catch (error) {
    toast.error(error.response?.data?.message || 'Operation failed')
    console.error(error)
  }
}

const deleteUser = async (user) => {
  if (!confirm(`Are you sure you want to delete ${user.name}?`)) return

  try {
    await api.delete(`/admin/users/${user.id}`)
    toast.success('User deleted successfully')
    fetchUsers(pagination.value.current_page)
  } catch (error) {
    toast.error(error.response?.data?.message || 'Delete failed')
  }
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  fetchUsers()
})
</script>
