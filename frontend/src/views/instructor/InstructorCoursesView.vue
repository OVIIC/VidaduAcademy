<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-white">Course Management</h1>
      <button 
        @click="createCourse"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors flex items-center"
      >
        <span class="mr-2">+</span> Create New Course
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex gap-4">
      <div class="flex-1">
        <input 
          v-model="searchQuery"
          @input="debouncedSearch"
          type="text" 
          placeholder="Search courses..." 
          class="w-full bg-gray-900 border border-gray-700 rounded-md px-4 py-2 text-white focus:outline-none focus:border-indigo-500"
        >
      </div>
      <select 
        v-model="statusFilter"
        @change="fetchCourses"
        class="bg-gray-900 border border-gray-700 rounded-md px-4 py-2 text-white focus:outline-none focus:border-indigo-500"
      >
        <option value="">All Statuses</option>
        <option value="published">Published</option>
        <option value="draft">Draft</option>
        <option value="archived">Archived</option>
      </select>
    </div>

    <!-- Courses Table -->
    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-800">
        <thead class="bg-gray-800">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Course</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Price</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Created</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
          <tr v-if="loading" class="animate-pulse">
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading courses...</td>
          </tr>
          <tr v-else-if="courses.length === 0">
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No courses found. Create your first course!</td>
          </tr>
          <tr v-for="course in courses" :key="course.id" class="hover:bg-gray-800/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-12 w-16 flex-shrink-0 bg-gray-800 rounded overflow-hidden border border-gray-700">
                  <img v-if="course.thumbnail" :src="course.thumbnail" class="h-full w-full object-cover" alt="">
                  <div v-else class="h-full w-full flex items-center justify-center text-gray-600">
                    <span class="text-xs">No Img</span>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-white">{{ course.title }}</div>
                  <div class="text-xs text-gray-500">Slug: {{ course.slug }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="{
                  'bg-green-900/30 text-green-400 border border-green-900/50': course.status === 'published',
                  'bg-yellow-900/30 text-yellow-400 border border-yellow-900/50': course.status === 'draft',
                  'bg-gray-700 text-gray-400': course.status === 'archived'
                }"
              >
                {{ capitalize(course.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
              {{ course.price > 0 ? `$${course.price}` : 'Free' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
              {{ formatDate(course.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="editCourse(course.id)" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</button>
              <button @click="deleteCourse(course)" class="text-red-400 hover:text-red-300">Delete</button>
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

    <!-- Create Modal -->
    <div v-if="isCreateModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="isCreateModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-700">
          <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-white mb-4">Create New Course</h3>
            <div>
              <label class="block text-sm font-medium text-gray-400 mb-1">Course Title</label>
              <input 
                v-model="newCourseTitle"
                type="text" 
                class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="e.g. Advanced Vue.js Masterclass"
              >
            </div>
          </div>
          <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
            <button 
              @click="submitCreateCourse"
              :disabled="!newCourseTitle || createLoading"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ createLoading ? 'Creating...' : 'Create & Edit' }}
            </button>
            <button 
              @click="isCreateModalOpen = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-gray-300 hover:bg-gray-600 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '@/services/api'
import { useToast } from 'vue-toastification'
import { debounce } from 'lodash'

const router = useRouter()
const toast = useToast()

const courses = ref([])
const loading = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const isCreateModalOpen = ref(false)
const newCourseTitle = ref('')
const createLoading = ref(false)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0
})

const fetchCourses = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      search: searchQuery.value,
      status: statusFilter.value
    }
    const response = await api.get('/instructor/courses', { params })
    courses.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to
    }
  } catch (error) {
    toast.error('Failed to load courses')
    console.error(error)
  } finally {
    loading.value = false
  }
}

const debouncedSearch = debounce(() => {
  fetchCourses(1)
}, 300)

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchCourses(page)
  }
}

const createCourse = () => {
  newCourseTitle.value = ''
  isCreateModalOpen.value = true
}

const submitCreateCourse = async () => {
  createLoading.value = true
  try {
    const response = await api.post('/instructor/courses', {
      title: newCourseTitle.value
    })
    toast.success('Course created successfully')
    isCreateModalOpen.value = false
    // Redirect to edit page
    router.push(`/instructor/courses/${response.data.course.id}/edit`)
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to create course')
  } finally {
    createLoading.value = false
  }
}

const editCourse = (id) => {
  router.push(`/instructor/courses/${id}/edit`)
}

const deleteCourse = async (course) => {
  if (!confirm(`Are you sure you want to delete "${course.title}"? This action cannot be undone.`)) return

  try {
    await api.delete(`/instructor/courses/${course.id}`)
    toast.success('Course deleted successfully')
    fetchCourses(pagination.value.current_page)
  } catch (error) {
    toast.error(error.response?.data?.message || 'Delete failed')
  }
}

const capitalize = (s) => {
  if (typeof s !== 'string') return ''
  return s.charAt(0).toUpperCase() + s.slice(1)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  fetchCourses()
})
</script>
