<template>
  <div class="text-white font-sans selection:bg-primary-500 selection:text-white">


    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-24">
      <!-- Header (Hidden in Detail View for immersive experience or handled inside Detail) -->
      <div v-show="!selectedCatalogCourse || currentTab === 'overview'" class="mb-8 transition-all duration-300">
        <h1 class="text-3xl sm:text-4xl font-black text-white mb-2">Vitajte späť, {{ user?.name }}!</h1>
        <p class="text-xl text-dark-300 font-extralight">Pokračujte vo svojej vzdelávacej ceste</p>
      </div>

      <!-- Tab Switcher (Hide when viewing details?) -> Decision: Hide to focus on course, keep back button to return -->
      <!-- Tab Switcher removed as per user request (handled by Sidebar) -->

      <!-- Overview Tab -->
      <div v-show="currentTab === 'overview'" class="transition-opacity duration-300 animate-fade-in">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="group bg-dark-900 border border-dark-800 hover:border-primary-500/30 rounded-3xl p-6 transition-all duration-300 hover:bg-dark-800/50">
            <div class="flex items-center">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-500/10 to-purple-500/10 flex items-center justify-center mr-6 group-hover:scale-110 transition-transform duration-300">
                    <BookOpenIcon class="w-10 h-10 text-primary-400" />
                </div>
                <div>
                <h2 class="text-sm text-dark-400 font-bold uppercase tracking-wider mb-1">Zapísané kurzy</h2>
                <p class="text-3xl font-black text-white">{{ enrolledCourses.length }}</p>
                </div>
            </div>
            </div>
            
            <div class="group bg-dark-900 border border-dark-800 hover:border-secondary-500/30 rounded-3xl p-6 transition-all duration-300 hover:bg-dark-800/50">
            <div class="flex items-center">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-secondary-500/10 to-orange-500/10 flex items-center justify-center mr-6 group-hover:scale-110 transition-transform duration-300">
                    <CheckBadgeIcon class="w-10 h-10 text-secondary-400" />
                </div>
                <div>
                <h2 class="text-sm text-dark-400 font-bold uppercase tracking-wider mb-1">Dokončené</h2>
                <p class="text-3xl font-black text-white">{{ completedCourses }}</p>
                </div>
            </div>
            </div>

            <div class="group bg-dark-900 border border-dark-800 hover:border-emerald-500/30 rounded-3xl p-6 transition-all duration-300 hover:bg-dark-800/50">
            <div class="flex items-center">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-500/10 to-green-500/10 flex items-center justify-center mr-6 group-hover:scale-110 transition-transform duration-300">
                    <AcademicCapIcon class="w-10 h-10 text-emerald-400" />
                </div>
                <div>
                <h2 class="text-sm text-dark-400 font-bold uppercase tracking-wider mb-1">Certifikáty</h2>
                <p class="text-3xl font-black text-white">{{ certificates }}</p>
                </div>
            </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
            <!-- Continue Learning -->
            <div class="bg-dark-900 border border-dark-800 rounded-3xl p-8 mb-8">
                <h2 class="text-2xl font-black text-white mb-6">Pokračovať v učení</h2>
                
                <!-- Loading -->
                <div v-if="loading" class="flex justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                </div>
                
                <div v-else-if="recentCourses.length > 0" class="space-y-4">
                <div
                    v-for="course in recentCourses"
                    :key="course.id"
                    class="group bg-dark-800/30 border border-dark-700/50 rounded-2xl p-5 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/80"
                >
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="font-bold text-white mb-2 text-lg">{{ course.title }}</h3>
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-full max-w-xs bg-dark-700/50 rounded-full h-2">
                                <div
                                    class="bg-gradient-to-r from-primary-500 to-secondary-500 h-2 rounded-full transition-all duration-500 ease-out"
                                    :style="{ width: `${course.progress}%` }"
                                ></div>
                            </div>
                            <span class="text-sm text-dark-300 font-medium whitespace-nowrap">{{ course.progress }}%</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <router-link
                        v-if="course.progress < 100"
                        :to="{ name: 'CourseStudy', params: { slug: course.slug } }"
                        class="inline-block w-full sm:w-auto text-center px-6 py-3 bg-primary-600 hover:bg-primary-500 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-primary-500/25"
                        >
                        Pokračovať
                        </router-link>
                        <div
                        v-else
                        class="inline-block w-full sm:w-auto text-center px-6 py-3 bg-dark-700 text-dark-300 rounded-xl text-sm font-bold cursor-not-allowed"
                        >
                        Nedostupné
                        </div>
                    </div>
                    </div>
                </div>
                </div>

                <div v-else class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-dark-800 flex items-center justify-center">
                        <svg class="h-8 w-8 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                </div>
                <h3 class="mt-2 text-lg font-bold text-white">Zatiaľ žiadne kurzy</h3>
                <p class="mt-1 text-dark-300 font-light">Začnite zapísaním do svojho prvého kurzu.</p>
                <div class="mt-8">
                    <button
                    @click="currentTab = 'catalog'"
                    class="px-8 py-4 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-primary-500/25 inline-flex"
                    >
                    Prehliadať kurzy
                    </button>
                </div>
                </div>
            </div>

            <!-- Your Courses Section -->
            <div class="bg-dark-900 border border-dark-800 rounded-3xl p-8">
                <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black text-white">Tvoje kurzy</h2>
                <router-link
                    to="/my-courses"
                    class="px-4 py-2 text-sm font-bold text-dark-300 bg-dark-800 rounded-xl hover:bg-dark-700 hover:text-white transition-all"
                >
                    Zobraziť všetky
                </router-link>
                </div>
                
                <!-- Loading -->
                <div v-if="loading" class="flex justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                </div>
                
                <!-- Courses Grid -->
                <div v-else-if="enrolledCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    v-for="course in enrolledCourses.slice(0, 4)"
                    :key="course.id"
                    class="group bg-dark-800/30 border border-dark-700/50 rounded-2xl p-4 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/80 cursor-pointer"
                    @click="$router.push({ name: 'CourseStudy', params: { slug: course.slug } })"
                >
                    <div class="flex items-start space-x-4">
                    <!-- Course Thumbnail -->
                    <div class="flex-shrink-0">
                        <img
                        v-if="course.thumbnail"
                        :src="course.thumbnail"
                        :alt="course.title"
                        class="w-20 h-20 object-cover rounded-xl shadow-md group-hover:shadow-primary-500/10 transition-shadow"
                        >
                        <div
                        v-else
                        class="w-20 h-20 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-xl flex items-center justify-center shadow-md"
                        >
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        </div>
                    </div>
                    
                    <!-- Course Info -->
                    <div class="flex-1 min-w-0 py-1">
                        <h3 class="font-bold text-white truncate mb-1 group-hover:text-primary-400 transition-colors">{{ course.title }}</h3>
                        <p v-if="course.instructor?.name" class="text-xs text-dark-400 font-medium uppercase tracking-wider mb-3">{{ course.instructor.name }}</p>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-dark-700/50 rounded-full h-1.5 mb-2">
                        <div
                            class="bg-gradient-to-r from-primary-500 to-secondary-500 h-1.5 rounded-full transition-all duration-300"
                            :style="{ width: `${course.enrollment_data?.progress_percentage || 0}%` }"
                        ></div>
                        </div>
                        <div class="flex items-center justify-between">
                        <span class="text-xs text-dark-300 font-medium">
                            {{ course.enrollment_data?.progress_percentage || 0 }}% dokončené
                        </span>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                
                <!-- Empty State -->
                <div v-else class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-dark-800 flex items-center justify-center">
                    <svg class="h-8 w-8 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                <h3 class="mt-2 text-lg font-bold text-white">Žiadne zakúpené kurzy</h3>
                <p class="mt-1 text-dark-300 font-light">Začnite objavovaním našich kurzov.</p>
                <div class="mt-6">
                    <button
                        @click="currentTab = 'catalog'"
                        class="px-6 py-3 bg-dark-800/50 hover:bg-dark-800 text-white font-bold rounded-xl border border-dark-700 hover:border-dark-600 transition-all text-sm inline-flex items-center"
                    >
                    Prehliadať kurzy
                    </button>
                </div>
                </div>
            </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
            <!-- Quick Actions -->
            <div class="bg-dark-900 border border-dark-800 rounded-3xl p-6 sticky top-24">
                <h3 class="text-xl font-black text-white mb-6">Rýchle akcie</h3>
                <div class="space-y-3">
                <router-link
                    to="/my-courses"
                    class="w-full bg-dark-800/50 hover:bg-dark-800 border border-dark-700 hover:border-dark-600 text-white px-5 py-3.5 rounded-xl text-sm font-bold transition-all block text-center backdrop-blur-sm"
                >
                    Moje kurzy
                </router-link>
                <router-link
                    to="/profile"
                    class="w-full bg-dark-800/50 hover:bg-dark-800 border border-dark-700 hover:border-dark-600 text-white px-5 py-3.5 rounded-xl text-sm font-bold transition-all block text-center backdrop-blur-sm"
                >
                    Upraviť profil
                </router-link>
                </div>
            </div>
            </div>
        </div>
      </div>

      <!-- Catalog Tab -->
      <div v-show="currentTab === 'catalog'" class="transition-opacity duration-300 animate-fade-in">
        
        <!-- Master View (Grid) -->
        <div v-show="!selectedCatalogCourse" class="bg-dark-900/50 border border-dark-800 rounded-3xl p-4 sm:p-8 transition-all duration-300">
            <CourseCatalog @select="handleCatalogSelect" />
        </div>

        <!-- Detail View -->
         <transition name="slide-up">
            <div v-if="selectedCatalogCourse" class="relative">
                <CourseDetail 
                    :course="selectedCatalogCourse"
                    back-button
                    is-dashboard
                    :loading="showCheckoutLoading && checkoutCourse?.id === selectedCatalogCourse?.id"
                    @back="selectedCatalogCourse = null"
                    @purchase="handlePurchase"
                />
            </div>
         </transition>
      </div>
    </div>

    <!-- Checkout Loading Modal -->
    <CheckoutLoadingModal
      :show="showCheckoutLoading"
      :courseTitle="checkoutCourse?.title"
      :coursePrice="checkoutCourse?.price"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import { useCourseStore } from '@/stores/course'
import { usePurchase } from '@/composables/usePurchase'
import CourseCatalog from '@/components/course/CourseCatalog.vue'
import CourseDetail from '@/components/course/CourseDetail.vue'
import CheckoutLoadingModal from '@/components/ui/CheckoutLoadingModal.vue'
import { BookOpenIcon, CheckBadgeIcon, AcademicCapIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const authStore = useAuthStore()
const enrollmentStore = useEnrollmentStore()
const courseStore = useCourseStore()

const user = computed(() => authStore.user)

// Tabs
const currentTab = ref(route.query.tab || 'overview')
watch(() => route.query.tab, (newTab) => {
    currentTab.value = newTab || 'overview'
})


// Catalog State
const selectedCatalogCourse = ref(null)

const handleCatalogSelect = async (course) => {
    // Instead of navigating, set selected course to show details in dashboard
    try {
        // Optimistically set, assuming we have basic data. CourseDetail can fetch more if needed or we update it.
        selectedCatalogCourse.value = course
        
        // Fetch full details to ensure we have requirements etc.
        const fullCourse = await courseStore.fetchCourse(course.slug)
        selectedCatalogCourse.value = { ...course, ...fullCourse }
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' })
    } catch (e) {
        console.error('Failed to load course details', e)
    }
}

const { loading: showCheckoutLoading, checkoutCourse, handlePurchase: startPurchase } = usePurchase()

const handlePurchase = (course) => {
    const successUrl = `${window.location.origin}/payment/success?session_id={CHECKOUT_SESSION_ID}`
    const cancelUrl = `${window.location.origin}/dashboard`
    startPurchase(course, successUrl, cancelUrl)
}

// Loading state
const loading = computed(() => enrollmentStore.loading)

// Get courses from enrollment store
const enrolledCourses = computed(() => enrollmentStore.myCourses || [])

const completedCourses = computed(() => {
  return enrolledCourses.value.filter(course => {
    const progress = course.enrollment_data?.progress_percentage || 0
    return progress >= 100
  }).length
})

const certificates = computed(() => completedCourses.value)

// Sort courses by progress percentage (highest to lowest)
const recentCourses = computed(() => {
  return enrolledCourses.value
    .filter(course => {
      const progress = course.enrollment_data?.progress_percentage || 0
      return progress > 0 // Show courses with any progress
    })
    .sort((a, b) => {
      const progressA = a.enrollment_data?.progress_percentage || 0
      const progressB = b.enrollment_data?.progress_percentage || 0
      return progressB - progressA // Sort descending (highest first)
    })
    .slice(0, 5) // Show top 5 courses
    .map(course => ({
      id: course.id,
      title: course.title,
      slug: course.slug,
      progress: course.enrollment_data?.progress_percentage || 0,
      thumbnail: course.thumbnail,
      instructor: course.instructor
    }))
})

const loadDashboardData = async () => {
  try {
    // Load user courses from enrollment store
    await enrollmentStore.loadMyCourses(true)
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  }
}

onMounted(() => {
  loadDashboardData()
})
</script>

<style scoped>
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

.delay-500 { animation-delay: 0.5s; }
.delay-1000 { animation-delay: 1s; }

/* Marquee Animation */
.marquee-container {
  animation: marquee 40s linear infinite;
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* Hide scrollbar */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
