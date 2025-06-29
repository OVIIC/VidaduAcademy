<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center min-h-screen">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
    </div>

    <!-- Lesson Content -->
    <div v-else-if="lesson" class="flex h-screen">
      <!-- Sidebar - Course Navigation (Collapsed on mobile) -->
      <div class="hidden lg:block w-80 bg-gray-800 border-r border-gray-700 flex flex-col">
        <!-- Course Header -->
        <div class="p-6 border-b border-gray-700">
          <router-link
            :to="{ name: 'course-detail', params: { id: lesson.course.id } }"
            class="text-primary-400 hover:text-primary-300 text-sm mb-2 inline-block"
          >
            ← Back to Course
          </router-link>
          <h1 class="text-lg font-semibold text-white">{{ lesson.course.title }}</h1>
        </div>

        <!-- Current Lesson Info -->
        <div class="p-4 border-b border-gray-700">
          <h2 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">
            Current Lesson
          </h2>
          <h3 class="text-white font-medium">{{ lesson.title }}</h3>
          <p class="text-sm text-gray-400 mt-1">{{ formatDuration(lesson.duration) }}</p>
        </div>

        <!-- Navigation -->
        <div class="flex-1 p-4">
          <div class="space-y-2">
            <button
              @click="previousLesson"
              :disabled="!canGoPrevious"
              class="w-full bg-gray-700 hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200"
            >
              ← Previous Lesson
            </button>
            <button
              @click="nextLesson"
              :disabled="!canGoNext"
              class="w-full bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200"
            >
              Next Lesson →
            </button>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col">
        <!-- Mobile Header -->
        <div class="lg:hidden bg-gray-800 border-b border-gray-700 p-4">
          <div class="flex items-center justify-between">
            <router-link
              :to="{ name: 'CourseStudy', params: { slug: lesson.course.slug } }"
              class="text-primary-400 hover:text-primary-300"
            >
              ← Back
            </router-link>
            <h1 class="text-lg font-semibold text-white truncate mx-4">{{ lesson.title }}</h1>
            <button
              @click="toggleCompleted"
              :class="[
                'px-3 py-1 rounded text-sm font-medium',
                isLessonCompleted
                  ? 'bg-green-600 text-white'
                  : 'bg-gray-600 text-white'
              ]"
            >
              {{ isLessonCompleted ? '✓' : 'Mark Complete' }}
            </button>
          </div>
        </div>

        <!-- Desktop Lesson Header -->
        <div class="hidden lg:block bg-gray-800 border-b border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-white mb-2">{{ lesson.title }}</h1>
              <p class="text-gray-400">{{ lesson.description }}</p>
            </div>
            <div class="flex items-center space-x-4">
              <span class="text-sm text-gray-400">{{ formatDuration(lesson.duration) }}</span>
              <button
                @click="toggleCompleted"
                :class="[
                  'px-4 py-2 rounded-lg text-sm font-medium transition duration-200',
                  isLessonCompleted
                    ? 'bg-green-600 hover:bg-green-700 text-white'
                    : 'bg-gray-600 hover:bg-gray-500 text-white'
                ]"
              >
                {{ isLessonCompleted ? 'Completed' : 'Mark Complete' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Lesson Content -->
        <div class="flex-1 overflow-y-auto">
          <div class="max-w-4xl mx-auto p-6">
            <!-- Video Player Placeholder -->
            <div class="aspect-video bg-gray-800 rounded-lg mb-8 flex items-center justify-center">
              <div class="text-center">
                <svg class="w-20 h-20 text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8 12.5l5-3-5-3v6z"/>
                  <path d="M10 18a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                <p class="text-gray-400">Video Player</p>
                <p class="text-sm text-gray-500 mt-1">{{ lesson.title }}</p>
                <button class="mt-4 bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg transition duration-200">
                  Play Video
                </button>
              </div>
            </div>

            <!-- Lesson Content -->
            <div class="space-y-8">
              <!-- Overview -->
              <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Lesson Overview</h2>
                <p class="text-gray-300 leading-relaxed">
                  {{ lesson.description || 'This lesson covers important concepts that will help you master the skills needed for YouTube growth and channel management. Follow along with the video and complete the exercises to reinforce your learning.' }}
                </p>
              </div>

              <!-- Learning Objectives -->
              <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Learning Objectives</h3>
                <ul class="space-y-3 text-gray-300">
                  <li class="flex items-start">
                    <svg class="w-5 h-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Understand the key concepts presented in this lesson
                  </li>
                  <li class="flex items-start">
                    <svg class="w-5 h-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Apply practical techniques to your YouTube channel
                  </li>
                  <li class="flex items-start">
                    <svg class="w-5 h-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Implement strategies for improved channel performance
                  </li>
                  <li class="flex items-start">
                    <svg class="w-5 h-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Measure and analyze your progress effectively
                  </li>
                </ul>
              </div>

              <!-- Key Takeaways -->
              <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Key Takeaways</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="bg-gray-700 rounded-lg p-4">
                    <h4 class="text-white font-medium mb-2">💡 Pro Tip</h4>
                    <p class="text-gray-300 text-sm">Focus on consistency over perfection when implementing new strategies.</p>
                  </div>
                  <div class="bg-gray-700 rounded-lg p-4">
                    <h4 class="text-white font-medium mb-2">⚠️ Common Mistake</h4>
                    <p class="text-gray-300 text-sm">Avoid making too many changes at once - implement gradually.</p>
                  </div>
                  <div class="bg-gray-700 rounded-lg p-4">
                    <h4 class="text-white font-medium mb-2">📊 Metrics to Track</h4>
                    <p class="text-gray-300 text-sm">Monitor engagement rates, watch time, and subscriber growth.</p>
                  </div>
                  <div class="bg-gray-700 rounded-lg p-4">
                    <h4 class="text-white font-medium mb-2">🎯 Action Item</h4>
                    <p class="text-gray-300 text-sm">Create an action plan based on the lesson content.</p>
                  </div>
                </div>
              </div>

              <!-- Resources -->
              <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Additional Resources</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <a href="#" class="block bg-gray-700 hover:bg-gray-600 rounded-lg p-4 transition duration-200">
                    <div class="flex items-center">
                      <div class="text-2xl mr-3">📄</div>
                      <div>
                        <h4 class="text-white font-medium">Lesson Notes</h4>
                        <p class="text-gray-400 text-sm">Download summary and key points</p>
                      </div>
                    </div>
                  </a>
                  <a href="#" class="block bg-gray-700 hover:bg-gray-600 rounded-lg p-4 transition duration-200">
                    <div class="flex items-center">
                      <div class="text-2xl mr-3">💾</div>
                      <div>
                        <h4 class="text-white font-medium">Templates</h4>
                        <p class="text-gray-400 text-sm">Downloadable tools and templates</p>
                      </div>
                    </div>
                  </a>
                  <a href="#" class="block bg-gray-700 hover:bg-gray-600 rounded-lg p-4 transition duration-200">
                    <div class="flex items-center">
                      <div class="text-2xl mr-3">🔗</div>
                      <div>
                        <h4 class="text-white font-medium">External Links</h4>
                        <p class="text-gray-400 text-sm">Useful references and tools</p>
                      </div>
                    </div>
                  </a>
                  <a href="#" class="block bg-gray-700 hover:bg-gray-600 rounded-lg p-4 transition duration-200">
                    <div class="flex items-center">
                      <div class="text-2xl mr-3">❓</div>
                      <div>
                        <h4 class="text-white font-medium">Quiz</h4>
                        <p class="text-gray-400 text-sm">Test your knowledge</p>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="lg:hidden bg-gray-800 border-t border-gray-700 p-4">
          <div class="flex space-x-4">
            <button
              @click="previousLesson"
              :disabled="!canGoPrevious"
              class="flex-1 bg-gray-700 hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200"
            >
              ← Previous
            </button>
            <button
              @click="nextLesson"
              :disabled="!canGoNext"
              class="flex-1 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200"
            >
              Next →
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Lesson not found -->
    <div v-else class="flex justify-center items-center min-h-screen">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-white mb-2">Lesson Not Found</h1>
        <p class="text-gray-400 mb-4">The lesson you're looking for doesn't exist or you don't have access.</p>
        <router-link to="/my-courses" class="text-primary-400 hover:text-primary-300">
          Back to My Courses
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '@/services/api'

export default {
  name: 'LessonView',
  setup() {
    const route = useRoute()
    const router = useRouter()
    
    const loading = ref(false)
    const lesson = ref(null)
    const isLessonCompleted = ref(false)

    const canGoPrevious = computed(() => {
      return lesson.value?.previous_lesson_id != null
    })

    const canGoNext = computed(() => {
      return lesson.value?.next_lesson_id != null
    })

    const formatDuration = (minutes) => {
      if (!minutes) return '0 min'
      const hours = Math.floor(minutes / 60)
      const mins = minutes % 60
      return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`
    }

    const loadLesson = async () => {
      loading.value = true
      try {
        const response = await api.get(`/learning/lessons/${route.params.lessonId}`)
        lesson.value = response.data.lesson
        isLessonCompleted.value = response.data.completed || false
      } catch (error) {
        console.error('Error loading lesson:', error)
        router.push('/my-courses')
      } finally {
        loading.value = false
      }
    }

    const previousLesson = () => {
      if (!canGoPrevious.value) return
      router.push({
        name: 'lesson',
        params: {
          courseId: route.params.courseId,
          lessonId: lesson.value.previous_lesson_id
        }
      })
    }

    const nextLesson = () => {
      if (!canGoNext.value) return
      router.push({
        name: 'lesson',
        params: {
          courseId: route.params.courseId,
          lessonId: lesson.value.next_lesson_id
        }
      })
    }

    const toggleCompleted = async () => {
      if (!lesson.value) return
      
      try {
        const response = await api.post('/learning/progress', {
          lesson_id: lesson.value.id,
          completed: !isLessonCompleted.value
        })
        
        isLessonCompleted.value = response.data.completed
      } catch (error) {
        console.error('Error updating progress:', error)
      }
    }

    onMounted(() => {
      loadLesson()
    })

    return {
      loading,
      lesson,
      isLessonCompleted,
      canGoPrevious,
      canGoNext,
      formatDuration,
      previousLesson,
      nextLesson,
      toggleCompleted
    }
  }
}
</script>
