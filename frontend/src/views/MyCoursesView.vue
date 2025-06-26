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
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>

      <!-- My Courses Grid -->
      <div v-else-if="filteredCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <MyCourseCard
          v-for="course in filteredCourses"
          :key="course.id"
          :course="course"
        />
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
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { enrollmentService } from '@/services'
import MyCourseCard from '@/components/courses/MyCourseCard.vue'

const authStore = useAuthStore()
const loading = ref(false)
const activeTab = ref('all')
const myCourses = ref([])

const tabs = [
  { key: 'all', label: 'Všetky kurzy' },
  { key: 'in-progress', label: 'Prebiehajúce' },
  { key: 'completed', label: 'Dokončené' },
  { key: 'not-started', label: 'Nezačaté' }
]

const filteredCourses = computed(() => {
  switch (activeTab.value) {
    case 'in-progress':
      return myCourses.value.filter(course => {
        const progress = course.enrollment_data?.progress_percentage || 0
        return progress > 0 && progress < 100
      })
    case 'completed':
      return myCourses.value.filter(course => {
        const progress = course.enrollment_data?.progress_percentage || 0
        return progress >= 100
      })
    case 'not-started':
      return myCourses.value.filter(course => {
        const progress = course.enrollment_data?.progress_percentage || 0
        return progress === 0
      })
    default:
      return myCourses.value
  }
})

const getTabCount = (tabKey) => {
  switch (tabKey) {
    case 'in-progress':
      return myCourses.value.filter(course => {
        const progress = course.enrollment_data?.progress_percentage || 0
        return progress > 0 && progress < 100
      }).length
    case 'completed':
      return myCourses.value.filter(course => {
        const progress = course.enrollment_data?.progress_percentage || 0
        return progress >= 100
      }).length
    case 'not-started':
      return myCourses.value.filter(course => {
        const progress = course.enrollment_data?.progress_percentage || 0
        return progress === 0
      }).length
    default:
      return myCourses.value.length
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
  if (!authStore.user) {
    return
  }
  
  loading.value = true
  try {
    console.log('Loading my courses...')
    const response = await enrollmentService.getMyCourses()
    console.log('My courses response:', response)
    myCourses.value = response.data || []
    console.log('My courses loaded:', myCourses.value.length)
  } catch (error) {
    console.error('Error loading my courses:', error)
    myCourses.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadMyCourses()
})
</script>
