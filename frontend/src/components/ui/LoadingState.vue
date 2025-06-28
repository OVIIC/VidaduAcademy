<template>
  <div :class="containerClass">
    <!-- Skeleton Loading for Course Cards -->
    <template v-if="type === 'courses'">
      <div class="grid-responsive">
        <div v-for="i in count" :key="i" class="course-card animate-pulse">
          <div class="h-40 sm:h-48 bg-gray-200 rounded-t-lg"></div>
          <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            <div class="flex justify-between items-center">
              <div class="h-6 bg-gray-200 rounded w-1/4"></div>
              <div class="h-8 bg-gray-200 rounded w-20"></div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Skeleton Loading for Text Content -->
    <template v-else-if="type === 'text'">
      <div class="animate-pulse space-y-4">
        <div v-for="i in count" :key="i" class="space-y-2">
          <div class="h-4 bg-gray-200 rounded w-full"></div>
          <div class="h-4 bg-gray-200 rounded w-5/6"></div>
          <div class="h-4 bg-gray-200 rounded w-4/6"></div>
        </div>
      </div>
    </template>

    <!-- Spinner Loading -->
    <template v-else-if="type === 'spinner'">
      <div class="flex justify-center items-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 sm:h-12 sm:w-12 border-b-2 border-primary-600"></div>
        <span v-if="message" class="ml-3 text-gray-600">{{ message }}</span>
      </div>
    </template>

    <!-- Dots Loading -->
    <template v-else-if="type === 'dots'">
      <div class="flex justify-center items-center py-8 space-x-2">
        <div 
          v-for="i in 3" 
          :key="i"
          class="w-2 h-2 sm:w-3 sm:h-3 bg-primary-600 rounded-full animate-bounce"
          :style="{ animationDelay: `${i * 0.1}s` }"
        ></div>
        <span v-if="message" class="ml-3 text-gray-600">{{ message }}</span>
      </div>
    </template>

    <!-- Button Loading -->
    <template v-else-if="type === 'button'">
      <div class="flex items-center justify-center">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>{{ message || 'Načítava...' }}</span>
      </div>
    </template>

    <!-- Custom Content Loading -->
    <template v-else>
      <slot />
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'spinner',
    validator: value => ['courses', 'text', 'spinner', 'dots', 'button', 'custom'].includes(value)
  },
  count: {
    type: Number,
    default: 6
  },
  message: {
    type: String,
    default: ''
  },
  class: {
    type: String,
    default: ''
  }
})

const containerClass = computed(() => {
  return props.class || ''
})
</script>

<style scoped>
@keyframes bounce {
  0%, 80%, 100% {
    transform: scale(0);
  }
  40% {
    transform: scale(1);
  }
}

.animate-bounce {
  animation: bounce 1.4s infinite ease-in-out both;
}
</style>
