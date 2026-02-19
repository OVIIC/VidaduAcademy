<template>
  <section id="how-it-works" ref="howItWorksSection" class="relative bg-dark-950 py-24 pb-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-32 relative z-10">
        <h2 class="text-3xl sm:text-4xl font-black text-white mb-6">Ako to funguje?</h2>
        <p class="text-xl text-dark-300 font-extralight">Tvoja cesta k úspechu v 3 jednoduchých krokoch</p>
      </div>

      <div class="flex flex-col lg:flex-row gap-8 lg:gap-20">
        <!-- Left Column: Scrollable Text Steps -->
        <div class="w-full lg:w-1/2 flex flex-col gap-[50vh] pb-[20vh] relative z-20">
          
          <!-- Step 1 Text -->
          <div class="step-text min-h-[50vh] flex flex-col justify-center" data-step="0">
            <div class="inline-block px-4 py-2 bg-primary-500/10 border border-primary-500/20 text-primary-400 rounded-full text-sm font-bold mb-6 w-fit backdrop-blur-sm">
              KROK 01
            </div>
            <h3 class="text-4xl font-black text-white mb-6 leading-tight">Registruješ sa <span class="text-primary-400">zadarmo</span></h3>
            <p class="text-xl text-dark-300 leading-relaxed max-w-lg font-extralight">
              Účet si vytvoríš behom pár sekúnd. Nie je potrebná žiadna platobná karta ani žiadna platba. Získaj okamžitý prístup k úvodným lekciám a potom sa rozhodneš, či chceš pokračovať ďalej.
            </p>
          </div>

          <!-- Step 2 Text -->
          <div class="step-text min-h-[50vh] flex flex-col justify-center" data-step="1">
            <div class="inline-block px-4 py-2 bg-secondary-500/10 border border-secondary-500/20 text-secondary-400 rounded-full text-sm font-bold mb-6 w-fit backdrop-blur-sm">
              KROK 02
            </div>
            <h3 class="text-4xl font-black text-white mb-6 leading-tight">Nájdi svoj kurz a <span class="text-secondary-400">študuj</span></h3>
            <p class="text-xl text-dark-300 leading-relaxed max-w-lg font-extralight">
              Náš katalóg ti ponúka všetko pre tvoj rast, od prvého videa až po biznis stratégie. Uč sa vlastným tempom, alebo si vyžiadaj osobnú konzultáciu s Vidadu expertom.
            </p>
          </div>

          <!-- Step 3 Text -->
          <div class="step-text min-h-[50vh] flex flex-col justify-center" data-step="2">
            <div class="inline-block px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-full text-sm font-bold mb-6 w-fit backdrop-blur-sm">
              KROK 03
            </div>
            <h3 class="text-4xl font-black text-white mb-6 leading-tight">Aplikuj vedomostia <span class="text-emerald-400">zarábaj</span></h3>
            <p class="text-xl text-dark-300 leading-relaxed max-w-lg font-extralight">
              Použi získané vedomosti v praxi. Sleduj ako ti rastú zhliadnutia, odberatelia a príjmy. Získaj certifikát a staň sa profesionálnym tvorcom.
            </p>
          </div>

        </div>

        <!-- Right Column: Sticky Visuals -->
        <div class="hidden lg:block w-1/2 relative">
          <div class="sticky h-[560px] max-h-[75vh] w-full rounded-3xl overflow-hidden border border-dark-800 bg-dark-900 shadow-2xl"
               style="top: max(140px, 50vh - 280px)"
          >
             <!-- Background Elements -->
             <div class="absolute inset-0 bg-[url('/images/grid-pattern.svg')] bg-repeat opacity-[0.05] blur-[3px]"></div>
             <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary-600/10 rounded-full blur-[100px] transition-colors duration-700"
                  :class="stepColors[activeStep]"
             ></div>

             <!-- Content Container -->
             <div class="relative h-full w-full flex items-center justify-center p-12">
                 
                 <!-- Step 1 Visual: Profile/Registration -->
                 <div 
                    class="absolute inset-0 flex items-center justify-center transition-all duration-700 transform"
                    :class="activeStep === 0 ? 'opacity-100 scale-100 translate-y-0' : 'opacity-0 scale-95 translate-y-12'"
                 >
                    <div class="relative w-80 bg-dark-800 rounded-2xl border border-dark-700 shadow-2xl p-6 rotate-[-6deg] hover:rotate-0 transition-transform duration-500">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-full bg-primary-500/20 flex items-center justify-center">
                                <UserPlusIcon class="w-6 h-6 text-primary-400" />
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="h-2 w-20 bg-dark-600 rounded-full"></div>
                                <div class="h-2 w-12 bg-dark-700 rounded-full"></div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-2 w-full bg-dark-700 rounded-full"></div>
                            <div class="h-2 w-full bg-dark-700 rounded-full"></div>
                            <div class="h-2 w-2/3 bg-dark-700 rounded-full"></div>
                        </div>
                        <div class="mt-6">
                            <button class="w-full py-2 bg-primary-600 rounded-lg text-xs font-bold text-white text-center">Registrovať sa</button>
                        </div>
                        
                        <!-- Floating Badge -->
                        <div class="absolute -right-6 -top-6 bg-dark-800 border border-dark-600 p-3 rounded-xl shadow-xl flex items-center gap-2 animate-bounce-slow">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-xs font-bold text-white">Online</span>
                        </div>
                    </div>
                 </div>

                 <!-- Step 2 Visual: Video Player -->
                 <div 
                    class="absolute inset-0 flex items-center justify-center transition-all duration-700 transform"
                    :class="activeStep === 1 ? 'opacity-100 scale-100 translate-y-0' : 'opacity-0 scale-95 translate-y-12'"
                 >
                    <div class="relative w-96 bg-dark-800 rounded-2xl border border-dark-700 shadow-2xl overflow-hidden rotate-[3deg] hover:rotate-0 transition-transform duration-500">
                        <!-- Player Header -->
                        <div class="h-8 bg-dark-900 border-b border-dark-700 flex items-center px-4 gap-2">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        </div>
                        <!-- Video Content -->
                        <div class="h-48 bg-dark-900/50 flex items-center justify-center relative group">
                            <div class="absolute inset-0 bg-secondary-900/20"></div>
                            <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20 group-hover:scale-110 transition-transform cursor-pointer">
                                <PlayCircleIcon class="w-8 h-8 text-white ml-1" />
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-dark-700">
                                <div class="h-full w-1/3 bg-secondary-500"></div>
                            </div>
                        </div>
                        <div class="p-4 bg-dark-800">
                            <div class="flex gap-3">
                                <div class="w-10 h-10 rounded-lg bg-secondary-500/20 flex items-center justify-center flex-shrink-0">
                                    <AcademicCapIcon class="w-5 h-5 text-secondary-400" />
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white mb-1">Mastering DaVinci Resolve</div>
                                    <div class="text-xs text-dark-400">Lekcia 4/12 • 15 minút</div>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>

                 <!-- Step 3 Visual: Graph/Earnings -->
                 <div 
                    class="absolute inset-0 flex items-center justify-center transition-all duration-700 transform"
                    :class="activeStep === 2 ? 'opacity-100 scale-100 translate-y-0' : 'opacity-0 scale-95 translate-y-12'"
                 >
                    <div class="relative w-80 bg-dark-800 rounded-2xl border border-dark-700 shadow-2xl p-6 rotate-[-3deg] hover:rotate-0 transition-transform duration-500">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <div class="text-xs text-dark-400 font-bold uppercase tracking-wider">Tvoj Príjem</div>
                                <div class="text-2xl font-bold text-white">€2,450.00</div>
                            </div>
                            <div class="bg-emerald-500/10 px-2 py-1 rounded text-xs font-bold text-emerald-400">
                                +12.5%
                            </div>
                        </div>
                        
                        <!-- Bars -->
                        <div class="flex items-end justify-between gap-2 h-32 mb-2">
                            <div class="w-full bg-dark-700 rounded-t h-[40%] hover:bg-emerald-500/50 transition-colors"></div>
                            <div class="w-full bg-dark-700 rounded-t h-[60%] hover:bg-emerald-500/50 transition-colors"></div>
                            <div class="w-full bg-dark-700 rounded-t h-[30%] hover:bg-emerald-500/50 transition-colors"></div>
                            <div class="w-full bg-dark-700 rounded-t h-[75%] hover:bg-emerald-500/50 transition-colors"></div>
                            <div class="w-full bg-emerald-500 rounded-t h-[90%] shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
                        </div>
                        <div class="flex justify-between text-xs text-dark-500">
                            <span>Po</span>
                            <span>Ut</span>
                            <span>St</span>
                            <span>Št</span>
                            <span>Pi</span>
                        </div>
                    </div>
                 </div>

             </div>
        </div>
      </div>
    </div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center mt-24">
      <h2 class="text-4xl sm:text-5xl font-black text-white mb-8 tracking-tight">
        Pripravený začať svoju cestu?
      </h2>
      <p class="text-xl text-dark-300 mb-12 max-w-2xl mx-auto font-extralight">
        Nečakaj kým ťa predbehne konkurencia. Pridaj sa k VidaduAcademy ešte dnes a začni budovať svoju budúcnosť na YouTube.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <router-link to="/register" class="px-10 py-5 bg-[rgb(237,111,85)] text-white font-bold text-lg rounded-2xl hover:bg-[rgb(220,100,75)] transition-all shadow-lg hover:shadow-[rgb(237,111,85)]/25 hover:-translate-y-1">
          Zaregistrovať sa zadarmo
        </router-link>
        <router-link to="/courses" class="px-10 py-5 bg-dark-800/50 hover:bg-dark-800 text-white font-bold text-lg rounded-2xl border border-dark-700 hover:border-dark-600 transition-all backdrop-blur-sm">
          Prezrieť všetky kurzy
        </router-link>
      </div>
      <p class="mt-8 text-sm text-dark-400">
        Žiadna platobná karta nie je potrebná pre registráciu.
      </p>
    </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
  UserPlusIcon,
  PlayCircleIcon,
  AcademicCapIcon
} from '@heroicons/vue/24/outline'

const activeStep = ref(0)
const howItWorksSection = ref(null)
const stepColors = ['bg-primary-600/10', 'bg-secondary-600/10', 'bg-emerald-600/10']

// Intersection Observer for Sticky Sections
let observer = null

const initObserver = () => {
    const options = {
        threshold: 0.5, // Trigger when 50% of the item is visible
        rootMargin: "-20% 0px -20% 0px"
    }

    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                activeStep.value = parseInt(entry.target.getAttribute('data-step'))
            }
        })
    }, options)

    const steps = document.querySelectorAll('.step-text')
    steps.forEach(step => observer.observe(step))
}

onMounted(() => {
  setTimeout(initObserver, 100)
})

onUnmounted(() => {
  if (observer) observer.disconnect()
})
</script>

<style scoped>
@keyframes bounce {
  0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
  50% { transform: translateY(0); animation-timing-function: cubic-bezier(0, 0, 0.2, 1); }
}

.animate-bounce-slow {
    animation: bounce 3s infinite;
}
</style>
