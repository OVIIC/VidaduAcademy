<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>

    <!-- Course Content -->
    <div v-else-if="course" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Course Header -->
      <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <nav class="text-sm text-gray-500 mb-2">
              <router-link to="/my-courses" class="hover:text-primary-600">Moje kurzy</router-link>
              <span class="mx-2">/</span>
              <span class="text-gray-900">{{ course.title }}</span>
            </nav>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ course.title }}</h1>
            
            <div class="flex items-center space-x-6 text-sm text-gray-600">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                {{ course.instructor?.name }}
              </div>
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ formatDuration(course.duration_minutes) }}
              </div>
              <div class="flex items-center">
                <span :class="getDifficultyBadgeClass(course.difficulty_level)">
                  {{ formatDifficulty(course.difficulty_level) }}
                </span>
              </div>
            </div>
          </div>
          
          <!-- Progress Circle -->
          <div class="flex-shrink-0 ml-6">
            <div class="relative w-20 h-20">
              <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                <path
                  class="text-gray-200"
                  stroke="currentColor"
                  stroke-width="3"
                  fill="none"
                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                />
                <path
                  class="text-primary-600"
                  stroke="currentColor"
                  stroke-width="3"
                  fill="none"
                  :stroke-dasharray="`${progressPercentage}, 100`"
                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                />
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-sm font-semibold text-gray-900">{{ Math.round(progressPercentage) }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Course Content (Left Column) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Course Overview -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Prehľad kurzu</h2>
            <p class="text-gray-700 mb-6">{{ course.description }}</p>
            
            <!-- What you will learn -->
            <div v-if="course.what_you_will_learn && course.what_you_will_learn.length > 0" class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Čo sa naučíte</h3>
              <ul class="space-y-2">
                <li v-for="item in course.what_you_will_learn" :key="item" class="flex items-start">
                  <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-gray-700">{{ item }}</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Course Lessons -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Obsah kurzu</h2>
            
            <div v-if="lessons && lessons.length > 0" class="space-y-4">
              <div
                v-for="(lesson, index) in lessons"
                :key="lesson.id"
                class="border rounded-lg p-4 hover:bg-gray-50 transition duration-200 cursor-pointer"
                @click="selectLesson(lesson)"
                :class="{
                  'border-primary-500 bg-primary-50': selectedLesson?.id === lesson.id,
                  'border-gray-200': selectedLesson?.id !== lesson.id
                }"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                      <span class="text-sm font-medium text-gray-600">{{ index + 1 }}</span>
                    </div>
                    <div>
                      <h3 class="font-medium text-gray-900">{{ lesson.title }}</h3>
                      <p class="text-sm text-gray-500">{{ lesson.description || 'Popis lekcie' }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ formatDuration(lesson.duration_minutes || 15) }}</span>
                    <svg v-if="lesson.completed" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- No lessons fallback -->
            <div v-else class="text-center py-8 text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <p class="mt-2">Lekcie budú čoskoro dostupné</p>
            </div>
          </div>
        </div>

        <!-- Sidebar (Right Column) -->
        <div class="lg:col-span-1">
          <!-- Course Stats -->
          <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Štatistiky kurzu</h3>
            <div class="space-y-4">
              <div class="flex justify-between">
                <span class="text-gray-600">Pokrok:</span>
                <span class="font-medium">{{ Math.round(progressPercentage) }}%</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Dokončené lekcie:</span>
                <span class="font-medium">{{ completedLessons }}/{{ totalLessons }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Zostávajúci čas:</span>
                <span class="font-medium">{{ formatDuration(remainingTime) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Zapísaný:</span>
                <span class="font-medium">{{ formatEnrollmentDate(enrollmentDate) }}</span>
              </div>
            </div>
          </div>

          <!-- Continue Learning Button -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pokračovať v učení</h3>
            <button
              v-if="nextLesson"
              @click="startLesson(nextLesson)"
              class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200"
            >
              {{ progressPercentage === 0 ? 'Začať kurz' : 'Pokračovať' }}
            </button>
            <div v-else class="text-center py-4 text-gray-500">
              <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              <p>Kurz je dokončený!</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <div class="text-gray-400 text-lg">Kurz sa nenašiel</div>
      <p class="text-gray-500 mt-2">Skúste sa vrátiť neskôr.</p>
      <router-link 
        to="/my-courses"
        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
      >
        Späť na moje kurzy
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { learningService } from '@/services'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const course = ref(null)
const lessons = ref([])
const selectedLesson = ref(null)

const progressPercentage = computed(() => {
  return course.value?.enrollment_data?.progress_percentage || 0
})

const completedLessons = computed(() => {
  return lessons.value.filter(lesson => lesson.completed).length
})

const totalLessons = computed(() => {
  return lessons.value.length || 1
})

const remainingTime = computed(() => {
  const totalTime = course.value?.duration_minutes || 0
  const completedTime = Math.round((totalTime * progressPercentage.value) / 100)
  return Math.max(0, totalTime - completedTime)
})

const enrollmentDate = computed(() => {
  return course.value?.enrollment_data?.enrolled_at
})

const nextLesson = computed(() => {
  return lessons.value.find(lesson => !lesson.completed) || lessons.value[0]
})

const loadCourseContent = async () => {
  loading.value = true
  try {
    console.log('Loading course content for slug:', route.params.slug)
    const response = await learningService.getCourseContent(route.params.slug)
    console.log('Course content response:', response)
    
    course.value = response.course || response
    lessons.value = response.lessons || []
    
    // Mock some lessons if none exist
    if (lessons.value.length === 0) {
      lessons.value = [
        { id: 1, title: 'Úvod do kurzu', description: 'Základy a prehľad kurzu', duration_minutes: 15, completed: false },
        { id: 2, title: 'Prvé kroky', description: 'Začíname s prácou', duration_minutes: 20, completed: false },
        { id: 3, title: 'Pokročilé techniky', description: 'Hlbšie znalosti', duration_minutes: 25, completed: false },
        { id: 4, title: 'Praktické cvičenia', description: 'Aplikácia poznatkov', duration_minutes: 30, completed: false },
        { id: 5, title: 'Záver a certifikát', description: 'Dokončenie kurzu', duration_minutes: 10, completed: false }
      ]
    }
  } catch (error) {
    console.error('Error loading course content:', error)
    course.value = null
  } finally {
    loading.value = false
  }
}

const selectLesson = (lesson) => {
  selectedLesson.value = lesson
  console.log('Selected lesson:', lesson.title)
}

const startLesson = (lesson) => {
  console.log('Starting lesson:', lesson.title)
  // TODO: Navigate to lesson player/viewer
  // router.push({ name: 'LessonPlayer', params: { courseSlug: route.params.slug, lessonId: lesson.id } })
}

const formatDuration = (minutes) => {
  if (!minutes) return '0min'
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  
  if (hours > 0) {
    return `${hours}h ${mins}min`
  }
  return `${mins}min`
}

const formatDifficulty = (level) => {
  const levels = {
    beginner: 'Začiatočník',
    intermediate: 'Pokročilý',
    advanced: 'Expert'
  }
  return levels[level] || level
}

const getDifficultyBadgeClass = (level) => {
  const classes = {
    beginner: 'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800',
    intermediate: 'px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800',
    advanced: 'px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800'
  }
  return classes[level] || 'px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800'
}

const formatEnrollmentDate = (dateString) => {
  if (!dateString) return 'N/A'
  
  const date = new Date(dateString)
  return date.toLocaleDateString('sk-SK', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(() => {
  loadCourseContent()
})
</script>
