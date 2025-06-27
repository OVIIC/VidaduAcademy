<template>
  <div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Moje kurzy</h1>
        <p class="mt-2 text-gray-600">Pokračujte vo svojom vzdelávaní</p>
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
                ? 'border-primary-500 text-primary-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
            <span
              :class="[
                'ml-2 py-0.5 px-2 rounded-full text-xs',
                activeTab === tab.key
                  ? 'bg-primary-100 text-primary-600'
                  : 'bg-gray-100 text-gray-600'
              ]"
            >
              {{ getTabCount(tab.key) }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Loading -->
      <div v-if="!authStore.isAuthenticated" class="text-center py-12">
        <p class="text-gray-500">Musíte sa prihlásiť, aby ste videli svoje kurzy.</p>
        <router-link
          to="/login"
          class="mt-4 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
        >
          Prihlásiť sa
        </router-link>
      </div>

      <!-- Loading -->
      <div v-else-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>

      <!-- My Courses Grid -->
      <div v-else-if="authStore.isAuthenticated && filteredCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <MyCourseCard
          v-for="course in filteredCourses"
          :key="course.id"
          :course="course"
        />
      </div>

      <!-- Empty State -->
      <div v-else-if="authStore.isAuthenticated" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ getEmptyStateTitle() }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ getEmptyStateDescription() }}</p>
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
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import { useRoute } from 'vue-router'
import MyCourseCard from '@/components/courses/MyCourseCard.vue'

const authStore = useAuthStore()
const enrollmentStore = useEnrollmentStore()
const route = useRoute()
const activeTab = ref('all')

const tabs = [
  { key: 'all', label: 'Všetky kurzy' },
  { key: 'in-progress', label: 'Prebiehajúce' },
  { key: 'completed', label: 'Dokončené' },
  { key: 'not-started', label: 'Nezačaté' }
]

// Use store data directly
const myCourses = computed(() => enrollmentStore.myCourses)
const loading = computed(() => enrollmentStore.loading)

const filteredCourses = computed(() => {
  if (!enrollmentStore || !enrollmentStore.myCourses) {
    return []
  }
  
  switch (activeTab.value) {
    case 'in-progress':
      return enrollmentStore.inProgressCourses || []
    case 'completed':
      return enrollmentStore.completedCourses || []
    case 'not-started':
      return enrollmentStore.notStartedCourses || []
    default:
      return enrollmentStore.myCourses || []
  }
})

const getTabCount = (tabKey) => {
  if (!enrollmentStore) return 0
  
  switch (tabKey) {
    case 'in-progress':
      return (enrollmentStore.inProgressCourses || []).length
    case 'completed':
      return (enrollmentStore.completedCourses || []).length
    case 'not-started':
      return (enrollmentStore.notStartedCourses || []).length
    default:
      return (enrollmentStore.myCourses || []).length
  }
}

const getEmptyStateTitle = () => {
  switch (activeTab.value) {
    case 'in-progress':
      return 'Žiadne kurzy v progrese'
    case 'completed':
      return 'Žiadne dokončené kurzy'
    case 'not-started':
      return 'Žiadne nezačaté kurzy'
    default:
      return 'Zatiaľ nie ste zapísaní do žiadneho kurzu'
  }
}

const getEmptyStateDescription = () => {
  switch (activeTab.value) {
    case 'in-progress':
      return 'Začnite s niektorým zo svojich kurzov'
    case 'completed':
      return 'Dokončite niektorý zo svojich kurzov'
    case 'not-started':
      return 'Začnite s niektorým zo svojich kurzov'
    default:
      return 'Prejdite do sekcie kurzov a začnite sa vzdelávať'
  }
}

const loadMyCourses = async () => {
  if (!authStore.user || !authStore.isAuthenticated) {
    console.log('User not authenticated, skipping course loading')
    return
  }
  
  try {
    await enrollmentStore.loadMyCourses(true) // Force refresh
  } catch (error) {
    console.error('Error loading my courses in component:', error)
  }
}

onMounted(() => {
  loadMyCourses()
})

// Watch for route changes to refresh data when navigating back to this page
watch(() => route.name, (newRouteName, oldRouteName) => {
  // Only refresh if we're navigating TO MyCourses from a different route
  if (newRouteName === 'MyCourses' && oldRouteName && oldRouteName !== 'MyCourses') {
    console.log('Navigated to MyCourses, refreshing data...')
    loadMyCourses()
  }
}, { immediate: false })
</script>
