<template>
  <div class="bg-dark-900 border border-dark-800 rounded-3xl p-8 mb-8">
    <h2 class="text-2xl font-black text-white mb-6">Pokračovať v učení</h2>
    
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
    </div>
    
    <div v-else-if="courses.length > 0" class="space-y-4">
      <div
        v-for="course in courses"
        :key="course.id"
        class="group bg-dark-800/30 border border-dark-700/50 rounded-2xl p-5 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/80"
      >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex-1 min-w-0">
            <h3 class="font-bold text-white mb-2 text-lg truncate" :title="course.title">{{ course.title }}</h3>
            <div class="flex items-center gap-4 mb-2">
              <div class="w-full max-w-xs bg-dark-700/50 rounded-full h-2">
                <div
                  class="bg-gradient-to-r from-primary-500 to-secondary-500 h-2 rounded-full transition-all duration-500 ease-out"
                  :style="{ width: `${course.progress}%` }"
                ></div>
              </div>
              <span class="text-sm text-dark-300 font-medium whitespace-nowrap">{{ course.progress }}%</span>
            </div>
          </div>
          <div class="flex-shrink-0">
            <router-link
              v-if="course.progress < 100"
              :to="{ name: 'CourseStudy', params: { slug: course.slug } }"
              class="inline-block w-full sm:w-auto text-center px-6 py-3 bg-primary-600 hover:bg-primary-500 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-primary-500/25 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-900"
              role="button"
            >
              Pokračovať
            </router-link>
            <div
              v-else
              class="inline-block w-full sm:w-auto text-center px-6 py-3 bg-dark-700 text-dark-300 rounded-xl text-sm font-bold cursor-not-allowed"
            >
              Nedostupné
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-12">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-dark-800 flex items-center justify-center">
            <svg class="h-8 w-8 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
      </div>
      <h3 class="mt-2 text-lg font-bold text-white">Zatiaľ žiadne kurzy</h3>
      <p class="mt-1 text-dark-300 font-light">Začnite zapísaním do svojho prvého kurzu.</p>
      <div class="mt-8">
        <button
          @click="$emit('browse')"
          class="px-8 py-4 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-primary-500/25 inline-flex focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-900"
        >
          Prehliadať kurzy
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  courses: {
    type: Array,
    required: true,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

defineEmits(['browse'])
</script>
