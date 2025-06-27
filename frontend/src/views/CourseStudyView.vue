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
          <!-- Selected Lesson Content -->
          <div v-if="selectedLesson" class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Lesson Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-xl font-semibold">{{ selectedLesson.title }}</h2>
                  <p class="text-primary-100 mt-1">Lekcia {{ selectedLessonIndex + 1 }} z {{ lessons.length }}</p>
                </div>
                <div class="flex items-center space-x-2">
                  <button
                    v-if="selectedLessonIndex > 0"
                    @click="selectLesson(lessons[selectedLessonIndex - 1])"
                    class="p-2 text-white hover:bg-primary-500 rounded-lg transition duration-200"
                    title="Predchádzajúca lekcia"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                  </button>
                  <button
                    v-if="selectedLessonIndex < lessons.length - 1"
                    @click="selectLesson(lessons[selectedLessonIndex + 1])"
                    class="p-2 text-white hover:bg-primary-500 rounded-lg transition duration-200"
                    title="Ďalšia lekcia"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Video Player -->
            <div v-if="selectedLesson.video_url" class="aspect-video bg-black">
              <iframe
                :src="getEmbedUrl(selectedLesson.video_url)"
                class="w-full h-full"
                frameborder="0"
                allowfullscreen
                title="Lesson Video"
              ></iframe>
            </div>

            <!-- Lesson Content -->
            <div class="p-6">
              <div v-if="selectedLesson.description" class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Popis lekcie</h3>
                <p class="text-gray-700">{{ selectedLesson.description }}</p>
              </div>

              <div v-if="selectedLesson.content" class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Obsah lekcie</h3>
                <div class="prose max-w-none text-gray-700" v-html="selectedLesson.content"></div>
              </div>

              <!-- Lesson Resources -->
              <div v-if="selectedLesson.resources && selectedLesson.resources.length > 0" class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Zdroje a súbory</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <a
                    v-for="resource in selectedLesson.resources"
                    :key="resource.url"
                    :href="resource.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition duration-200"
                  >
                    <div class="flex-shrink-0 mr-3">
                      <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg v-if="resource.type === 'pdf'" class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else-if="resource.type === 'video'" class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">{{ resource.title }}</p>
                      <p v-if="resource.description" class="text-xs text-gray-500 truncate">{{ resource.description }}</p>
                      <p class="text-xs text-primary-600 font-medium uppercase">{{ getResourceTypeLabel(resource.type) }}</p>
                    </div>
                    <div class="flex-shrink-0 ml-2">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                    </div>
                  </a>
                </div>
              </div>

              <!-- Lesson Transcript -->
              <div v-if="selectedLesson.transcript" class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Prepis videa</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                  <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ selectedLesson.transcript }}</p>
                </div>
              </div>

              <!-- Lesson Actions -->
              <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <button
                  @click="toggleLessonCompletion(selectedLesson)"
                  :class="[
                    'flex items-center px-4 py-2 rounded-lg font-medium transition duration-200',
                    selectedLesson.completed
                      ? 'bg-green-100 text-green-800 hover:bg-green-200'
                      : 'bg-primary-100 text-primary-800 hover:bg-primary-200'
                  ]"
                >
                  <svg v-if="selectedLesson.completed" class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  {{ selectedLesson.completed ? 'Označiť ako nedokončené' : 'Označiť ako dokončené' }}
                </button>

                <div class="flex items-center space-x-2">
                  <button
                    v-if="selectedLessonIndex > 0"
                    @click="selectLesson(lessons[selectedLessonIndex - 1])"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200"
                  >
                    Predchádzajúca
                  </button>
                  <button
                    v-if="selectedLessonIndex < lessons.length - 1"
                    @click="selectLesson(lessons[selectedLessonIndex + 1])"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition duration-200"
                  >
                    Ďalšia lekcia
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Course Overview (when no lesson selected) -->
          <div v-else class="bg-white rounded-lg shadow-sm p-6">
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

          <!-- Course Lessons List -->
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
  // Try to get progress from course enrollment data or calculate from lessons
  const enrollmentProgress = course.value?.progress_percentage || course.value?.enrollment?.progress_percentage
  
  if (enrollmentProgress !== undefined) {
    return enrollmentProgress
  }
  
  // Fallback: calculate from completed lessons
  const completed = completedLessons.value
  const total = totalLessons.value
  return total > 0 ? Math.round((completed / total) * 100) : 0
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

const selectedLessonIndex = computed(() => {
  return selectedLesson.value ? lessons.value.findIndex(lesson => lesson.id === selectedLesson.value.id) : -1
})

const loadCourseContent = async () => {
  loading.value = true
  try {
    console.log('Loading course content for slug:', route.params.slug)
    const response = await learningService.getCourseContent(route.params.slug)
    console.log('Course content response:', response)
    
    course.value = response.course || response
    lessons.value = response.lessons || []
    
    // Store enrollment data in course for progress tracking
    if (response.enrollment) {
      course.value.enrollment = response.enrollment
      course.value.progress_percentage = response.enrollment.progress_percentage
    }
    
    // Convert video_duration from seconds to minutes for display and map progress data
    if (lessons.value.length > 0) {
      lessons.value = lessons.value.map(lesson => ({
        ...lesson,
        duration_minutes: lesson.video_duration ? Math.ceil(lesson.video_duration / 60) : 15,
        completed: lesson.user_progress?.completed || false,
        progress_percentage: lesson.user_progress?.progress_percentage || 0,
        watch_time_seconds: lesson.user_progress?.watch_time_seconds || 0
      }))
    }
    
    // Mock some lessons if none exist
    if (lessons.value.length === 0) {
      lessons.value = [
        { 
          id: 1, 
          title: 'Úvod do kurzu', 
          description: 'Základy a prehľad kurzu', 
          content: '<h3>Vitajte v kurze!</h3><p>V tejto úvodnej lekcii sa zoznámite so základmi kurzu.</p>',
          duration_minutes: 15, 
          completed: false,
          video_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        },
        { 
          id: 2, 
          title: 'Prvé kroky', 
          description: 'Začíname s prácou', 
          content: '<h3>Prvé kroky</h3><p>Teraz sa naučíte základné techniky a postupy.</p>',
          duration_minutes: 20, 
          completed: false,
          video_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        },
        { 
          id: 3, 
          title: 'Pokročilé techniky', 
          description: 'Hlbšie znalosti', 
          content: '<h3>Pokročilé techniky</h3><p>V tejto lekcii sa dostaneme k pokročilejším témam.</p>',
          duration_minutes: 25, 
          completed: false,
          video_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        },
        { 
          id: 4, 
          title: 'Praktické cvičenia', 
          description: 'Aplikácia poznatkov', 
          content: '<h3>Praktické cvičenia</h3><p>Teraz si vyskúšate naučené poznatky na praktických príkladoch.</p>',
          duration_minutes: 30, 
          completed: false,
          video_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        },
        { 
          id: 5, 
          title: 'Záver a certifikát', 
          description: 'Dokončenie kurzu', 
          content: '<h3>Záver kurzu</h3><p>Gratulujeme! Dokončili ste kurz a môžete si stiahnuť certifikát.</p>',
          duration_minutes: 10, 
          completed: false,
          video_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        }
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

const getEmbedUrl = (url) => {
  if (!url) return ''
  
  // YouTube URL conversion
  if (url.includes('youtube.com/watch?v=')) {
    const videoId = url.split('v=')[1].split('&')[0]
    return `https://www.youtube.com/embed/${videoId}`
  }
  
  if (url.includes('youtu.be/')) {
    const videoId = url.split('youtu.be/')[1].split('?')[0]
    return `https://www.youtube.com/embed/${videoId}`
  }
  
  // Vimeo URL conversion
  if (url.includes('vimeo.com/')) {
    const videoId = url.split('vimeo.com/')[1]
    return `https://player.vimeo.com/video/${videoId}`
  }
  
  // Direct video URLs
  return url
}

const getResourceTypeLabel = (type) => {
  const labels = {
    pdf: 'PDF dokument',
    link: 'Webový odkaz',
    download: 'Stiahnutie',
    video: 'Video',
    image: 'Obrázok',
    document: 'Dokument',
    external: 'Externý zdroj'
  }
  return labels[type] || type
}

const toggleLessonCompletion = async (lesson) => {
  try {
    const wasCompleted = lesson.completed
    const newCompletionStatus = !wasCompleted
    
    // Optimistically update UI
    lesson.completed = newCompletionStatus
    
    // Call API to update lesson completion status
    const progressData = {
      completed: newCompletionStatus,
      watch_time_seconds: lesson.watch_time_seconds || 0
    }
    
    const response = await learningService.updateProgress(
      course.value.slug,
      lesson.slug,
      progressData
    )
    
    console.log(`Lesson ${lesson.title} marked as ${newCompletionStatus ? 'completed' : 'incomplete'}`)
    console.log('Progress update response:', response)
    
    // Update lesson with fresh data from server
    if (response.progress) {
      lesson.completed = response.progress.completed
      lesson.watch_time_seconds = response.progress.watch_time_seconds
      lesson.progress_percentage = response.progress.progress_percentage
    }
    
    // Update course progress if enrollment data is returned
    if (response.enrollment) {
      course.value.progress_percentage = response.enrollment.progress_percentage
    }
    
  } catch (error) {
    console.error('Error updating lesson completion:', error)
    // Revert the change if API call fails
    lesson.completed = !lesson.completed
    
    // Show error message to user
    alert('Nastala chyba pri aktualizácii pokroku lekcie. Skúste to znovu.')
  }
}

onMounted(() => {
  loadCourseContent()
})
</script>
