<template>
  <div class="min-h-screen bg-black text-white font-sans selection:bg-primary-500 selection:text-white">
    <!-- Unified Background Section -->
    <div class="relative w-full bg-dark-950 overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
            <!-- Top Blob -->
            <div class="absolute -top-[500px] -left-1/4 w-[1000px] h-[1000px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow"></div>
            <!-- Middle Blob -->
            <div class="absolute top-[40%] -right-1/4 w-[800px] h-[800px] bg-secondary-600/10 rounded-full blur-[100px] opacity-30 animate-pulse-slow delay-500"></div>
            <!-- Bottom Blob -->
            <div class="absolute -bottom-[200px] -left-1/4 w-[800px] h-[800px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow delay-1000"></div>
            <div class="absolute top-0 left-0 w-full h-full bg-[url('/images/grid-pattern.svg')] bg-repeat opacity-[0.03]"></div>
        </div>

        <HeroSection />

        <FeaturedCourses />

        <WhyChooseUs />
    </div>

    <HowItWorks />

    <FaqSection />

  </div>
</template>

<script setup>
import HeroSection from './home/HeroSection.vue'
import FeaturedCourses from './home/FeaturedCourses.vue'
import WhyChooseUs from './home/WhyChooseUs.vue'
import HowItWorks from './home/HowItWorks.vue'
import FaqSection from './home/FaqSection.vue'
</script>

<style scoped>
/* Global Animations used in background */
@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.2; transform: scale(1.1); }
}

.animate-pulse-slow {
  animation: pulse-slow 8s infinite ease-in-out;
}

.delay-500 { animation-delay: 0.5s; }
.delay-1000 { animation-delay: 1s; }
</style>
