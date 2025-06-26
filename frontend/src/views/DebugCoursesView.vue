<template>
  <div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Debug Kurzy</h1>
      
      <div class="mb-4 p-4 bg-gray-100 rounded">
        <p>Loading: {{ loading }}</p>
        <p>Courses count: {{ courses.length }}</p>
        <p>Raw courses: {{ JSON.stringify(courses, null, 2) }}</p>
      </div>
      
      <div v-if="loading">Načítavam...</div>
      <div v-else-if="courses.length === 0">Žiadne kurzy</div>
      <div v-else>
        <div v-for="course in courses" :key="course.id" class="border p-4 mb-4 rounded">
          <h3>{{ course.title }}</h3>
          <p>{{ course.short_description }}</p>
          <p>Cena: ${{ course.price }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  setup() {
    const courses = ref([])
    const loading = ref(false)

    const loadCourses = async () => {
      loading.value = true
      try {
        console.log('Direct API call starting...')
        const response = await fetch('http://localhost:8000/api/courses')
        const data = await response.json()
        console.log('Direct API response:', data)
        courses.value = data.data || []
        console.log('Courses set to:', courses.value)
      } catch (error) {
        console.error('Error:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadCourses()
    })

    return {
      courses,
      loading
    }
  }
}
</script>
