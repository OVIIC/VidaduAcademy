<template>
  <div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Test CourseCard</h1>
      
      <div v-if="testCourse" class="space-y-4">
        <h2 class="text-xl font-semibold">Raw Course Data:</h2>
        <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto max-h-40">{{ JSON.stringify(testCourse, null, 2) }}</pre>
        
        <h2 class="text-xl font-semibold">Jednoduchý CourseCard:</h2>
        <div class="max-w-sm">
          <SimpleCourseCard :course="testCourse" />
        </div>
        
        <h2 class="text-xl font-semibold">Originálny CourseCard:</h2>
        <div class="max-w-sm">
          <CourseCard :course="testCourse" />
        </div>
      </div>
      
      <div v-else>
        <p>Loading test course...</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import CourseCard from '@/components/courses/CourseCard.vue'
import SimpleCourseCard from '@/components/courses/SimpleCourseCard.vue'

export default {
  name: 'TestView',
  components: {
    CourseCard,
    SimpleCourseCard
  },
  setup() {
    const testCourse = ref(null)

    onMounted(async () => {
      try {
        const response = await fetch('http://localhost:8000/api/courses')
        const data = await response.json()
        console.log('Test API response:', data)
        
        if (data.data && data.data.length > 0) {
          testCourse.value = data.data[0]
          console.log('Test course set:', testCourse.value)
        }
      } catch (error) {
        console.error('Error fetching test course:', error)
      }
    })

    return {
      testCourse
    }
  }
}
</script>
