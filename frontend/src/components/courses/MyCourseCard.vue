<template>
  <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-200">
    <!-- Course Image -->
    <div class="aspect-video bg-gray-200 relative">
      <img
        v-if="course.thumbnail"
        :src="course.thumbnail"
        :alt="course.title"
        class="w-full h-full object-cover"
      />
      <div class="absolute inset-0 bg-black bg-opacity-20"></div>
      
      <!-- Progress overlay -->
      <div class="absolute bottom-0 left-0 right-0 bg-white bg-opacity-90 p-2">
        <div class="flex items-center justify-between text-xs">
          <span class="text-gray-600">Pokrok</span>
          <span class="font-semibold text-primary-600">{{ progressPercentage }}%</span>
        </div>
        <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
          <div 
            class="bg-primary-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: progressPercentage + '%' }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Course Content -->
    <div class="p-4">
      <div class="flex items-start justify-between mb-2">
        <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
          {{ course.title }}
        </h3>
        <span 
          :class="statusBadgeClass"
          class="ml-2 px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap"
        >
          {{ statusText }}
        </span>
      </div>

      <p class="text-sm text-gray-600 mb-3 line-clamp-2">
        {{ course.short_description }}
      </p>

      <!-- Instructor info -->
      <div class="flex items-center mb-3 text-sm text-gray-500">
        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
        </svg>
        <span>{{ course.instructor?.name }}</span>
      </div>

      <!-- Enrollment info -->
      <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
        <span>Zapísaný: {{ formatDate(course.enrollment_data?.enrolled_at) }}</span>
        <span v-if="course.enrollment_data?.last_accessed_at">
          Posledný prístup: {{ formatDate(course.enrollment_data.last_accessed_at) }}
        </span>
      </div>

      <!-- Action buttons -->
      <div class="flex space-x-2">
        <router-link
          :to="{ name: 'CourseDetail', params: { slug: course.slug } }"
          :class="[
            'flex-1 text-center px-4 py-2 rounded-lg text-sm font-medium transition duration-200',
            actionButtonClass
          ]"
        >
          {{ actionText }}
        </router-link>
        
        <button
          v-if="isCompleted"
          @click="viewCertificate"
          class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200"
        >
          Certifikát
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  course: {
    type: Object,
    required: true
  }
})

const progressPercentage = computed(() => {
  return props.course.enrollment_data?.progress_percentage || 0
})

const isCompleted = computed(() => {
  return progressPercentage.value >= 100
})

const isInProgress = computed(() => {
  return progressPercentage.value > 0 && progressPercentage.value < 100
})

const isNotStarted = computed(() => {
  return progressPercentage.value === 0
})

const statusText = computed(() => {
  if (isCompleted.value) return 'Dokončené'
  if (isInProgress.value) return 'Prebiehajúce'
  return 'Nezačaté'
})

const statusBadgeClass = computed(() => {
  if (isCompleted.value) return 'bg-green-100 text-green-800'
  if (isInProgress.value) return 'bg-blue-100 text-blue-800'
  return 'bg-gray-100 text-gray-800'
})

const actionText = computed(() => {
  if (isCompleted.value) return 'Opakovať'
  if (isInProgress.value) return 'Pokračovať'
  return 'Začať'
})

const actionButtonClass = computed(() => {
  if (isCompleted.value) return 'bg-green-600 hover:bg-green-700 text-white'
  if (isInProgress.value) return 'bg-primary-600 hover:bg-primary-700 text-white'
  return 'bg-primary-600 hover:bg-primary-700 text-white'
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  
  const date = new Date(dateString)
  return date.toLocaleDateString('sk-SK', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const viewCertificate = () => {
  // TODO: Implement certificate viewing
  console.log('Viewing certificate for course:', props.course.title)
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
