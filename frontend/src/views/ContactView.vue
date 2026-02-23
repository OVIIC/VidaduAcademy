<template>
  <div class="min-h-screen bg-black text-white font-sans selection:bg-primary-500 selection:text-white">
    <!-- Unified Background Section -->
    <div class="relative w-full bg-dark-950 overflow-hidden min-h-screen flex flex-col pt-24 pb-16">
      <!-- Background Effects -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
          <!-- Top Blob -->
          <div class="absolute -top-[500px] -left-1/4 w-[1000px] h-[1000px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow"></div>
          <!-- Middle Blob -->
          <div class="absolute top-[40%] -right-1/4 w-[800px] h-[800px] bg-secondary-600/10 rounded-full blur-[100px] opacity-30 animate-pulse-slow delay-500"></div>
          <!-- Bottom Blob -->
          <div class="absolute -bottom-[200px] -left-1/4 w-[800px] h-[800px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow delay-1000"></div>
          <div class="absolute top-0 left-0 w-full h-full bg-[url('/images/grid-pattern.svg')] bg-repeat opacity-[0.03] blur-[3px]"></div>
      </div>

      <!-- Hero Title -->
      <div class="relative container-responsive w-full px-4 sm:px-6 lg:px-8 z-10 pt-16 pb-12 text-center pointer-events-none">
        <div class="melting-text-container mb-8 pointer-events-auto">
          <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-tight tracking-tight drop-shadow-2xl mb-4 flex flex-wrap justify-center gap-3">
            <span class="text-white">Kontaktujte</span>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-primary-500 to-secondary-500 animate-gradient-x">
              nás
            </span>
          </h1>
        </div>
        <p class="text-xl sm:text-2xl text-dark-200 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up delay-200 font-extralight pointer-events-auto">
          Máte otázky ku kurzom, platforme alebo potrebujete poradiť s výberom? Sme tu pre vás.
        </p>
      </div>

      <!-- Content Layout -->
      <div class="relative container-responsive w-full px-4 sm:px-6 lg:px-8 z-10 animate-fade-in-up delay-300 flex-1">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
          
          <!-- Contact Information -->
          <div class="flex flex-col justify-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-8 text-white">Spojme sa</h2>
            
            <div class="space-y-8">
              <!-- Email -->
              <div class="flex items-start group">
                <div class="flex-shrink-0 mt-1">
                  <div class="w-14 h-14 bg-dark-800 rounded-2xl flex items-center justify-center border border-dark-700 text-primary-400 group-hover:border-primary-500/50 group-hover:bg-primary-500/10 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-6">
                  <h3 class="text-xl font-bold text-white">Email</h3>
                  <p class="mt-2 text-dark-300 leading-relaxed font-extralight">Radi vám odpovieme na vaše otázky e-mailom.</p>
                  <a href="mailto:vidadutv@vidadu.com" class="mt-3 inline-block text-primary-400 hover:text-primary-300 transition-colors text-lg font-bold">
                    vidadutv@vidadu.com
                  </a>
                </div>
              </div>

              <!-- Online Support -->
              <div class="flex items-start group">
                 <div class="flex-shrink-0 mt-1">
                  <div class="w-14 h-14 bg-dark-800 rounded-2xl flex items-center justify-center border border-dark-700 text-primary-400 group-hover:border-primary-500/50 group-hover:bg-primary-500/10 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-6">
                   <h3 class="text-xl font-bold text-white">Online Podpora</h3>
                   <p class="mt-2 text-dark-300 leading-relaxed font-extralight">Pre študentov poskytujeme prioritnú podporu cez náš Discord server a komunitnú platformu.</p>
                </div>
              </div>
              
            </div>
          </div>

          <!-- Contact Form -->
          <div class="relative">
            <div class="relative bg-dark-900 border border-dark-800 p-8 sm:p-10 rounded-[2rem] shadow-xl">
              <h3 class="text-2xl font-bold mb-8 text-white">Napíšte nám správu</h3>
              <form @submit.prevent="submitForm" class="space-y-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-dark-200 mb-2">Vaše meno</label>
                  <input 
                    type="text" 
                    id="name" 
                    v-model="form.name" 
                    required
                    class="w-full bg-dark-800 border border-dark-700 rounded-xl px-5 py-3.5 text-white placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    placeholder="Jozef Mrkvička"
                  >
                </div>
                
                <div>
                  <label for="email" class="block text-sm font-medium text-dark-200 mb-2">E-mailová adresa</label>
                  <input 
                    type="email" 
                    id="email" 
                    v-model="form.email" 
                    required
                    class="w-full bg-dark-800 border border-dark-700 rounded-xl px-5 py-3.5 text-white placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    placeholder="jozef@priklad.sk"
                  >
                </div>
                
                <div>
                  <label for="subject" class="block text-sm font-medium text-dark-200 mb-2">Predmet</label>
                  <input 
                    type="text" 
                    id="subject" 
                    v-model="form.subject" 
                    required
                    class="w-full bg-dark-800 border border-dark-700 rounded-xl px-5 py-3.5 text-white placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    placeholder="Ako vám môžeme pomôcť?"
                  >
                </div>

                <div>
                  <label for="message" class="block text-sm font-medium text-dark-200 mb-2">Správa</label>
                  <textarea 
                    id="message" 
                    v-model="form.message" 
                    rows="4" 
                    required
                    class="w-full bg-dark-800 border border-dark-700 rounded-xl px-5 py-3.5 text-white placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-none"
                    placeholder="Tu napíšte vašu správu..."
                  ></textarea>
                </div>

                <button 
                  type="submit" 
                  class="group/btn relative w-full mt-4 px-8 py-4 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-primary-500/25 transform hover:-translate-y-1 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center"
                  :disabled="isSubmitting"
                >
                  <span class="relative z-10 flex items-center gap-2">
                    <span v-if="isSubmitting" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    {{ isSubmitting ? 'Odosielanie...' : 'Odoslať správu' }}
                  </span>
                  <div class="absolute inset-0 bg-gradient-to-r from-primary-500 via-white/20 to-primary-500 translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-700 ease-in-out"></div>
                </button>
                
                <!-- Success/Error messages -->
                <transition name="fade">
                  <div v-if="submitStatus === 'success'" class="p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400 text-sm mt-4 font-medium text-center">
                    Vaša správa bola úspešne odoslaná. Čoskoro sa vám ozveme!
                  </div>
                </transition>
                <transition name="fade">
                  <div v-if="submitStatus === 'error'" class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm mt-4 font-medium text-center">
                    Pri odosielaní správy nastala chyba. Skúste to prosím neskôr, alebo nás kontaktujte priamo e-mailom.
                  </div>
                </transition>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const isSubmitting = ref(false)
const submitStatus = ref(null) // 'success' or 'error'

const submitForm = async () => {
  isSubmitting.value = true
  submitStatus.value = null
  
  try {
    // In a real implementation you would send this to your backend API
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    submitStatus.value = 'success'
    
    // Reset form
    form.value = {
      name: '',
      email: '',
      subject: '',
      message: ''
    }
  } catch (error) {
    console.error('Error sending message:', error)
    submitStatus.value = 'error'
  } finally {
    isSubmitting.value = false
    
    // Hide notification message after 5 seconds
    if (submitStatus.value === 'success') {
      setTimeout(() => {
        submitStatus.value = null
      }, 5000)
    }
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Global Animations reused here */
@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.2; transform: scale(1.1); }
}

.animate-pulse-slow {
  animation: pulse-slow 8s infinite ease-in-out;
}

.animate-fade-in-up {
  animation: fadeInUp 0.8s ease-out forwards;
  opacity: 0;
  transform: translateY(20px);
}

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

.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-500 { animation-delay: 0.5s; }
.delay-1000 { animation-delay: 1s; }
</style>
