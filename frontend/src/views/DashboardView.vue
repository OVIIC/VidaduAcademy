<template>
  <div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Vitajte späť, {{ user?.name }}!</h1>
        <p class="mt-2 text-gray-600">Pokračujte vo svojej vzdelávacej ceste</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
            <div class="p-3 bg-yellow-100 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Hodiny učenia</p>
              <p class="text-2xl font-bold text-gray-900">{{ totalLearningHours }}h</p>
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
          <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Pokračovať v učení</h2>
            
            <div v-if="recentCourses.length > 0" class="space-y-4">
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
                      :to="{ name: 'learn', params: { courseId: course.id } }"
                      class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200"
                    >
                      Pokračovať
                    </router-link>
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

          <!-- Recent Activity -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Posledná aktivita</h2>
            
            <div v-if="recentActivity.length > 0" class="space-y-4">
              <div
                v-for="activity in recentActivity"
                :key="activity.id"
                class="flex items-start space-x-3"
              >
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-gray-900">{{ activity.description }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(activity.created_at) }}</p>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-6">
              <p class="text-gray-500">Žiadna posledná aktivita</p>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
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

          <!-- Learning Streak -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Študijná séria</h3>
            <div class="text-center">
              <div class="text-3xl font-bold text-orange-500 mb-2">🔥</div>
              <p class="text-2xl font-bold text-gray-900">{{ learningStreak }}</p>
              <p class="text-sm text-gray-500">dní po sebe</p>
            </div>
            <div class="mt-4 grid grid-cols-7 gap-1">
              <div
                v-for="day in last7Days"
                :key="day.date"
                :class="[
                  'w-4 h-4 rounded-sm',
                  day.active ? 'bg-green-500' : 'bg-gray-200'
                ]"
                :title="day.date"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useCourseStore } from '@/stores/course'

export default {
  name: 'DashboardView',
  setup() {
    const authStore = useAuthStore()
    const courseStore = useCourseStore()
    
    const recentActivity = ref([])
    const learningStreak = ref(3)

    const user = computed(() => authStore.user)
    const enrolledCourses = computed(() => user.value?.enrollments || [])
    
    const completedCourses = computed(() => {
      return enrolledCourses.value.filter(enrollment => enrollment.progress === 100).length
    })

    const totalLearningHours = computed(() => {
      return Math.floor(Math.random() * 50) + 10 // Mock data
    })

    const certificates = computed(() => completedCourses.value)

    const recentCourses = computed(() => {
      return enrolledCourses.value
        .filter(enrollment => enrollment.progress > 0 && enrollment.progress < 100)
        .slice(0, 3)
        .map(enrollment => ({
          ...enrollment.course,
          progress: enrollment.progress
        }))
    })

    const last7Days = computed(() => {
      const days = []
      for (let i = 6; i >= 0; i--) {
        const date = new Date()
        date.setDate(date.getDate() - i)
        days.push({
          date: date.toISOString().split('T')[0],
          active: Math.random() > 0.3 // Mock activity
        })
      }
      return days
    })

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const loadDashboardData = async () => {
      try {
        // Load user enrollments and progress
        await authStore.fetchUser()
        
        // Mock recent activity
        recentActivity.value = [
          {
            id: 1,
            description: 'Completed lesson "Introduction to YouTube Analytics"',
            created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString()
          },
          {
            id: 2,
            description: 'Started course "YouTube SEO Mastery"',
            created_at: new Date(Date.now() - 5 * 60 * 60 * 1000).toISOString()
          },
          {
            id: 3,
            description: 'Earned certificate for "Content Creation Basics"',
            created_at: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString()
          }
        ]
      } catch (error) {
        console.error('Error loading dashboard data:', error)
      }
    }

    onMounted(() => {
      loadDashboardData()
    })

    return {
      user,
      enrolledCourses,
      completedCourses,
      totalLearningHours,
      certificates,
      recentCourses,
      recentActivity,
      learningStreak,
      last7Days,
      formatDate
    }
  }
}
</script>
