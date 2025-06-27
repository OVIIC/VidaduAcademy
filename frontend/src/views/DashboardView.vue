<template>
  <div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Vitajte späť, {{ user?.name }}!</h1>
        <p class="mt-2 text-gray-600">Pokračujte vo svojej vzdelávacej ceste</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center">
            <div class="p-3 bg-primary-100 rounded-lg">
              <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Zapísané kurzy</p>
              <p class="text-2xl font-bold text-gray-900">{{ enrolledCourses.length }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Dokončené</p>
              <p class="text-2xl font-bold text-gray-900">{{ completedCourses }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center">
            <div class="p-3 bg-secondary-100 rounded-lg">
              <svg class="w-6 h-6 text-secondary-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 7H7v6h6V7z"/>
                <path d="M13 2H7a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Certifikáty</p>
              <p class="text-2xl font-bold text-gray-900">{{ certificates }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
          <!-- Continue Learning -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Pokračovať v učení</h2>
            
            <!-- Loading -->
            <div v-if="loading" class="flex justify-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
            </div>
            
            <div v-else-if="recentCourses.length > 0" class="space-y-4">
              <div
                v-for="course in recentCourses"
                :key="course.id"
                class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-200"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h3 class="font-medium text-gray-900 mb-1">{{ course.title }}</h3>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                      <div
                        class="bg-primary-600 h-2 rounded-full"
                        :style="{ width: `${course.progress}%` }"
                      ></div>
                    </div>
                    <p class="text-sm text-gray-500">{{ course.progress }}% dokončené</p>
                  </div>
                  <div class="ml-4">
                    <router-link
                      v-if="course.slug"
                      :to="{ name: 'CourseStudy', params: { slug: course.slug } }"
                      class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200"
                    >
                      Pokračovať
                    </router-link>
                    <div
                      v-else
                      class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed"
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
              <h3 class="mt-2 text-sm font-medium text-gray-900">Zatiaľ žiadne kurzy</h3>
              <p class="mt-1 text-sm text-gray-500">Začnite zapísaním do svojho prvého kurzu.</p>
              <div class="mt-6">
                <router-link
                  to="/courses"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
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
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rýchle akcie</h3>
            <div class="space-y-3">
              <router-link
                to="/courses"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 block text-center"
              >
                Prehliadať kurzy
              </router-link>
              <router-link
                to="/my-courses"
                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-200 block text-center"
              >
                Moje kurzy
              </router-link>
              <router-link
                to="/profile"
                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-200 block text-center"
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
