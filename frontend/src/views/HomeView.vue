<template>
  <div class="min-h-screen bg-black text-white">
    <!-- Hero Section -->
    <section class="relative w-full py-12 sm:py-16 lg:py-20 text-white overflow-hidden min-h-[80vh] flex items-center bg-black">
      
      <!-- Matrix Rain Animation - Full width hero background -->
      <canvas ref="matrixCanvas" 
              class="absolute top-0 left-0 w-full h-full z-0" 
              style="pointer-events: none;">
      </canvas>
      
      <!-- Dark overlay for better text readability (over Matrix) -->
      <div class="absolute inset-0 bg-black opacity-40 z-10"></div>
      
      <!-- Content - highest z-index -->
      <div class="relative container-responsive z-20 w-full">
        <div class="text-center">
          <div class="melting-text-container mb-16 pb-12">
            <h1 class="melting-text text-4xl sm:text-5xl lg:text-6xl font-bold leading-relaxed drop-shadow-lg mb-2">
              Dominuj na YouTube
            </h1>
            <h1 class="melting-text-secondary text-4xl sm:text-5xl lg:text-6xl font-bold leading-relaxed drop-shadow-lg">
              <span class="text-primary-500">so svojim kanálom</span>
            </h1>
          </div>
          <p class="text-xl lg:text-2xl text-gray-200 mb-8 max-w-3xl mx-auto drop-shadow-md">
            Nauč sa od úspešných YouTube tvorcov ako premeniť svoj kanál na 
            profitujúci biznis.
          </p>
          
          <!-- Stats Section in Hero -->
          <div class="grid grid-cols-3 gap-4 max-w-2xl mx-auto mb-8 text-center">
            <div class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-4">
              <div class="text-2xl font-bold text-white drop-shadow-md">1000+</div>
              <div class="text-sm text-gray-300">spokojných študentov</div>
            </div>
            <div class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-4">
              <div class="text-2xl font-bold text-white drop-shadow-md">50+</div>
              <div class="text-sm text-gray-300">hodín obsahu</div>
            </div>
            <div class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-4">
              <div class="text-2xl font-bold text-white drop-shadow-md">4.8⭐</div>
              <div class="text-sm text-gray-300">priemerné hodnotenie</div>
            </div>
          </div>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link to="/courses" class="bg-primary-500 hover:bg-primary-600 text-white font-medium py-4 px-8 rounded-2xl transition-colors text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
              🚀 Preskúmajte kurzy
            </router-link>
            <router-link to="/register" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-black font-medium py-4 px-8 rounded-2xl transition-all text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
              ✨ Zaregistrovať sa
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Courses -->
    <section class="py-16 bg-black">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
            Odporúčané kurzy
          </h2>
          <p class="text-xl text-gray-300 max-w-2xl mx-auto">
            Starostlivo vybrané kurzy od našich najlepších inštruktorov na urýchlenie vašej YouTube cesty
          </p>
        </div>

        <div v-if="courseStore.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="i in 6" :key="i" class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl animate-pulse">
            <div class="h-48 bg-gray-800 rounded-t-2xl"></div>
            <div class="p-6 space-y-4">
              <div class="h-4 bg-gray-700 rounded w-3/4"></div>
              <div class="h-3 bg-gray-700 rounded w-1/2"></div>
              <div class="h-8 bg-gray-700 rounded w-1/4"></div>
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
          <router-link to="/courses" class="bg-primary-500 hover:bg-primary-600 text-white font-medium py-3 px-8 rounded-2xl transition-colors text-lg shadow-lg hover:shadow-xl">
            Zobraziť všetky kurzy
          </router-link>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-black">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
            Prečo si vybrať VidaduAcademy?
          </h2>
          <p class="text-xl text-gray-300 max-w-2xl mx-auto">
            Všetko, co potrebujete na vybudovanie úspešného YouTube kanála na jednom mieste
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 backdrop-blur-xl border border-primary-500/30 rounded-full flex items-center justify-center mx-auto mb-6">
              <AcademicCapIcon class="h-8 w-8 text-primary-500" />
            </div>
            <h3 class="text-xl font-semibold mb-4 text-white">Experti inštruktori</h3>
            <p class="text-gray-300">
              Učte sa od YouTube tvorcov s miliónmi odberateľov a overenou históriou úspechov.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 backdrop-blur-xl border border-primary-500/30 rounded-full flex items-center justify-center mx-auto mb-6">
              <PlayIcon class="h-8 w-8 text-primary-500" />
            </div>
            <h3 class="text-xl font-semibold mb-4 text-white">Praktický obsah</h3>
            <p class="text-gray-300">
              Návody krok za krokom a príklady z reálneho sveta, ktoré môžete okamžite implementovať.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 backdrop-blur-xl border border-primary-500/30 rounded-full flex items-center justify-center mx-auto mb-6">
              <ChartBarIcon class="h-8 w-8 text-primary-500" />
            </div>
            <h3 class="text-xl font-semibold mb-4 text-white">Sledovanie pokroku</h3>
            <p class="text-gray-300">
              Monitorujte svoju vzdelávaciu cestu s podrobným sledovaním pokroku a certifikátmi o dokončení.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 backdrop-blur-xl border border-primary-500/30 rounded-full flex items-center justify-center mx-auto mb-6">
              <UsersIcon class="h-8 w-8 text-primary-500" />
            </div>
            <h3 class="text-xl font-semibold mb-4 text-white">Komunitná podpora</h3>
            <p class="text-gray-300">
              Spojte sa s ostatnými tvorcami a získajte podporu od našej aktívnej komunity študentov.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 backdrop-blur-xl border border-primary-500/30 rounded-full flex items-center justify-center mx-auto mb-6">
              <DevicePhoneMobileIcon class="h-8 w-8 text-primary-500" />
            </div>
            <h3 class="text-xl font-semibold mb-4 text-white">Učte sa kdekoľvek</h3>
            <p class="text-gray-300">
              Pristupujte k svojim kurzom na akomkoľvek zariadení, kedykoľvek. Stiahnite si lekcie na offline sledovanie.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 backdrop-blur-xl border border-primary-500/30 rounded-full flex items-center justify-center mx-auto mb-6">
              <StarIcon class="h-8 w-8 text-primary-500" />
            </div>
            <h3 class="text-xl font-semibold mb-4 text-white">Garantovaná kvalita</h3>
            <p class="text-gray-300">
              Všetky kurzy sú starostlivo vyberané a pravidelne aktualizované pre zaručenie najvyššej kvality.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-black">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
            Príbehy úspechov
          </h2>
          <p class="text-xl text-gray-300">
            Počujte od študentov, ktorí transformovali svoje YouTube kanály
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-8 text-center animate-fade-in">
            <div class="flex justify-center mb-4">
              <StarIcon v-for="i in 5" :key="i" class="h-5 w-5 text-yellow-400 fill-current" />
            </div>
            <p class="text-gray-300 mb-6">
              "VidaduAcademy mi pomohla narásť z 1 tisíc na 100 tisíc odberateľov za pouhých 6 mesiacov. Stratégie skutočne fungujú!"
            </p>
            <div class="font-semibold text-white">Sarah M.</div>
            <div class="text-sm text-gray-400">Gaming Creator</div>
          </div>

          <div class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-8 text-center animate-fade-in">
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
import { onMounted, ref, onUnmounted } from 'vue'
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
const matrixCanvas = ref(null)
let animationId = null

const initMatrix = () => {
  console.log('🎬 Starting Matrix Rain Animation...')
  const canvas = matrixCanvas.value
  if (!canvas) {
    console.error('❌ Canvas not found!')
    return
  }

  const ctx = canvas.getContext('2d')
  
  const resizeCanvas = () => {
    const heroSection = canvas.parentElement
    canvas.width = heroSection.offsetWidth
    canvas.height = heroSection.offsetHeight
    console.log(`📐 Canvas resized to full hero section: ${canvas.width}x${canvas.height}`)
  }
  
  resizeCanvas()
  window.addEventListener('resize', resizeCanvas)
  
  const fontSize = 14
  const columns = canvas.width / fontSize
  const drops = []
  
  // Initialize drops
  for (let x = 0; x < columns; x++) {
    drops[x] = Math.random() * canvas.height
  }
  
  console.log(`💧 Created ${drops.length} drops for ${Math.floor(columns)} columns`)
  
  const animate = () => {
    // Create slower fade effect
    ctx.fillStyle = 'rgba(59, 49, 87, 0.05)'
    ctx.fillRect(0, 0, canvas.width, canvas.height)
    
    // Set text properties
    ctx.font = `${fontSize}px 'Courier New', monospace`
    
    // Draw characters
    for (let i = 0; i < drops.length; i++) {
      const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789<>{}[]()+=/*-_|\\:;.,?!@#$%^&*'
      const text = chars[Math.floor(Math.random() * chars.length)]
      
      // Add opacity variation
      const alpha = Math.random() * 0.4 + 0.6
      const hexAlpha = Math.floor(alpha * 255).toString(16).padStart(2, '0')
      ctx.fillStyle = '#7A65B4' + hexAlpha
      
      // Draw character
      ctx.fillText(text, i * fontSize, drops[i] * fontSize)
      
      // Move drop down (slower speed)
      if (drops[i] * fontSize > canvas.height && Math.random() > 0.985) {
        drops[i] = 0
      }
      drops[i] += 0.2 + Math.random() * 0.3
    }
    
    animationId = requestAnimationFrame(animate)
  }
  
  console.log('🎮 Starting animation loop...')
  animate()
}

onMounted(() => {
  courseStore.fetchFeaturedCourses()
  
  // Start matrix animation
  setTimeout(() => {
    initMatrix()
  }, 100)
})

onUnmounted(() => {
  if (animationId) {
    cancelAnimationFrame(animationId)
  }
})
</script>

<style scoped>
/* Melting Text Effect */
.melting-text-container {
  position: relative;
  overflow: hidden;
}

.melting-text {
  position: relative;
  animation: melt 4s infinite ease-in-out;
  background: linear-gradient(90deg, #ffffff, #f3f4f6, #ffffff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.melting-text-secondary {
  position: relative;
  animation: melt 4s infinite ease-in-out 0.5s;
}

.melting-text-secondary span {
  background: linear-gradient(90deg, #ef4444, #dc2626, #ef4444);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.melting-text::before,
.melting-text::after {
  content: 'Dominuj na YouTube';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, #ffffff, #f3f4f6, #ffffff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  z-index: -1;
  transform: scaleY(1);
  opacity: 0.4;
  animation: drip 4s infinite ease-in-out;
}

.melting-text::after {
  filter: blur(8px);
  opacity: 0.2;
}

.melting-text-secondary::before,
.melting-text-secondary::after {
  content: 'so svojim kanálom';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, #ef4444, #dc2626, #ef4444);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  z-index: -1;
  transform: scaleY(1);
  opacity: 0.4;
  animation: drip 4s infinite ease-in-out 0.5s;
}

.melting-text-secondary::after {
  filter: blur(8px);
  opacity: 0.2;
}

/* Keyframes for melting effect */
@keyframes melt {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(15px);
  }
}

@keyframes drip {
  0%, 100% {
    transform: scaleY(1);
    opacity: 0.4;
  }
  50% {
    transform: scaleY(1.3);
    opacity: 0.6;
  }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  @keyframes melt {
    0%, 100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(10px);
    }
  }
}
</style>
