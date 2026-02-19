<template>
  <section class="relative w-full min-h-[90vh] flex items-center z-10 pt-20">

    
    <!-- Content -->
    <div class="relative container-responsive w-full px-4 sm:px-6 lg:px-8 z-10 pointer-events-none">
        <div class="max-w-4xl mx-auto text-center pointer-events-auto">
        <div class="melting-text-container mb-8">
          <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-tight tracking-tight drop-shadow-2xl mb-4">
            <span class="block text-white mb-2">Dominuj na YouTube</span>
            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-primary-500 to-secondary-500 animate-gradient-x">
              so svojím kanálom
            </span>
          </h1>
        </div>
        
        <p class="text-xl sm:text-2xl text-dark-200 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up delay-200 font-extralight">
          Tvor obsah ako skutočný kreatívec.
          <br class="hidden sm:block" />
          VidaduAcademy ťa naučí presné postupy, ktoré používa top <span class="text-primary-400 font-black">1 %</span> online tvorcov.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-5 justify-center items-center animate-fade-in-up delay-300">
          <router-link to="/courses" class="group relative px-8 py-4 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg hover:shadow-primary-500/25 transform hover:-translate-y-1 overflow-hidden">
            <span class="relative z-10 flex items-center gap-2">
              Začni študovať
              <ArrowRightIcon class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
            </span>
            <div class="absolute inset-0 bg-gradient-to-r from-primary-500 via-white/20 to-primary-500 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 ease-in-out"></div>
          </router-link>
          
          <a href="#how-it-works" class="px-8 py-4 bg-dark-800/50 hover:bg-dark-800 text-white font-medium rounded-2xl border border-dark-700 hover:border-dark-600 transition-all duration-300 backdrop-blur-sm flex items-center gap-2">
            <PlayCircleIcon class="w-6 h-6 text-dark-300" />
            Ako to funguje
          </a>
        </div>

        <!-- Stats Quick View -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 pt-8 border-t border-dark-800/50 animate-fade-in-up delay-500">
          <div class="p-4">
            <div class="text-3xl font-bold text-white mb-1">1000+</div>
            <div class="text-sm text-dark-400 font-medium uppercase tracking-wider">Študentov</div>
          </div>
          <div class="p-4">
            <div class="text-3xl font-bold text-white mb-1">50+</div>
            <div class="text-sm text-dark-400 font-medium uppercase tracking-wider">Hodín obsahu</div>
          </div>
          <div class="p-4">
            <div class="text-3xl font-bold text-white mb-1">15+</div>
            <div class="text-sm text-dark-400 font-medium uppercase tracking-wider">Expertov</div>
          </div>
          <div class="p-4">
            <div class="text-3xl font-bold text-white mb-1">4.9/5</div>
            <div class="text-sm text-dark-400 font-medium uppercase tracking-wider">Hodnotenie</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ArrowRightIcon, PlayCircleIcon } from '@heroicons/vue/24/outline'
</script>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.8s ease-out forwards;
  opacity: 0;
  transform: translateY(20px);
}

.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-500 { animation-delay: 0.5s; }

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-gradient-x {
  background-size: 200% 200%;
  animation: gradientX 4s ease infinite;
}

@keyframes gradientX {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>
 
