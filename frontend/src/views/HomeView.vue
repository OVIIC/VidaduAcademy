<template>
  <div class="bg-white">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary-900 via-primary-800 to-purple-900 text-white">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="text-center">
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
            Master YouTube Growth
            <span class="text-youtube-red block">Build Your Channel</span>
          </h1>
          <p class="text-xl lg:text-2xl text-gray-200 mb-8 max-w-3xl mx-auto">
            Learn from successful YouTube creators and transform your channel into a thriving business. 
            Join thousands of students already growing their audiences.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link to="/courses" class="btn-primary text-lg px-8 py-4">
              Explore Courses
            </router-link>
            <router-link to="/register" class="btn-outline border-white text-white hover:bg-white hover:text-primary-900 text-lg px-8 py-4">
              Start Free Trial
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Courses -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
            Featured Courses
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Handpicked courses from our top instructors to accelerate your YouTube journey
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
            View All Courses
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
            <div class="text-gray-600 font-medium">Students Enrolled</div>
          </div>
          <div class="animate-fade-in">
            <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2">50+</div>
            <div class="text-gray-600 font-medium">Expert Courses</div>
          </div>
          <div class="animate-fade-in">
            <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2">95%</div>
            <div class="text-gray-600 font-medium">Success Rate</div>
          </div>
          <div class="animate-fade-in">
            <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2">24/7</div>
            <div class="text-gray-600 font-medium">Support Available</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
            Why Choose VidaduAcademy?
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Everything you need to build a successful YouTube channel in one place
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <AcademicCapIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Expert Instructors</h3>
            <p class="text-gray-600">
              Learn from YouTube creators with millions of subscribers and proven track records of success.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <PlayIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Practical Content</h3>
            <p class="text-gray-600">
              Step-by-step tutorials and real-world examples that you can implement immediately.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <ChartBarIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Track Progress</h3>
            <p class="text-gray-600">
              Monitor your learning journey with detailed progress tracking and completion certificates.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <UsersIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Community Support</h3>
            <p class="text-gray-600">
              Connect with fellow creators and get support from our active community of learners.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <DevicePhoneMobileIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Learn Anywhere</h3>
            <p class="text-gray-600">
              Access your courses on any device, anytime. Download lessons for offline viewing.
            </p>
          </div>

          <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <StarIcon class="h-8 w-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold mb-4">Quality Guaranteed</h3>
            <p class="text-gray-600">
              All courses are carefully curated and regularly updated to ensure the highest quality.
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
            Success Stories
          </h2>
          <p class="text-xl text-gray-600">
            Hear from students who transformed their YouTube channels
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="card p-8 text-center animate-fade-in">
            <div class="flex justify-center mb-4">
              <StarIcon v-for="i in 5" :key="i" class="h-5 w-5 text-yellow-400 fill-current" />
            </div>
            <p class="text-gray-600 mb-6">
              "VidaduAcademy helped me grow from 1K to 100K subscribers in just 6 months. The strategies actually work!"
            </p>
            <div class="font-semibold text-gray-900">Sarah M.</div>
            <div class="text-sm text-gray-500">Gaming Creator</div>
          </div>

          <div class="card p-8 text-center animate-fade-in">
            <div class="flex justify-center mb-4">
              <StarIcon v-for="i in 5" :key="i" class="h-5 w-5 text-yellow-400 fill-current" />
            </div>
            <p class="text-gray-600 mb-6">
              "The monetization course changed everything. I'm now making $5K+ per month from my channel."
            </p>
            <div class="font-semibold text-gray-900">Michael R.</div>
            <div class="text-sm text-gray-500">Tech Reviewer</div>
          </div>

          <div class="card p-8 text-center animate-fade-in">
            <div class="flex justify-center mb-4">
              <StarIcon v-for="i in 5" :key="i" class="h-5 w-5 text-yellow-400 fill-current" />
            </div>
            <p class="text-gray-600 mb-6">
              "Clear, actionable advice from real experts. Best investment I've made for my YouTube journey."
            </p>
            <div class="font-semibold text-gray-900">Emma L.</div>
            <div class="text-sm text-gray-500">Lifestyle Vlogger</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-primary-600 to-purple-600 text-white">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-bold mb-6">
          Ready to Transform Your YouTube Channel?
        </h2>
        <p class="text-xl mb-8 opacity-90">
          Join thousands of successful creators who started their journey with VidaduAcademy
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link to="/register" class="btn-outline border-white text-white hover:bg-white hover:text-primary-600 text-lg px-8 py-4">
            Start Learning Today
          </router-link>
          <router-link to="/courses" class="bg-white text-primary-600 hover:bg-gray-100 font-medium py-4 px-8 rounded-lg transition-colors text-lg">
            Browse Courses
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
