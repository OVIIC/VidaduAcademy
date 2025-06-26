<template>
  <div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Všetky kurzy</h1>
        <p class="mt-2 text-gray-600">Objavte kurzy na rast vašeho YouTube kanála</p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>

      <!-- Courses Grid -->
      <div v-else-if="courses && courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="course in courses" 
          :key="course.id"
          class="card bg-white border rounded-lg p-4 shadow-sm"
        >
          <h3 class="text-lg font-semibold mb-2">{{ course.title }}</h3>
          <p class="text-gray-600 text-sm mb-2">{{ course.short_description }}</p>
          <div class="flex justify-between items-center">
            <span class="text-xl font-bold text-primary-600">${{ course.price }}</span>
            <button class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
              Zistiť viac
            </button>
          </div>
        </div>
      </div>

      <!-- No courses found -->
      <div v-else class="text-center py-12">
        <div class="text-gray-400 text-lg">Žiadne kurzy sa nenašli</div>
        <p class="text-gray-500 mt-2">Skúste sa neskôr vrátiť pre nové kurzy.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { courseService } from '@/services'

const courses = ref([])
const loading = ref(false)

const loadCourses = async () => {
  loading.value = true
  try {
    console.log('Loading courses...')
    const response = await courseService.getAllCourses()
    console.log('API response:', response)
    courses.value = response.data || []
    console.log('Courses loaded:', courses.value.length)
  } catch (error) {
    console.error('Error loading courses:', error)
    courses.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCourses()
})
</script>
