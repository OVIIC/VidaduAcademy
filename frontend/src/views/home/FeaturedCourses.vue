<template>
  <section class="py-24 relative overflow-hidden z-10">
    <div class="relative z-10 mb-4 px-4 max-w-7xl mx-auto">
      <div class="text-center max-w-3xl mx-auto mb-6">
        <h2 class="text-3xl sm:text-4xl font-black text-white mb-6">
          Najdetailnejšie kurzy ktoré nájdeš online
        </h2>
        <p class="text-sm text-dark-50 font-extralight">
          Odhalili sme stratégie, ktoré fungujú tým najlepším, a zabalili ich do zrozumiteľnej formy. Žiadna zbytočná teória, iba funkčné postupy.
        </p>
      </div>

      <!-- Category Selector -->
      <div class="flex flex-wrap justify-center gap-3 mb-4">
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

    <!-- 3D Carousel Container -->
    <div class="relative w-full overflow-hidden pb-4 pt-0" style="min-height: 500px;">
      
      <!-- Loading State -->
      <div v-if="courseStore.loading" class="flex justify-center items-center h-[400px]">
         <div class="w-12 h-12 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredFeaturedCourses.length === 0" class="w-full text-center text-dark-400 py-10">
        Pre túto kategóriu sa nenašli žiadne kurzy.
      </div>

      <!-- 3D Scene - Exact implementation from snippet -->
      <div v-else class="scene">
        <div class="a3d" :style="{ '--n': filteredFeaturedCourses.length }">
          <div 
            v-for="(course, index) in filteredFeaturedCourses" 
            :key="course.id"
            class="card-container"
            :style="{ '--i': index }"
          >
             <!-- Using CourseCard inside the 3D transformed container -->
             <!-- We need to ensure the card takes full width/height of the container -->
            <CourseCard
              :course="course"
              :show-duration="false"
              :enable-hover-effects="true"
              class="w-full h-full shadow-2xl"
            />
          </div>
        </div>
      </div>

    </div>

    <div class="mt-4 text-center relative z-10">
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
/* Relevant CSS from snippet */
.scene, .a3d { 
  display: grid;
}

.scene {
  /* prevent scrollbars */
  overflow: hidden;
  /* for 3D look; smaller = more extreme effect */
  perspective: 35em;
  /* Mask commented out as it might interfere with dark mode or needs specific adjustment */
  /* mask: linear-gradient(90deg, #0000, red 20% 80%, #0000); */
  width: 100%;
  padding-top: 2rem;
  padding-bottom: 2rem;
}

.a3d {
  place-self: center; /* middle align */
  /* don't flatten 3D transformed children of this parent having its own 3D transform */
  transform-style: preserve-3d;
  animation: ry 60s linear infinite;
}

/* simplest y axis rotation */
@keyframes ry { 
  to { rotate: y 1turn; } 
}

.card-container {
  /* base card width */
  --w: 17.5em;
  /* compute base angle corresponding to a card */
  --ba: 1turn/var(--n);
  
  grid-area: 1/1; /* stack in same one grid cell */
  width: var(--w);
  aspect-ratio: 7/10;
  
  /* don't want to see back of cards in front of screen plane */
  backface-visibility: hidden;
  
  /* transform chain */
  transform: 
    rotateY(calc(var(--i) * var(--ba)))
    translateZ(calc(-1 * (0.5 * var(--w) + 15em) / tan(0.5 * var(--ba))));
}

.card-container:hover {
    /* Optional: pause or effect on hover could go here, 
       but note that pausing the parent .a3d on hover is usually better UX */
}

/* Pause animation on hover */
.a3d:hover {
  animation-play-state: paused;
}

@media (prefers-reduced-motion: reduce) {
  .a3d { animation-duration: 128s; }
}
</style>
