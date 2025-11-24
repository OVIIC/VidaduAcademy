<template>
  <div class="min-h-screen bg-secondary-800 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Vitajte späť, {{ user?.name }}!</h1>
        <p class="mt-2 text-gray-300">Pokračujte vo svojej vzdelávacej ceste</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-secondary-surface shadow-highlight backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6">
          <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 backdrop-blur-xl border border-primary-500/30 rounded-xl">
              <img src="/images/zapisane_kurzy_icon.png" alt="Zapísané kurzy" class="w-6 h-6 object-contain" />
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-300">Zapísané kurzy</p>
              <p class="text-2xl font-bold text-white">{{ enrolledCourses.length }}</p>
            </div>
          </div>
        </div>
        <div class="bg-secondary-surface shadow-highlight backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6">
          <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-green-500/20 via-green-600/20 to-green-500/20 backdrop-blur-xl border border-green-500/30 rounded-xl">
              <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-300">Dokončené</p>
              <p class="text-2xl font-bold text-white">{{ completedCourses }}</p>
            </div>
          </div>
        </div>
        <div class="bg-secondary-surface shadow-highlight backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6">
          <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-secondary-500/20 via-purple-500/20 to-secondary-500/20 backdrop-blur-xl border border-secondary-500/30 rounded-xl">
              <svg class="w-6 h-6 text-secondary-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 7H7v6h6V7z"/>
                <path d="M13 2H7a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-300">Certifikáty</p>
              <p class="text-2xl font-bold text-white">{{ certificates }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
          <!-- Continue Learning -->
          <div class="bg-secondary-surface shadow-highlight backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">Pokračovať v učení</h2>
            
            <!-- Loading -->
            <div v-if="loading" class="flex justify-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
            </div>
            
            <div v-else-if="recentCourses.length > 0" class="space-y-4">
              <div
                v-for="course in recentCourses"
                :key="course.id"
                class="bg-gradient-to-br from-gray-800/40 via-secondary-900/30 to-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-xl p-4 hover:border-primary-500/50 transition duration-200"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h3 class="font-medium text-white mb-1">{{ course.title }}</h3>
                    <div class="w-full bg-gray-700/50 rounded-full h-2 mb-2">
                      <div
                        class="bg-gradient-to-r from-primary-500 to-secondary-500 h-2 rounded-full"
                        :style="{ width: `${course.progress}%` }"
                      ></div>
                    </div>
                    <p class="text-sm text-gray-300">{{ course.progress }}% dokončené</p>
                  </div>
                  <div class="ml-4">
                    <router-link
                      v-if="course.progress < 100"
                      :to="{ name: 'CourseStudy', params: { slug: course.slug } }"
                      class="bg-gradient-to-r from-primary-500 to-secondary-500 hover:from-primary-600 hover:to-secondary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition duration-200 shadow-highlight"
                    >
                      Pokračovať
                    </router-link>
                    <div
                      v-else
                      class="bg-gray-700 text-gray-400 px-4 py-2 rounded-xl text-sm font-medium cursor-not-allowed"
                    >
                      Nedostupné
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-200">Zatiaľ žiadne kurzy</h3>
              <p class="mt-1 text-sm text-gray-400">Začnite zapísaním do svojho prvého kurzu.</p>
              <div class="mt-6">
                <router-link
                  to="/courses"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-secondary-500 hover:from-primary-600 hover:to-secondary-600 text-white rounded-xl text-sm font-medium transition duration-200 shadow-highlight"
                >
                  Prehliadať kurzy
                </router-link>
              </div>
            </div>
          </div>

          <!-- Your Courses Section -->
          <div class="bg-secondary-surface shadow-highlight backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6 mt-8">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold text-white">Tvoje kurzy</h2>
              <router-link
                to="/my-courses"
                class="text-primary-400 hover:text-primary-300 text-sm font-medium"
              >
                Zobraziť všetky
              </router-link>
            </div>
            
            <!-- Loading -->
            <div v-if="loading" class="flex justify-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
            </div>
            
            <!-- Courses Grid -->
            <div v-else-if="enrolledCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div
                v-for="course in enrolledCourses.slice(0, 4)"
                :key="course.id"
                class="bg-gradient-to-br from-gray-800/40 via-secondary-900/30 to-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-xl p-4 hover:border-primary-500/50 transition duration-200"
              >
                <div class="flex items-start space-x-4">
                  <!-- Course Thumbnail -->
                  <div class="flex-shrink-0">
                    <img
                      v-if="course.thumbnail"
                      :src="course.thumbnail"
                      :alt="course.title"
                      class="w-16 h-12 object-cover rounded-xl"
                    >
                    <div
                      v-else
                      class="w-16 h-12 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-xl flex items-center justify-center"
                    >
                      <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </div>
                  </div>
                  
                  <!-- Course Info -->
                  <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-white truncate mb-1">{{ course.title }}</h3>
                    <p v-if="course.instructor?.name" class="text-sm text-gray-300 mb-2">{{ course.instructor.name }}</p>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-700/50 rounded-full h-2 mb-2">
                      <div
                        class="bg-gradient-to-r from-primary-500 to-secondary-500 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${course.enrollment_data?.progress_percentage || 0}%` }"
                      ></div>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-xs text-gray-400">
                        {{ course.enrollment_data?.progress_percentage || 0 }}% dokončené
                      </span>
                      <router-link
                        v-if="course.slug"
                        :to="{ name: 'CourseStudy', params: { slug: course.slug } }"
                        class="text-xs text-primary-400 hover:text-primary-300 font-medium"
                      >
                        Otvoriť →
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Empty State -->
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-200">Žiadne zakúpené kurzy</h3>
              <p class="mt-1 text-sm text-gray-400">Začnite objavovaním našich kurzov.</p>
              <div class="mt-6">
                <router-link
                  to="/courses"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-secondary-500 hover:from-primary-600 hover:to-secondary-600 text-white rounded-xl text-sm font-medium transition duration-200 shadow-highlight"
                >
                  Prehliadať kurzy
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
          <!-- Quick Actions -->
          <div class="bg-secondary-surface shadow-highlight backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Rýchle akcie</h3>
            <div class="space-y-3">
              <router-link
                to="/courses"
                class="w-full bg-gradient-to-r from-primary-500 to-secondary-500 hover:from-primary-600 hover:to-secondary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition duration-200 block text-center shadow-highlight"
              >
                Prehliadať kurzy
              </router-link>
              <router-link
                to="/my-courses"
                class="w-full bg-gradient-to-br from-gray-800/40 via-secondary-900/30 to-gray-800/50 backdrop-blur-xl border border-gray-700/50 hover:border-primary-500/50 text-gray-200 px-4 py-2 rounded-xl text-sm font-medium transition duration-200 block text-center shadow-highlight"
              >
                Moje kurzy
              </router-link>
              <router-link
                to="/profile"
                class="w-full bg-gradient-to-br from-gray-800/40 via-secondary-900/30 to-gray-800/50 backdrop-blur-xl border border-gray-700/50 hover:border-primary-500/50 text-gray-200 px-4 py-2 rounded-xl text-sm font-medium transition duration-200 block text-center shadow-highlight"
              >
                Upraviť profil
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'

const authStore = useAuthStore()
const enrollmentStore = useEnrollmentStore()

const user = computed(() => authStore.user)

// Loading state
const loading = computed(() => enrollmentStore.loading)

// Get courses from enrollment store
const enrolledCourses = computed(() => enrollmentStore.myCourses || [])

const completedCourses = computed(() => {
  return enrolledCourses.value.filter(course => {
    const progress = course.enrollment_data?.progress_percentage || 0
    return progress >= 100
  }).length
})

const certificates = computed(() => completedCourses.value)

// Sort courses by progress percentage (highest to lowest)
const recentCourses = computed(() => {
  return enrolledCourses.value
    .filter(course => {
      const progress = course.enrollment_data?.progress_percentage || 0
      return progress > 0 // Show courses with any progress
    })
    .sort((a, b) => {
      const progressA = a.enrollment_data?.progress_percentage || 0
      const progressB = b.enrollment_data?.progress_percentage || 0
      return progressB - progressA // Sort descending (highest first)
    })
    .slice(0, 5) // Show top 5 courses
    .map(course => ({
      id: course.id,
      title: course.title,
      slug: course.slug,
      progress: course.enrollment_data?.progress_percentage || 0,
      thumbnail: course.thumbnail,
      instructor: course.instructor
    }))
})

const loadDashboardData = async () => {
  try {
    // Load user courses from enrollment store
    await enrollmentStore.loadMyCourses(true)
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  }
}

onMounted(() => {
  loadDashboardData()
})
</script>
