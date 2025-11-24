<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-white">Security Logs</h1>
      <button 
        @click="fetchLogs"
        class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md transition-colors flex items-center"
      >
        <ArrowPathIcon class="h-5 w-5 mr-2" :class="{ 'animate-spin': loading }" />
        Refresh
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex gap-4">
      <div class="flex-1">
        <input 
          v-model="searchQuery"
          @input="debouncedSearch"
          type="text" 
          placeholder="Search logs..." 
          class="w-full bg-gray-900 border border-gray-700 rounded-md px-4 py-2 text-white focus:outline-none focus:border-indigo-500"
        >
      </div>
      <select 
        v-model="typeFilter"
        @change="fetchLogs"
        class="bg-gray-900 border border-gray-700 rounded-md px-4 py-2 text-white focus:outline-none focus:border-indigo-500"
      >
        <option value="">All Events</option>
        <option value="login_failed">Login Failed</option>
        <option value="csp_violation">CSP Violation</option>
        <option value="suspicious_activity">Suspicious Activity</option>
        <option value="general_violation">General Violation</option>
      </select>
    </div>

    <!-- Logs Table -->
    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-800">
        <thead class="bg-gray-800">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Event</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Description</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">User / IP</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Time</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Details</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
          <tr v-if="loading" class="animate-pulse">
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading logs...</td>
          </tr>
          <tr v-else-if="logs.length === 0">
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No security logs found.</td>
          </tr>
          <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-800/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="{
                  'bg-red-900/30 text-red-400 border border-red-900/50': log.event_type === 'login_failed',
                  'bg-yellow-900/30 text-yellow-400 border border-yellow-900/50': log.event_type === 'csp_violation',
                  'bg-gray-700 text-gray-400': !['login_failed', 'csp_violation'].includes(log.event_type)
                }"
              >
                {{ formatType(log.event_type) }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-300 max-w-xs truncate" :title="log.description">
              {{ log.description }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
              <div v-if="log.user">
                <span class="text-white">{{ log.user.name }}</span>
                <span class="text-xs block">{{ log.user.email }}</span>
              </div>
              <div v-else>Guest</div>
              <div class="text-xs mt-1 font-mono">{{ log.ip_address }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
              {{ formatDate(log.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="viewDetails(log)" class="text-indigo-400 hover:text-indigo-300">View Payload</button>
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

    <!-- Details Modal -->
    <div v-if="selectedLog" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="selectedLog = null"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-700">
          <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-white mb-4">Log Details</h3>
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs text-gray-500 uppercase">Event Type</label>
                  <div class="text-white">{{ formatType(selectedLog.event_type) }}</div>
                </div>
                <div>
                  <label class="block text-xs text-gray-500 uppercase">Time</label>
                  <div class="text-white">{{ formatDate(selectedLog.created_at) }}</div>
                </div>
                <div>
                  <label class="block text-xs text-gray-500 uppercase">IP Address</label>
                  <div class="text-white font-mono">{{ selectedLog.ip_address }}</div>
                </div>
                <div>
                  <label class="block text-xs text-gray-500 uppercase">User Agent</label>
                  <div class="text-white text-xs truncate" :title="selectedLog.user_agent">{{ selectedLog.user_agent }}</div>
                </div>
              </div>
              
              <div>
                <label class="block text-xs text-gray-500 uppercase mb-1">Payload</label>
                <pre class="bg-gray-900 p-4 rounded-md overflow-x-auto text-xs text-green-400 font-mono">{{ JSON.stringify(selectedLog.payload, null, 2) }}</pre>
              </div>
            </div>
          </div>
          <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
            <button 
              @click="selectedLog = null"
              class="w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-gray-300 hover:bg-gray-600 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '@/services/api'
import { useToast } from 'vue-toastification'
import { ArrowPathIcon } from '@heroicons/vue/24/outline'
import { debounce } from 'lodash'

const toast = useToast()
const logs = ref([])
const loading = ref(false)
const searchQuery = ref('')
const typeFilter = ref('')
const selectedLog = ref(null)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0
})

const fetchLogs = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      search: searchQuery.value,
      event_type: typeFilter.value
    }
    const response = await api.get('/admin/security-logs', { params })
    logs.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to
    }
  } catch (error) {
    toast.error('Failed to load logs')
    console.error(error)
  } finally {
    loading.value = false
  }
}

const debouncedSearch = debounce(() => {
  fetchLogs(1)
}, 300)

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchLogs(page)
  }
}

const viewDetails = (log) => {
  selectedLog.value = log
}

const formatType = (type) => {
  return type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString()
}

onMounted(() => {
  fetchLogs()
})
</script>
