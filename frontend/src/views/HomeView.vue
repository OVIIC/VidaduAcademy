<template>
  <div class="min-h-screen bg-black text-white font-sans selection:bg-primary-500 selection:text-white">
    <!-- Unified Background Section -->
    <div class="relative w-full bg-dark-950 overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-[500px] -left-1/4 w-[1000px] h-[1000px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow"></div>
            <!-- Middle Blob -->
            <div class="absolute top-[40%] -right-1/4 w-[800px] h-[800px] bg-secondary-600/10 rounded-full blur-[100px] opacity-30 animate-pulse-slow delay-500"></div>
            <!-- Bottom Blob -->
            <div class="absolute -bottom-[200px] -left-1/4 w-[800px] h-[800px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow delay-1000"></div>
            <div class="absolute top-0 left-0 w-full h-full bg-[url('/images/grid-pattern.svg')] bg-repeat opacity-[0.03]"></div>
        </div>

        <!-- Hero Section -->
        <section class="relative w-full min-h-[90vh] flex items-center z-10 pt-20">
        <!-- Content -->
        <div class="relative container-responsive w-full px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">

            
            <div class="melting-text-container mb-8">
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold leading-tight tracking-tight drop-shadow-2xl mb-4">
                <span class="block text-white mb-2">Dominuj na YouTube</span>
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-primary-500 to-secondary-500 animate-gradient-x">
                    so svojim kanálom
                </span>
                </h1>
            </div>
            
            <p class="text-xl sm:text-2xl text-dark-200 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up delay-200 font-normal">
                Tvor obsah ako skutočný kreatívec.
                <br class="hidden sm:block" />
                VidaduAcademy ťa naučí presné postupy, ktoré používa top <span class="text-primary-400 font-black">1 %</span> online tvorcov.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-5 justify-center items-center animate-fade-in-up delay-300">
                <router-link to="/courses" class="group relative px-8 py-4 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg hover:shadow-primary-500/25 transform hover:-translate-y-1 overflow-hidden">
                <span class="relative z-10 flex items-center gap-2">
                    🚀 Začať študovať
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

        <!-- Featured Courses Section -->
        <section class="py-24 relative overflow-hidden z-10">
        <!-- Background Glow Removed for Unity -->

        <div class="relative z-10 mb-12 px-4 max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-10">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                Odporúčané kurzy
            </h2>
            <p class="text-lg text-dark-300">
                Naštartuj svoj rast s našimi najpopulárnejšími kurzami. Praktické vedomosti, ktoré môžeš použiť hneď.
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

        <!-- Why Choose Us -->
        <section class="py-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                Prečo práve VidaduAcademy?
            </h2>
            <p class="text-xl text-dark-300">
                Nidky nebolo jednoduchšie stať sa profesionálnym tvorcom. Ponúkame ti kompletný ekosystém pre tvoj úspech.
            </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <!-- Feature 1 -->
            <div class="group p-8 rounded-3xl bg-dark-900 border border-dark-800 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/50">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500/20 to-purple-500/20 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                <UserGroupIcon class="w-8 h-8 text-primary-400" />
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Komunita Tvorcov</h3>
                <p class="text-dark-300 leading-relaxed">
                Nie si na to sám. Staň sa súčasťou aktívnej komunity, kde si navzájom pomáhame, zdieľame tipy a rastieme spolu.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="group p-8 rounded-3xl bg-dark-900 border border-dark-800 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/50">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-cyan-500/20 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                <ChartBarIcon class="w-8 h-8 text-blue-400" />
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Stratégie na Mieru</h3>
                <p class="text-dark-300 leading-relaxed">
                Žiadna teória, len prax. Učíme ťa presné kroky a stratégie, ktoré fungujú v roku 2025, podložené reálnymi dátami.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="group p-8 rounded-3xl bg-dark-900 border border-dark-800 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/50">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                <ShieldCheckIcon class="w-8 h-8 text-emerald-400" />
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Overení Experti</h3>
                <p class="text-dark-300 leading-relaxed">
                Uč sa od ľudí, ktorí to reálne dokázali. Naši inštruktori majú za sebou milióny zhliadnutí a úspešné kanály.
                </p>
            </div>
            </div>
        </div>
        </section>
    </div>

    <!-- How It Works - Sticky Scroll -->
    <section id="how-it-works" ref="howItWorksSection" class="relative bg-dark-950 py-24 pb-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-24">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">Ako to funguje?</h2>
                <p class="text-xl text-dark-300">Tvoja cesta k úspechu v 3 jednoduchých krokoch</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 lg:gap-20">
                <!-- Left Column: Scrollable Text Steps -->
                <div class="w-full lg:w-1/2 flex flex-col gap-[50vh] pb-[20vh] relative z-20">
                    
                    <!-- Step 1 Text -->
                    <div class="step-text min-h-[50vh] flex flex-col justify-center" data-step="0">
                        <div class="inline-block px-4 py-2 bg-primary-500/10 border border-primary-500/20 text-primary-400 rounded-full text-sm font-bold mb-6 w-fit backdrop-blur-sm">
                            KROK 01
                        </div>
                        <h3 class="text-4xl font-bold text-white mb-6 leading-tight">Zaregistruj sa <span class="text-primary-400">zadarmo</span></h3>
                        <p class="text-xl text-dark-300 leading-relaxed max-w-lg">
                            Vytvor si účet behom pár sekúnd. Nie je potrebná žiadna kreditná karta. Získaj okamžitý prístup k úvodným lekciám a pripoj sa do našej komunity.
                        </p>
                    </div>

                    <!-- Step 2 Text -->
                    <div class="step-text min-h-[50vh] flex flex-col justify-center" data-step="1">
                        <div class="inline-block px-4 py-2 bg-secondary-500/10 border border-secondary-500/20 text-secondary-400 rounded-full text-sm font-bold mb-6 w-fit backdrop-blur-sm">
                            KROK 02
                        </div>
                        <h3 class="text-4xl font-bold text-white mb-6 leading-tight">Vyber si svoju <span class="text-secondary-400">cestu</span></h3>
                        <p class="text-xl text-dark-300 leading-relaxed max-w-lg">
                            Prejdi si katalóg kurzov. Či už začínaš s YouTube, chceš sa naučiť strihať, alebo pochopiť monetizáciu. Sleduj lekcie vlastným tempom, kedykoľvek.
                        </p>
                    </div>

                    <!-- Step 3 Text -->
                    <div class="step-text min-h-[50vh] flex flex-col justify-center" data-step="2">
                        <div class="inline-block px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-full text-sm font-bold mb-6 w-fit backdrop-blur-sm">
                            KROK 03
                        </div>
                        <h3 class="text-4xl font-bold text-white mb-6 leading-tight">Aplikuj a <span class="text-emerald-400">zarábaj</span></h3>
                        <p class="text-xl text-dark-300 leading-relaxed max-w-lg">
                            Použi získané vedomosti v praxi. Sleduj ako ti rastú zhliadnutia, odberatelia a príjmy. Získaj certifikát a staň sa profesionálnym tvorcom.
                        </p>
                    </div>

                </div>

                <!-- Right Column: Sticky Visuals -->
                <div class="hidden lg:block w-1/2 relative">
                    <div class="sticky top-[20%] h-[600px] w-full rounded-3xl overflow-hidden border border-dark-800 bg-dark-900 shadow-2xl">
                         <!-- Background Elements -->
                         <div class="absolute inset-0 bg-[url('/images/grid-pattern.svg')] bg-repeat opacity-[0.05]"></div>
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
                <h2 class="text-4xl sm:text-5xl font-bold text-white mb-8 tracking-tight">
                Pripravený začať svoju cestu?
                </h2>
                <p class="text-xl text-dark-300 mb-12 max-w-2xl mx-auto">
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
                Žiadna kreditná karta nie je potrebná pre registráciu.
                </p>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-24 bg-dark-950">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-white text-center mb-16">
          Často kladené otázky
        </h2>
        
        <div class="space-y-6">
          <div v-for="(faq, index) in faqs" :key="index" class="bg-dark-900 rounded-2xl overflow-hidden border border-dark-800">
            <button @click="toggleFaq(index)" class="w-full px-8 py-6 flex items-center justify-between text-left hover:bg-dark-800/50 transition-colors">
              <span class="text-lg font-medium text-white">{{ faq.question }}</span>
              <ChevronDownIcon class="w-6 h-6 text-dark-400 transition-transform duration-300" :class="{ 'rotate-180': faq.isOpen }" />
            </button>
            <div v-show="faq.isOpen" class="px-8 pb-8 text-dark-300">
              <p class="pt-4 border-t border-dark-800">{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>


  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import {
  ArrowRightIcon,
  PlayCircleIcon,
  UserGroupIcon,
  ChartBarIcon,
  ShieldCheckIcon,
  ChevronDownIcon,
  UserPlusIcon,
  AcademicCapIcon
} from '@heroicons/vue/24/outline'
import { useCourseStore } from '@/stores/course'
import CourseCard from '@/components/courses/CourseCard.vue'

const courseStore = useCourseStore()

const activeCategory = ref('all')
const activeStep = ref(0)
const howItWorksSection = ref(null)
const stepColors = ['bg-primary-600/10', 'bg-secondary-600/10', 'bg-emerald-600/10']

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

const faqs = ref([
  {
    question: 'Je VidaduAcademy vhodná pre úplných začiatočníkov?',
    answer: 'Absolútne! Máme špeciálne kurzy navrhnuté pre ľudí, ktorí ešte nikdy nenahrali video. Prevedieme ťa od založenia kanála až po prvé úspešné video.',
    isOpen: false
  },
  {
    question: 'Koľko času musím venovať štúdiu?',
    answer: 'Všetky naše kurzy sú "self-paced", čo znamená, že študuješ vlastným tempom. Máme študentov, ktorí venujú štúdiu 15 minút denne, aj takých, ktorí to berú ako plný úväzok.',
    isOpen: false
  },
  {
    question: 'Získam po dokončení kurzu certifikát?',
    answer: 'Áno, po úspešnom dokončení každého kurzu získaš oficiálny certifikát od VidaduAcademy, ktorý si môžeš pridať na LinkedIn alebo do životopisu.',
    isOpen: false
  },
  {
    question: 'Aká je politika vrátenia peňazí?',
    answer: 'Veríme v kvalitu našich kurzov. Ak nebudeš spokojný, ponúkame 14-dňovú garanciu vrátenia peňazí bez zbytočných otázok.',
    isOpen: false
  }
])

const toggleFaq = (index) => {
  faqs.value[index].isOpen = !faqs.value[index].isOpen
}



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
  courseStore.fetchFeaturedCourses()
  setTimeout(initObserver, 100)
})

onUnmounted(() => {
  if (observer) observer.disconnect()
})
</script>

<style scoped>
.text-stroke {
  -webkit-text-stroke: 1px rgba(255, 255, 255, 0.1);
}

/* Animations */
@keyframes bounce {
  0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
  50% { transform: translateY(0); animation-timing-function: cubic-bezier(0, 0, 0.2, 1); }
}

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

.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-500 { animation-delay: 0.5s; }
.delay-1000 { animation-delay: 1s; }

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

/* Marquee Animation */
.marquee-container {
  animation: marquee 40s linear infinite;
}



@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
</style>


