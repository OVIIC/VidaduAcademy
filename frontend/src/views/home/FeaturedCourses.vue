<template>
  <section class="py-24 relative overflow-hidden z-10">
    <div class="relative z-10 mb-12 px-4 max-w-7xl mx-auto">
      <div class="text-center max-w-3xl mx-auto mb-10">
        <h2 class="text-3xl sm:text-4xl font-black text-white mb-6">
          Najdetailnejšie kurzy ktoré nájdeš online
        </h2>
        <p class="text-sm text-dark-50 font-extralight">
          Odhalili sme stratégie, ktoré fungujú tým najlepším, a zabalili ich do zrozumiteľnej formy. Žiadna zbytočná teória, iba funkčné postupy.
        </p>
      </div>

      <!-- Category Selector -->
      <div class="flex flex-wrap justify-center gap-3 mb-8">
        <button
          v-for="category in categories"
          :key="category.id"
          @click="activeCategory = category.id"
          class="px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300"
          :class="[
            activeCategory === category.id
              ? 'bg-white text-dark-950 shadow-lg scale-105'
              : 'bg-dark-800/50 text-dark-300 hover:bg-dark-700 border border-dark-700/50 hover:text-white'
          ]"
        >
          {{ category.label }}
        </button>
      </div>
    </div>

    <!-- Marquee Container -->
    <div class="relative w-full">
      <!-- Fade Masks -->
      <div class="absolute left-0 top-0 bottom-0 w-32 bg-gradient-to-r from-dark-950 to-transparent z-20 pointer-events-none"></div>
      <div class="absolute right-0 top-0 bottom-0 w-32 bg-gradient-to-l from-dark-950 to-transparent z-20 pointer-events-none"></div>

      <!-- Marquee Track -->
      <div class="flex marquee-container">
        <!-- First Set -->
        <div class="flex gap-8 px-4 flex-shrink-0">
          <div v-if="courseStore.loading" class="flex gap-8">
            <div v-for="i in 4" :key="`skeleton-${i}`" class="w-[350px] bg-dark-800 rounded-2xl h-[420px] animate-pulse"></div>
          </div>
          <div v-else-if="filteredFeaturedCourses.length === 0" class="w-full text-center text-dark-400 py-10">
            Pre túto kategóriu sa nenašli žiadne kurzy.
          </div>
          <CourseCard
            v-else
            v-for="course in filteredFeaturedCourses"
            :key="`first-${course.id}`"
            :course="course"
            :show-duration="false"
            :enable-hover-effects="false"
            class="w-[350px] flex-shrink-0 transform transition-transform duration-300"
          />
        </div>

        <!-- Second Set (Duplicate for seamless loop - only if we have enough items) -->
        <div v-if="filteredFeaturedCourses.length > 2" class="flex gap-8 px-4 flex-shrink-0" aria-hidden="true">
          <div v-if="courseStore.loading" class="flex gap-8">
            <div v-for="i in 4" :key="`skeleton-dup-${i}`" class="w-[350px] bg-dark-800 rounded-2xl h-[420px] animate-pulse"></div>
          </div>
          <CourseCard
            v-else
            v-for="course in filteredFeaturedCourses"
            :key="`second-${course.id}`"
            :course="course"
            :show-duration="false"
            :enable-hover-effects="false"
            class="w-[350px] flex-shrink-0 transform transition-transform duration-300"
          />
        </div>
      </div>
    </div>

    <div class="mt-12 text-center relative z-10">
      <router-link to="/courses" class="px-8 py-4 bg-[rgb(237,111,85)] hover:bg-[rgb(220,100,75)] text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-[rgb(237,111,85)]/25 hover:-translate-y-1 inline-flex items-center gap-2 group">
        <span>Všetky kurzy</span>
        <ArrowRightIcon class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
      </router-link>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { ArrowRightIcon } from '@heroicons/vue/24/outline'
import CourseCard from '@/components/courses/CourseCard.vue'
import { useCourseStore } from '@/stores/course'

const courseStore = useCourseStore()
const activeCategory = ref('all')

const categories = [
  { id: 'all', label: 'Všetky' },
  { id: 'youtube', label: 'YouTube Rast' },
  { id: 'editing', label: 'Strih & Editácia' },
  { id: 'monetization', label: 'Monetizácia' },
  { id: 'strategy', label: 'Stratégia' }
]

const filteredFeaturedCourses = computed(() => {
  if (activeCategory.value === 'all') {
    return courseStore.featuredCourses
  }

  const keywords = {
    youtube: ['rast', 'youtube', 'subscribers', 'views', 'kanál', 'growth', 'channel'],
    editing: ['strih', 'edit', 'davinci', 'premiere', 'capcut', 'video'],
    monetization: ['peniaze', 'zarabaj', 'monetizacia', 'business', 'zárobok', 'monetization', 'income'],
    strategy: ['strategia', 'plan', 'analyza', 'úspech', 'strategy', 'masterclass', 'content']
  }

  const targetKeywords = keywords[activeCategory.value] || []

  return courseStore.featuredCourses.filter(course => {
    const text = `${course.title} ${course.short_description || ''}`.toLowerCase()
    return targetKeywords.some(keyword => text.includes(keyword))
  })
})

onMounted(() => {
  courseStore.fetchFeaturedCourses()
})
</script>

<style scoped>
/* Marquee Animation */
.marquee-container {
  animation: marquee 40s linear infinite;
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
</style>
