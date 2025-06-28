<template>
  <div class="bg-white dark:bg-gray-900 transition-colors duration-200">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-secondary-900 via-secondary-800 to-primary-900 text-white overflow-hidden">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
          <polygon points="0,100 100,0 100,100" fill="currentColor"/>
        </svg>
      </div>
      
      <div class="relative container-responsive py-12 sm:py-16 lg:py-24">
        <div class="text-center">
          <h1 class="text-3xl sm:text-4xl lg:text-6xl font-bold leading-tight mb-4 sm:mb-6">
            <span class="block">Ovládnite rast</span>
            <span class="text-youtube-red block">YouTube kanála</span>
          </h1>
          
          <p class="text-base sm:text-lg lg:text-xl text-gray-200 mb-6 sm:mb-8 max-w-2xl mx-auto px-4">
            Učte sa od úspešných YouTube tvorcov a premeňte svoj kanál na prosperujúci biznis.
          </p>
          
          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-4">
            <router-link 
              to="/courses" 
              class="btn-primary text-base sm:text-lg px-6 sm:px-8 py-3 sm:py-4 w-full sm:w-auto"
            >
              🎓 Preskúmajte kurzy
            </router-link>
            <router-link 
              to="/register" 
              class="btn-secondary bg-white text-primary-900 hover:bg-gray-100 text-base sm:text-lg px-6 sm:px-8 py-3 sm:py-4 w-full sm:w-auto"
            >
              ✨ Zaregistrovať sa
            </router-link>
          </div>
          
          <!-- Trust indicators -->
          <div class="mt-8 sm:mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-8 text-gray-300">
            <div class="flex items-center">
              <span class="text-lg sm:text-xl font-bold mr-2">1000+</span>
              <span class="text-sm sm:text-base">spokojných študentov</span>
            </div>
            <div class="hidden sm:block w-px h-4 bg-gray-500"></div>
            <div class="flex items-center">
              <span class="text-lg sm:text-xl font-bold mr-2">50+</span>
              <span class="text-sm sm:text-base">hodín obsahu</span>
            </div>
            <div class="hidden sm:block w-px h-4 bg-gray-500"></div>
            <div class="flex items-center">
              <span class="text-lg sm:text-xl font-bold mr-2">4.8★</span>
              <span class="text-sm sm:text-base">priemerné hodnotenie</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Courses -->
    <section class="py-12 sm:py-16 bg-gray-50">
      <div class="container-responsive">
        <div class="text-center mb-8 sm:mb-12">
          <h2 class="heading-2 mb-3 sm:mb-4">
            🌟 Odporúčané kurzy
          </h2>
          <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
            Starostlivo vybrané kurzy od našich najlepších inštruktorov
          </p>
        </div>

        <!-- Loading State -->
        <LoadingState 
          v-if="courseStore.loading" 
          type="courses"
          :count="6"
          class="mb-8"
        />

        <!-- Courses Grid -->
        <div v-else-if="featuredCourses.length > 0" class="grid-responsive">
          <CourseCardMobile
            v-for="course in featuredCourses"
            :key="course.id"
            :course="course"
            :enrollment-data="getEnrollmentData(course.id)"
          />
        </div>

        <!-- No Courses -->
        <div v-else class="text-center py-8 sm:py-12">
          <div class="text-gray-400 text-lg mb-2">Žiadne odporúčané kurzy</div>
          <p class="text-gray-500">Čoskoro pridáme nové kurzy!</p>
        </div>

        <!-- View All Button -->
        <div v-if="featuredCourses.length > 0" class="text-center mt-8 sm:mt-12">
          <router-link to="/courses" class="btn-primary">
            Zobraziť všetky kurzy →
          </router-link>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-12 sm:py-16 bg-white">
      <div class="container-responsive">
        <div class="text-center mb-8 sm:mb-12">
          <h2 class="heading-2 mb-3 sm:mb-4">
            💡 Prečo si vybrať VidaduAcademy?
          </h2>
          <p class="text-base sm:text-lg text-gray-600">
            Všetko čo potrebujete na úspech na YouTube
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          <!-- Feature 1 -->
          <div class="text-center p-4 sm:p-6 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl sm:text-3xl">🎯</span>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">
              Praktické stratégie
            </h3>
            <p class="text-sm sm:text-base text-gray-600">
              Overené techniky od úspešných YouTuberov s miliónmi odberateľov
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="text-center p-4 sm:p-6 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl sm:text-3xl">📱</span>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">
              Mobilné učenie
            </h3>
            <p class="text-sm sm:text-base text-gray-600">
              Učte sa kedykoľvek a kdekoľvek na vašom telefóne alebo tablete
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="text-center p-4 sm:p-6 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl sm:text-3xl">🚀</span>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">
              Rýchle výsledky
            </h3>
            <p class="text-sm sm:text-base text-gray-600">
              Viditeľné výsledky už po prvom týždni implementácie
            </p>
          </div>

          <!-- Feature 4 -->
          <div class="text-center p-4 sm:p-6 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl sm:text-3xl">💎</span>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">
              Premium obsah
            </h3>
            <p class="text-sm sm:text-base text-gray-600">
              Exkluzívne materiály a šablóny nedostupné nikde inde
            </p>
          </div>

          <!-- Feature 5 -->
          <div class="text-center p-4 sm:p-6 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl sm:text-3xl">👥</span>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">
              Komunita
            </h3>
            <p class="text-sm sm:text-base text-gray-600">
              Pripojte sa k aktívnej komunite tvorcov a získajte feedback
            </p>
          </div>

          <!-- Feature 6 -->
          <div class="text-center p-4 sm:p-6 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl sm:text-3xl">🎓</span>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">
              Certifikáty
            </h3>
            <p class="text-sm sm:text-base text-gray-600">
              Získajte certifikáty po dokončení kurzov a ukážte svoje znalosti
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 sm:py-16 bg-primary-600 text-white">
      <div class="container-responsive">
        <div class="text-center mb-8 sm:mb-12">
          <h2 class="heading-2 mb-3 sm:mb-4">
            📊 Naše výsledky hovoria za nás
          </h2>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
          <div class="text-center">
            <div class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">1000+</div>
            <div class="text-sm sm:text-base text-primary-100">Absolventov kurzov</div>
          </div>
          <div class="text-center">
            <div class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">50M+</div>
            <div class="text-sm sm:text-base text-primary-100">Celkových zhliadnutí</div>
          </div>
          <div class="text-center">
            <div class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">98%</div>
            <div class="text-sm sm:text-base text-primary-100">Spokojnosť študentov</div>
          </div>
          <div class="text-center">
            <div class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">24/7</div>
            <div class="text-sm sm:text-base text-primary-100">Prístup k obsahu</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-12 sm:py-16 bg-gray-50">
      <div class="container-responsive">
        <div class="text-center mb-8 sm:mb-12">
          <h2 class="heading-2 mb-3 sm:mb-4">
            💬 Čo hovoria naši študenti
          </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          <!-- Testimonial 1 -->
          <div class="card p-4 sm:p-6">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                M
              </div>
              <div>
                <div class="font-semibold text-gray-900">Martin K.</div>
                <div class="text-sm text-gray-600">Gaming YouTuber</div>
              </div>
            </div>
            <p class="text-sm sm:text-base text-gray-700 italic">
              "Vďaka kurzom som za 3 mesiace zdvojnásobil počet odberateľov. Stratégie naozaj fungujú!"
            </p>
            <div class="flex text-yellow-400 mt-3">
              ★★★★★
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="card p-4 sm:p-6">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                S
              </div>
              <div>
                <div class="font-semibold text-gray-900">Simona T.</div>
                <div class="text-sm text-gray-600">Beauty Blogger</div>
              </div>
            </div>
            <p class="text-sm sm:text-base text-gray-700 italic">
              "Konečne som pochopila ako správne optimalizovať videá. Moje zhliadnutia rastú každý týždeň!"
            </p>
            <div class="flex text-yellow-400 mt-3">
              ★★★★★
            </div>
          </div>

          <!-- Testimonial 3 -->
          <div class="card p-4 sm:p-6 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                P
              </div>
              <div>
                <div class="font-semibold text-gray-900">Peter V.</div>
                <div class="text-sm text-gray-600">Tech Reviewer</div>
              </div>
            </div>
            <p class="text-sm sm:text-base text-gray-700 italic">
              "Najlepšia investícia do môjho kanála. Za pár mesiacov som dosiahol monetizáciu!"
            </p>
            <div class="flex text-yellow-400 mt-3">
              ★★★★★
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 sm:py-16 bg-gradient-to-r from-primary-600 to-secondary-600 text-white">
      <div class="container-responsive text-center">
        <h2 class="heading-2 mb-3 sm:mb-4">
          🚀 Pripravení začať svoju YouTube cestu?
        </h2>
        <p class="text-base sm:text-lg mb-6 sm:mb-8 max-w-2xl mx-auto">
          Pripojte sa k tisíckam úspešných tvorcov, ktorí už transformovali svoje kanály
        </p>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
          <router-link to="/courses" class="btn-secondary bg-white text-primary-900 hover:bg-gray-100">
            Začať teraz
          </router-link>
          <router-link to="/register" class="btn-ghost border-white text-white hover:bg-white hover:text-primary-900">
            Bezplatná registrácia
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useCourseStore } from '@/stores/course'
import { useEnrollmentStore } from '@/stores/enrollment'
import { useAuthStore } from '@/stores/auth'
import CourseCardMobile from '@/components/courses/CourseCardMobile.vue'
import LoadingState from '@/components/ui/LoadingState.vue'

const courseStore = useCourseStore()
const enrollmentStore = useEnrollmentStore()
const authStore = useAuthStore()

const featuredCourses = computed(() => {
  return courseStore.featuredCourses || []
})

const getEnrollmentData = (courseId) => {
  return enrollmentStore.enrollments.find(e => e.course_id === courseId)
}

onMounted(async () => {
  await courseStore.fetchFeaturedCourses()
  
  if (authStore.isAuthenticated) {
    await enrollmentStore.fetchEnrollments()
  }
})
</script>

<style scoped>
/* Hero section improvements */
.hero-pattern {
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

/* Mobile typography improvements */
@media (max-width: 640px) {
  .hero-title {
    line-height: 1.1;
  }
}

/* Touch-friendly improvements */
@media (hover: none) {
  .hover\:bg-gray-50:hover {
    background-color: inherit;
  }
}
</style>
