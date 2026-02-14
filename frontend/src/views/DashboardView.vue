<template>
  <div class="text-white font-sans selection:bg-primary-500 selection:text-white">


    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-24">
      <!-- Header (Hidden in Detail View for immersive experience or handled inside Detail) -->
      <div v-show="!selectedCatalogCourse || currentTab === 'overview'" class="mb-8 transition-all duration-300">
        <h1 class="text-3xl sm:text-4xl font-black text-white mb-2">Vitajte späť, {{ user?.name }}!</h1>
        <p class="text-xl text-dark-300 font-extralight">Pokračujte vo svojej vzdelávacej ceste</p>
      </div>

      <!-- Overview Tab -->
      <div v-show="currentTab === 'overview'" class="transition-opacity duration-300 animate-fade-in">
        <!-- Stats Cards -->
        <StatsOverview
          :enrolled-count="enrolledCourses.length"
          :completed-count="completedCourses"
          :certificates-count="certificates"
        />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
            <!-- Continue Learning -->
            <CourseProgressList
              :courses="recentCourses"
              :loading="loading"
              @browse="currentTab = 'catalog'"
            />

            <!-- Your Courses Section -->
            <EnrolledCoursesGrid
              :courses="enrolledCourses"
              :loading="loading"
              :limit="4"
              @browse="currentTab = 'catalog'"
            />
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
            <!-- Quick Actions -->
            <div class="bg-dark-900 border border-dark-800 rounded-3xl p-6 sticky top-24">
                <h3 class="text-xl font-black text-white mb-6">Rýchle akcie</h3>
                <div class="space-y-3">
                <router-link
                    to="/my-courses"
                    class="w-full bg-dark-800/50 hover:bg-dark-800 border border-dark-700 hover:border-dark-600 text-white px-5 py-3.5 rounded-xl text-sm font-bold transition-all block text-center backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-dark-600"
                >
                    Moje kurzy
                </router-link>
                <router-link
                    to="/profile"
                    class="w-full bg-dark-800/50 hover:bg-dark-800 border border-dark-700 hover:border-dark-600 text-white px-5 py-3.5 rounded-xl text-sm font-bold transition-all block text-center backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-dark-600"
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

// Dashboard Components
import StatsOverview from '@/components/dashboard/StatsOverview.vue'
import CourseProgressList from '@/components/dashboard/CourseProgressList.vue'
import EnrolledCoursesGrid from '@/components/dashboard/EnrolledCoursesGrid.vue'

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
/* Marquee Animation not used here anymore but global style */

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
