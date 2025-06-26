<template>
  <div class="bg-white">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-secondary-900 via-secondary-800 to-primary-900 text-white">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="text-center">
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
            Ovládnite rast YouTube
            <span class="text-youtube-red block">Vybudujte svoj kanál</span>
          </h1>
          <p class="text-xl lg:text-2xl text-gray-200 mb-8 max-w-3xl mx-auto">
            Učte sa od úspešných YouTube tvorcov a premeňte svoj kanál na prosperujúci biznis. 
            Pridajte sa k tisíckam študentov, ktorí už rozširujú svoje publikum.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link to="/courses" class="btn-primary text-lg px-8 py-4">
              Preskúmajte kurzy
            </router-link>
            <router-link to="/register" class="btn-outline border-white text-white hover:bg-white hover:text-primary-900 text-lg px-8 py-4">
              Zaregistrovať sa
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Courses -->
    <section class="py-16 bg-secondary-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
            Odporúčané kurzy
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Starostlivo vybrané kurzy od našich najlepších inštruktorov na urýchlenie vašej YouTube cesty
          </p>
        </div>

        <div v-if="courseStore.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="i in 6" :key="i" class="card animate-pulse">
            <div class="h-48 bg-gray-200"></div>
            <div class="p-6 space-y-4">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2"></div>
              <div class="h-8 bg-gray-200 rounded w-1/4"></div>
            </div>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <CourseCard
            v-for="course in courseStore.featuredCourses"
            :key="course.id"
            :course="course"
            class="animate-fade-in"
          />
        </div>

        <div class="text-center mt-12">
          <router-link to="/courses" class="btn-primary text-lg px-8 py-3">
            Zobraziť všetky kurzy
          </router-link>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
          <div class="animate-fade-in">
            <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2">10K+</div>
            <div class="text-gray-600 font-medium">Zapísaných študentov</div>
          </div>
          <div class="animate-fade-in">
            <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2">50+</div>
            <div class="text-gray-600 font-medium">Expertných kurzov</div>
          </div>
          <div class="animate-fade-in">
            <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2">95%</div>
            <div class="text-gray-600 font-medium">Úspešnosť</div>
          </div>
          <div class="animate-fade-in">
            <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2">24/7</div>
            <div class="text-gray-600 font-medium">Dostupná podpora</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-secondary-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
            Prečo si vybrať VidaduAcademy?
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Všetko, co potrebujete na vybudovanie úspešného YouTube kanála na jednom mieste
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <AcademicCapIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Experti inštruktori</h3>
            <p class="text-gray-600">
              Učte sa od YouTube tvorcov s miliónmi odberateľov a overenou históriou úspechov.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <PlayIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Praktický obsah</h3>
            <p class="text-gray-600">
              Návody krok za krokom a príklady z reálneho sveta, ktoré môžete okamžite implementovať.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <ChartBarIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Sledovanie pokroku</h3>
            <p class="text-gray-600">
              Monitorujte svoju vzdelávaciu cestu s podrobným sledovaním pokroku a certifikátmi o dokončení.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <UsersIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Komunitná podpora</h3>
            <p class="text-gray-600">
              Spojte sa s ostatnými tvorcami a získajte podporu od našej aktívnej komunity študentov.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <DevicePhoneMobileIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Učte sa kdekoľvek</h3>
            <p class="text-gray-600">
              Pristupujte k svojim kurzom na akomkoľvek zariadení, kedykoľvek. Stiahnite si lekcie na offline sledovanie.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <StarIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Garantovaná kvalita</h3>
            <p class="text-gray-600">
              Všetky kurzy sú starostlivo vyberané a pravidelne aktualizované pre zaručenie najvyššej kvality.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
            Príbehy úspechov
          </h2>
          <p class="text-xl text-gray-600">
            Počujte od študentov, ktorí transformovali svoje YouTube kanály
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="card p-8 text-center animate-fade-in">
            <div class="flex justify-center mb-4">
              <StarIcon v-for="i in 5" :key="i" class="h-5 w-5 text-yellow-400 fill-current" />
            </div>
            <p class="text-gray-600 mb-6">
              "VidaduAcademy mi pomohla narásť z 1 tisíc na 100 tisíc odberateľov za pouhých 6 mesiacov. Stratégie skutočne fungujú!"
            </p>
            <div class="font-semibold text-gray-900">Sarah M.</div>
            <div class="text-sm text-gray-500">Gaming Creator</div>
          </div>

          <div class="card p-8 text-center animate-fade-in">
            <div class="flex justify-center mb-4">
              <StarIcon v-for="i in 5" :key="i" class="h-5 w-5 text-yellow-400 fill-current" />
            </div>
            <p class="text-gray-600 mb-6">
              "Kurz o monetizácii zmenil všetko. Teraz zarábam viac ako 5 tisíc dolárov mesačne zo svojho kanála."
            </p>
            <div class="font-semibold text-gray-900">Michael R.</div>
            <div class="text-sm text-gray-500">Tech Reviewer</div>
          </div>

          <div class="card p-8 text-center animate-fade-in">
            <div class="flex justify-center mb-4">
              <StarIcon v-for="i in 5" :key="i" class="h-5 w-5 text-yellow-400 fill-current" />
            </div>
            <p class="text-gray-600 mb-6">
              "Jasné, realizovateľné rady od skutočných expertov. Najlepšia investícia, ktorú som urobila pre svoju YouTube cestu."
            </p>
            <div class="font-semibold text-gray-900">Emma L.</div>
            <div class="text-sm text-gray-500">Lifestyle Vlogger</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-primary-600 to-secondary-600 text-white">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-bold mb-6">
          Pripravení transformovať svoj YouTube kanál?
        </h2>
        <p class="text-xl mb-8 opacity-90">
          Pridajte sa k tisíckam úspešných tvorcov, ktorí začali svoju cestu s VidaduAcademy
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link to="/register" class="btn-outline border-white text-white hover:bg-white hover:text-primary-600 text-lg px-8 py-4">
            Zaregistrovať sa
          </router-link>
          <router-link to="/courses" class="bg-white text-primary-600 hover:bg-gray-100 font-medium py-4 px-8 rounded-lg transition-colors text-lg">
            Prehliadnuť kurzy
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import {
  AcademicCapIcon,
  PlayIcon,
  ChartBarIcon,
  UsersIcon,
  DevicePhoneMobileIcon,
  StarIcon,
} from '@heroicons/vue/24/outline'
import { useCourseStore } from '@/stores/course'
import CourseCard from '@/components/courses/CourseCard.vue'

const courseStore = useCourseStore()

onMounted(() => {
  courseStore.fetchFeaturedCourses()
})
</script>
