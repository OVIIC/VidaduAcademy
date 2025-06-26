<template>
  <!-- Modal Overlay -->
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
  >
    <!-- Background overlay -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        aria-hidden="true"
        @click="closeModal"
      ></div>

      <!-- This element is to trick the browser into centering the modal contents. -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal panel -->
      <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
        <!-- Close button -->
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button
            @click="closeModal"
            type="button"
            class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <span class="sr-only">Zavrieť</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Course Image -->
        <div v-if="course?.thumbnail" class="aspect-video w-full bg-gray-200 rounded-lg overflow-hidden mb-6">
          <img
            :src="course.thumbnail"
            :alt="course.title"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- Course Content -->
        <div>
          <!-- Course title and status -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-bold text-gray-900 mb-2">
                {{ course?.title }}
              </h3>
              <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span v-if="course?.instructor" class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                  {{ course.instructor.name }}
                </span>
                <span v-if="course?.duration_minutes" class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ formatDuration(course.duration_minutes) }}
                </span>
                <span v-if="course?.difficulty_level" :class="getDifficultyBadgeClass(course.difficulty_level)">
                  {{ formatDifficulty(course.difficulty_level) }}
                </span>
              </div>
            </div>
            <div class="ml-4 flex-shrink-0">
              <span class="text-2xl font-bold text-primary-600">
                €{{ course?.price }}
              </span>
            </div>
          </div>

          <!-- Short description -->
          <div v-if="course?.short_description" class="mb-4">
            <p class="text-gray-600 text-lg">{{ course.short_description }}</p>
          </div>

          <!-- Full description -->
          <div v-if="course?.description" class="mb-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-3">O kurze</h4>
            <div class="prose prose-sm max-w-none text-gray-700">
              <p v-for="paragraph in getDescriptionParagraphs(course.description)" :key="paragraph" class="mb-3">
                {{ paragraph }}
              </p>
            </div>
          </div>

          <!-- What you will learn -->
          <div v-if="course?.what_you_will_learn && course.what_you_will_learn.length > 0" class="mb-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-3">Čo sa naučíte</h4>
            <ul class="space-y-2">
              <li v-for="item in course.what_you_will_learn" :key="item" class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-700">{{ item }}</span>
              </li>
            </ul>
          </div>

          <!-- Requirements -->
          <div v-if="course?.requirements && course.requirements.length > 0" class="mb-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-3">Požiadavky</h4>
            <ul class="space-y-2">
              <li v-for="requirement in course.requirements" :key="requirement" class="flex items-start">
                <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-700">{{ requirement }}</span>
              </li>
            </ul>
          </div>

          <!-- Action buttons -->
          <div class="flex space-x-3 pt-4 border-t border-gray-200">
            <button
              v-if="!isAuthenticated"
              @click="goToLogin"
              class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200"
            >
              Prihlásiť sa pre nákup
            </button>
            <button
              v-else
              @click="purchaseCourse"
              class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200"
            >
              Kúpiť kurz za €{{ course?.price }}
            </button>
            <button
              @click="closeModal"
              class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200"
            >
              Zavrieť
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  course: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'purchase'])

const authStore = useAuthStore()
const router = useRouter()

const isAuthenticated = computed(() => !!authStore.user)

const closeModal = () => {
  emit('close')
}

const goToLogin = () => {
  closeModal()
  router.push('/login')
}

const purchaseCourse = () => {
  emit('purchase', props.course)
  closeModal()
}

const formatDuration = (minutes) => {
  if (!minutes) return ''
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

const getDescriptionParagraphs = (description) => {
  if (!description) return []
  return description.split('\n').filter(p => p.trim().length > 0)
}
</script>
