<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center min-h-screen">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
    </div>

    <!-- Course Learning Interface -->
    <div v-else-if="course" class="flex h-screen">
      <!-- Sidebar - Course Navigation -->
      <div class="w-80 bg-gray-800 border-r border-gray-700 flex flex-col">
        <!-- Course Header -->
        <div class="p-6 border-b border-gray-700">
          <router-link
            :to="{ name: 'course-detail', params: { id: course.id } }"
            class="text-primary-400 hover:text-primary-300 text-sm mb-2 inline-block"
          >
            ← Back to Course
          </router-link>
          <h1 class="text-lg font-semibold text-white">{{ course.title }}</h1>
          <div class="mt-3">
            <div class="flex justify-between text-sm text-gray-400 mb-1">
              <span>Progress</span>
              <span>{{ Math.round(courseProgress) }}%</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-2">
              <div
                class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${courseProgress}%` }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Lessons List -->
        <div class="flex-1 overflow-y-auto">
          <div class="p-4">
            <h2 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-4">
              Course Content
            </h2>
            <div class="space-y-2">
              <div
                v-for="(lesson, index) in course.lessons"
                :key="lesson.id"
                @click="selectLesson(lesson)"
                :class="[
                  'p-3 rounded-lg cursor-pointer transition duration-200',
                  currentLesson?.id === lesson.id
                    ? 'bg-primary-600 text-white'
                    : 'bg-gray-700 hover:bg-gray-600 text-gray-300'
                ]"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div
                      :class="[
                        'w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium',
                        getLessonStatus(lesson.id) === 'completed'
                          ? 'bg-green-500 text-white'
                          : getLessonStatus(lesson.id) === 'current'
                          ? 'bg-primary-500 text-white'
                          : 'bg-gray-600 text-gray-400'
                      ]"
                    >
                      <svg v-if="getLessonStatus(lesson.id) === 'completed'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                      </svg>
                      <span v-else>{{ index + 1 }}</span>
                    </div>
                    <div class="flex-1">
                      <h3 class="text-sm font-medium">{{ lesson.title }}</h3>
                      <p class="text-xs opacity-75">{{ formatDuration(lesson.duration) }}</p>
                    </div>
                  </div>
                  <div v-if="currentLesson?.id === lesson.id" class="text-primary-300">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M8 12.5l5-3-5-3v6z"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Controls -->
        <div class="p-4 border-t border-gray-700">
          <div class="flex space-x-2">
            <button
              @click="previousLesson"
              :disabled="!canGoPrevious"
              class="flex-1 bg-gray-700 hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200"
            >
              Previous
            </button>
            <button
              @click="nextLesson"
              :disabled="!canGoNext"
              class="flex-1 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col">
        <!-- Lesson Header -->
        <div class="bg-gray-800 border-b border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-white mb-2">{{ currentLesson?.title }}</h1>
              <p class="text-gray-400">{{ currentLesson?.description }}</p>
            </div>
            <div class="flex items-center space-x-4">
              <button
                @click="toggleCompleted"
                :class="[
                  'px-4 py-2 rounded-lg text-sm font-medium transition duration-200',
                  isCurrentLessonCompleted
                    ? 'bg-green-600 hover:bg-green-700 text-white'
                    : 'bg-gray-600 hover:bg-gray-500 text-white'
                ]"
              >
                {{ isCurrentLessonCompleted ? 'Completed' : 'Mark Complete' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Lesson Content -->
        <div class="flex-1 overflow-y-auto p-6">
          <div v-if="currentLesson" class="max-w-4xl mx-auto">
            <!-- Video Player Placeholder -->
            <div class="aspect-video bg-gray-800 rounded-lg mb-8 flex items-center justify-center">
              <div class="text-center">
                <svg class="w-20 h-20 text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8 12.5l5-3-5-3v6z"/>
                  <path d="M10 18a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <p class="text-gray-400">Video Player</p>
                <p class="text-sm text-gray-500 mt-1">{{ currentLesson.title }}</p>
              </div>
            </div>

            <!-- Lesson Content -->
            <div class="prose prose-invert max-w-none">
              <div class="bg-gray-800 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-white mb-4">Lesson Overview</h2>
                <p class="text-gray-300 leading-relaxed">
                  {{ currentLesson.description || 'Learn the essential concepts and techniques covered in this lesson. This comprehensive guide will walk you through practical examples and real-world applications.' }}
                </p>
              </div>

              <!-- Key Points -->
              <div class="bg-gray-800 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-4">Key Learning Points</h3>
                <ul class="space-y-2 text-gray-300">
                  <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                    </svg>
                    Understanding the core concepts and fundamentals
                  </li>
                  <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                    </svg>
                    Practical implementation and real-world examples
                  </li>
                  <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                    </svg>
                    Best practices and optimization techniques
                  </li>
                  <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                    </svg>
                    Common pitfalls and how to avoid them
                  </li>
                </ul>
              </div>

              <!-- Resources -->
              <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Additional Resources</h3>
                <div class="space-y-3">
                  <a href="#" class="block text-primary-400 hover:text-primary-300 transition duration-200">
                    📄 Lesson Notes & Summary
                  </a>
                  <a href="#" class="block text-primary-400 hover:text-primary-300 transition duration-200">
                    💾 Downloadable Resources
                  </a>
                  <a href="#" class="block text-primary-400 hover:text-primary-300 transition duration-200">
                    🔗 External References
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Course not found -->
    <div v-else class="flex justify-center items-center min-h-screen">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-white mb-2">Course Not Found</h1>
        <p class="text-gray-400 mb-4">The course you're looking for doesn't exist or you don't have access.</p>
        <router-link to="/my-courses" class="text-primary-400 hover:text-primary-300">
          Back to My Courses
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '@/services/api'

export default {
  name: 'LearnView',
  setup() {
    const route = useRoute()
    const router = useRouter()
    // const authStore = useAuthStore()
    
    const loading = ref(false)
    const course = ref(null)
    const currentLesson = ref(null)
    const lessonProgress = ref([])

    const courseProgress = computed(() => {
      if (!course.value?.lessons || lessonProgress.value.length === 0) return 0
      const completedLessons = lessonProgress.value.filter(p => p.completed).length
      return (completedLessons / course.value.lessons.length) * 100
    })

    const isCurrentLessonCompleted = computed(() => {
      if (!currentLesson.value) return false
      return lessonProgress.value.some(p => 
        p.lesson_id === currentLesson.value.id && p.completed
      )
    })

    const canGoPrevious = computed(() => {
      if (!course.value?.lessons || !currentLesson.value) return false
      const currentIndex = course.value.lessons.findIndex(l => l.id === currentLesson.value.id)
      return currentIndex > 0
    })

    const canGoNext = computed(() => {
      if (!course.value?.lessons || !currentLesson.value) return false
      const currentIndex = course.value.lessons.findIndex(l => l.id === currentLesson.value.id)
      return currentIndex < course.value.lessons.length - 1
    })

    const formatDuration = (minutes) => {
      if (!minutes) return '0 min'
      const hours = Math.floor(minutes / 60)
      const mins = minutes % 60
      return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`
    }

    const getLessonStatus = (lessonId) => {
      if (!currentLesson.value) return 'pending'
      if (lessonProgress.value.some(p => p.lesson_id === lessonId && p.completed)) {
        return 'completed'
      }
      if (currentLesson.value.id === lessonId) {
        return 'current'
      }
      return 'pending'
    }

    const loadCourse = async () => {
      loading.value = true
      try {
        const response = await api.get(`/learning/courses/${route.params.courseId}`)
        course.value = response.data.course
        lessonProgress.value = response.data.progress || []
        
        // Set current lesson from URL or first lesson
        if (route.params.lessonId) {
          const lesson = course.value.lessons.find(l => l.id === parseInt(route.params.lessonId))
          if (lesson) {
            currentLesson.value = lesson
          } else {
            currentLesson.value = course.value.lessons[0]
          }
        } else {
          currentLesson.value = course.value.lessons[0]
        }
      } catch (error) {
        console.error('Error loading course:', error)
        router.push('/my-courses')
      } finally {
        loading.value = false
      }
    }

    const selectLesson = (lesson) => {
      currentLesson.value = lesson
      router.replace({
        name: 'lesson',
        params: {
          courseId: route.params.courseId,
          lessonId: lesson.id
        }
      })
    }

    const previousLesson = () => {
      if (!canGoPrevious.value) return
      const currentIndex = course.value.lessons.findIndex(l => l.id === currentLesson.value.id)
      selectLesson(course.value.lessons[currentIndex - 1])
    }

    const nextLesson = () => {
      if (!canGoNext.value) return
      const currentIndex = course.value.lessons.findIndex(l => l.id === currentLesson.value.id)
      selectLesson(course.value.lessons[currentIndex + 1])
    }

    const toggleCompleted = async () => {
      if (!currentLesson.value) return
      
      try {
        const response = await api.post('/learning/progress', {
          lesson_id: currentLesson.value.id,
          completed: !isCurrentLessonCompleted.value
        })
        
        // Update local progress
        const existingIndex = lessonProgress.value.findIndex(p => p.lesson_id === currentLesson.value.id)
        if (existingIndex >= 0) {
          lessonProgress.value[existingIndex] = response.data
        } else {
          lessonProgress.value.push(response.data)
        }
      } catch (error) {
        console.error('Error updating progress:', error)
      }
    }

    // Watch for lesson changes in URL
    watch(() => route.params.lessonId, (newLessonId) => {
      if (newLessonId && course.value) {
        const lesson = course.value.lessons.find(l => l.id === parseInt(newLessonId))
        if (lesson) {
          currentLesson.value = lesson
        }
      }
    })

    onMounted(() => {
      loadCourse()
    })

    return {
      loading,
      course,
      currentLesson,
      courseProgress,
      isCurrentLessonCompleted,
      canGoPrevious,
      canGoNext,
      formatDuration,
      getLessonStatus,
      selectLesson,
      previousLesson,
      nextLesson,
      toggleCompleted
    }
  }
}
</script>
