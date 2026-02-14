<template>
  <div class="bg-dark-900 border border-dark-800 rounded-3xl p-8">
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-2xl font-black text-white">Tvoje kurzy</h2>
      <router-link
        v-if="showViewAll"
        to="/my-courses"
        class="px-4 py-2 text-sm font-bold text-dark-300 bg-dark-800 rounded-xl hover:bg-dark-700 hover:text-white transition-all focus:outline-none focus:ring-2 focus:ring-dark-600"
      >
        Zobraziť všetky
      </router-link>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
    </div>
    
    <!-- Courses Grid -->
    <div v-else-if="courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <router-link
        v-for="course in displayedCourses"
        :key="course.id"
        :to="{ name: 'CourseStudy', params: { slug: course.slug } }"
        class="group bg-dark-800/30 border border-dark-700/50 rounded-2xl p-4 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/80 block focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-900"
      >
        <div class="flex items-start space-x-4">
          <!-- Course Thumbnail -->
          <div class="flex-shrink-0">
            <img
              v-if="course.thumbnail"
              :src="course.thumbnail"
              :alt="course.title"
              class="w-20 h-20 object-cover rounded-xl shadow-md group-hover:shadow-primary-500/10 transition-shadow"
            >
            <div
              v-else
              class="w-20 h-20 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-xl flex items-center justify-center shadow-md"
            >
              <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          
          <!-- Course Info -->
          <div class="flex-1 min-w-0 py-1">
            <h3 class="font-bold text-white truncate mb-1 group-hover:text-primary-400 transition-colors" :title="course.title">{{ course.title }}</h3>
            <p v-if="course.instructor?.name" class="text-xs text-dark-400 font-medium uppercase tracking-wider mb-3">{{ course.instructor.name }}</p>
            
            <!-- Progress Bar -->
            <div class="w-full bg-dark-700/50 rounded-full h-1.5 mb-2">
              <div
                class="bg-gradient-to-r from-primary-500 to-secondary-500 h-1.5 rounded-full transition-all duration-300"
                :style="{ width: `${course.enrollment_data?.progress_percentage || 0}%` }"
              ></div>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-xs text-dark-300 font-medium">
                {{ course.enrollment_data?.progress_percentage || 0 }}% dokončené
              </span>
            </div>
          </div>
        </div>
      </router-link>
    </div>
    
    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-dark-800 flex items-center justify-center">
        <svg class="h-8 w-8 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
      </div>
      <h3 class="mt-2 text-lg font-bold text-white">Žiadne zakúpené kurzy</h3>
      <p class="mt-1 text-dark-300 font-light">Začnite objavovaním našich kurzov.</p>
      <div class="mt-6">
        <button
          @click="$emit('browse')"
          class="px-6 py-3 bg-dark-800/50 hover:bg-dark-800 text-white font-bold rounded-xl border border-dark-700 hover:border-dark-600 transition-all text-sm inline-flex items-center focus:outline-none focus:ring-2 focus:ring-dark-600"
        >
          Prehliadať kurzy
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  courses: {
    type: Array,
    required: true,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  limit: {
    type: Number,
    default: 4
  },
  showViewAll: {
    type: Boolean,
    default: true
  }
})

defineEmits(['browse'])

const displayedCourses = computed(() => {
    if (props.limit <= 0) return props.courses
    return props.courses.slice(0, props.limit)
})
</script>
