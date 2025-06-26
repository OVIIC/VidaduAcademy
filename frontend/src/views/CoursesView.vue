<template>
  <div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Všetky kurzy</h1>
        <p class="mt-2 text-gray-600">Objavte kurzy na rast vašeho YouTube kanála</p>
      </div>

      <!-- Filters -->
      <div class="mb-8 flex flex-wrap gap-4">
        <select v-model="selectedCategory" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
          <option value="">Všetky kategórie</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        
        <select v-model="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
          <option value="created_at">Najnovšie</option>
          <option value="price">Cena: Od najnižšej</option>
          <option value="-price">Cena: Od najvyššej</option>
          <option value="title">Názov: A-Z</option>
        </select>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>

      <!-- Courses Grid -->
      <div v-else-if="filteredCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <CourseCard
          v-for="course in filteredCourses"
          :key="course.id"
          :course="course"
        />
      </div>

      <!-- No courses found -->
      <div v-else class="text-center py-12">
        <div class="text-gray-400 text-lg">Žiadne kurzy sa nenašli</div>
        <p class="text-gray-500 mt-2">Skúste upraviť filtre alebo sa neskôr vráťte pre nové kurzy.</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useCourseStore } from '@/stores/course'
import CourseCard from '@/components/courses/CourseCard.vue'

export default {
  name: 'CoursesView',
  components: {
    CourseCard
  },
  setup() {
    const courseStore = useCourseStore()
    const selectedCategory = ref('')
    const sortBy = ref('created_at')
    const loading = ref(false)

    const categories = computed(() => courseStore.categories)
    const courses = computed(() => courseStore.courses)

    const filteredCourses = computed(() => {
      let filtered = courses.value

      if (selectedCategory.value) {
        filtered = filtered.filter(course => course.category_id === selectedCategory.value)
      }

      // Sort courses
      filtered.sort((a, b) => {
        switch (sortBy.value) {
          case 'price':
            return a.price - b.price
          case '-price':
            return b.price - a.price
          case 'title':
            return a.title.localeCompare(b.title)
          case 'created_at':
          default:
            return new Date(b.created_at) - new Date(a.created_at)
        }
      })

      return filtered
    })

    const loadData = async () => {
      loading.value = true
      try {
        await Promise.all([
          courseStore.fetchCourses(),
          courseStore.fetchCategories()
        ])
      } catch (error) {
        console.error('Error loading courses:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadData()
    })

    return {
      selectedCategory,
      sortBy,
      loading,
      categories,
      filteredCourses
    }
  }
}
</script>
