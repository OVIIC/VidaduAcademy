<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Courses</h1>
        <p class="mt-2 text-gray-600">Track your progress and continue learning</p>
      </div>

      <!-- Filter Tabs -->
      <div class="mb-8">
        <nav class="flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="[
              'py-2 px-1 border-b-2 font-medium text-sm transition duration-200',
              activeTab === tab.key
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
            <span
              :class="[
                'ml-2 py-0.5 px-2 rounded-full text-xs',
                activeTab === tab.key
                  ? 'bg-blue-100 text-blue-600'
                  : 'bg-gray-100 text-gray-600'
              ]"
            >
              {{ getTabCount(tab.key) }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>

      <!-- Courses Grid -->
      <div v-else-if="filteredCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="enrollment in filteredCourses"
          :key="enrollment.id"
          class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-200"
        >
          <!-- Course Thumbnail -->
          <div class="aspect-video bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
            <svg class="w-12 h-12 text-white/70" fill="currentColor" viewBox="0 0 20 20">
              <path d="M8 12.5l5-3-5-3v6z"/>
              <path d="M10 18a8 8 0 100-16 8 8 0 000 16z"/>
            </svg>
          </div>

          <div class="p-6">
            <!-- Course Info -->
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ enrollment.course.title }}</h3>
              <p class="text-sm text-gray-600 line-clamp-2">{{ enrollment.course.description }}</p>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-700">Progress</span>
                <span class="text-sm text-gray-600">{{ enrollment.progress }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: `${enrollment.progress}%` }"
                ></div>
              </div>
            </div>

            <!-- Course Stats -->
            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
              <span>{{ enrollment.course.lessons?.length || 0 }} lessons</span>
              <span>{{ formatDuration(enrollment.course.duration) }}</span>
            </div>

            <!-- Status Badge -->
            <div class="mb-4">
              <span
                :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  getStatusBadgeClass(enrollment.progress)
                ]"
              >
                {{ getStatusText(enrollment.progress) }}
              </span>
            </div>

            <!-- Actions -->
            <div class="flex space-x-3">
              <router-link
                :to="{ name: 'learn', params: { courseId: enrollment.course.id } }"
                :class="[
                  'flex-1 text-center px-4 py-2 rounded-lg text-sm font-medium transition duration-200',
                  enrollment.progress === 0
                    ? 'bg-blue-600 hover:bg-blue-700 text-white'
                    : enrollment.progress === 100
                    ? 'bg-green-600 hover:bg-green-700 text-white'
                    : 'bg-blue-600 hover:bg-blue-700 text-white'
                ]"
              >
                {{ getActionText(enrollment.progress) }}
              </router-link>
              
              <button
                @click="viewCertificate(enrollment)"
                v-if="enrollment.progress === 100"
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200"
              >
                Certificate
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ getEmptyStateTitle() }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ getEmptyStateDescription() }}</p>
        <div class="mt-6">
          <router-link
            to="/courses"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
          >
            Browse Courses
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'MyCoursesView',
  setup() {
    const authStore = useAuthStore()
    const loading = ref(false)
    const activeTab = ref('all')

    const tabs = [
      { key: 'all', label: 'All Courses' },
      { key: 'in-progress', label: 'In Progress' },
      { key: 'completed', label: 'Completed' },
      { key: 'not-started', label: 'Not Started' }
    ]

    const enrollments = computed(() => authStore.user?.enrollments || [])

    const filteredCourses = computed(() => {
      switch (activeTab.value) {
        case 'in-progress':
          return enrollments.value.filter(e => e.progress > 0 && e.progress < 100)
        case 'completed':
          return enrollments.value.filter(e => e.progress === 100)
        case 'not-started':
          return enrollments.value.filter(e => e.progress === 0)
        default:
          return enrollments.value
      }
    })

    const getTabCount = (tabKey) => {
      switch (tabKey) {
        case 'in-progress':
          return enrollments.value.filter(e => e.progress > 0 && e.progress < 100).length
        case 'completed':
          return enrollments.value.filter(e => e.progress === 100).length
        case 'not-started':
          return enrollments.value.filter(e => e.progress === 0).length
        default:
          return enrollments.value.length
      }
    }

    const getStatusText = (progress) => {
      if (progress === 0) return 'Not Started'
      if (progress === 100) return 'Completed'
      return 'In Progress'
    }

    const getStatusBadgeClass = (progress) => {
      if (progress === 0) return 'bg-gray-100 text-gray-800'
      if (progress === 100) return 'bg-green-100 text-green-800'
      return 'bg-blue-100 text-blue-800'
    }

    const getActionText = (progress) => {
      if (progress === 0) return 'Start Course'
      if (progress === 100) return 'Review Course'
      return 'Continue'
    }

    const getEmptyStateTitle = () => {
      switch (activeTab.value) {
        case 'in-progress':
          return 'No courses in progress'
        case 'completed':
          return 'No completed courses'
        case 'not-started':
          return 'No courses to start'
        default:
          return 'No enrolled courses'
      }
    }

    const getEmptyStateDescription = () => {
      switch (activeTab.value) {
        case 'in-progress':
          return 'Start learning to see your progress here.'
        case 'completed':
          return 'Complete a course to earn your first certificate.'
        case 'not-started':
          return 'All your enrolled courses are either in progress or completed.'
        default:
          return 'Enroll in your first course to start learning.'
      }
    }

    const formatDuration = (minutes) => {
      if (!minutes) return '0 min'
      const hours = Math.floor(minutes / 60)
      const mins = minutes % 60
      return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`
    }

    const viewCertificate = (enrollment) => {
      // Mock certificate viewing
      alert(`Certificate for "${enrollment.course.title}" - Feature coming soon!`)
    }

    const loadData = async () => {
      loading.value = true
      try {
        await authStore.fetchUser()
      } catch (error) {
        console.error('Error loading user data:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadData()
    })

    return {
      loading,
      activeTab,
      tabs,
      filteredCourses,
      getTabCount,
      getStatusText,
      getStatusBadgeClass,
      getActionText,
      getEmptyStateTitle,
      getEmptyStateDescription,
      formatDuration,
      viewCertificate
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
