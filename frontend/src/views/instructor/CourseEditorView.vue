<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div class="flex items-center">
        <button @click="$router.push('/instructor/courses')" class="mr-4 text-gray-400 hover:text-white">
          &larr; Back
        </button>
        <h1 class="text-2xl font-bold text-white">Edit Course</h1>
      </div>
      <div class="flex gap-3">
        <span v-if="lastSaved" class="text-sm text-gray-500 self-center mr-2">
          Saved {{ lastSaved }}
        </span>
        <button 
          @click="saveCourse" 
          :disabled="saving"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors disabled:opacity-50"
        >
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-500">
      Loading course details...
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Basic Info Card -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
          <h2 class="text-lg font-medium text-white mb-4">Basic Information</h2>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-400 mb-1">Course Title</label>
              <input 
                v-model="form.title"
                type="text" 
                class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-400 mb-1">Slug (URL)</label>
              <input 
                v-model="form.slug"
                type="text" 
                class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-400 mb-1">Short Description</label>
              <textarea 
                v-model="form.short_description"
                rows="2"
                class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-400 mb-1">Full Description</label>
              <textarea 
                v-model="form.description"
                rows="6"
                class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Curriculum Placeholder -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 opacity-75">
          <h2 class="text-lg font-medium text-white mb-4">Curriculum</h2>
          <div class="text-center py-8 border-2 border-dashed border-gray-700 rounded-lg">
            <p class="text-gray-400">Lesson management coming soon...</p>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Status Card -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
          <h2 class="text-lg font-medium text-white mb-4">Publishing</h2>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-400 mb-1">Status</label>
              <select 
                v-model="form.status"
                class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-400 mb-1">Difficulty Level</label>
              <select 
                v-model="form.difficulty_level"
                class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Pricing Card -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
          <h2 class="text-lg font-medium text-white mb-4">Pricing</h2>
          
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-1">Price (USD)</label>
            <div class="relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-400 sm:text-sm">$</span>
              </div>
              <input 
                v-model="form.price"
                type="number" 
                min="0"
                step="0.01"
                class="w-full bg-gray-800 border border-gray-700 rounded-md pl-7 pr-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>
            <p class="mt-1 text-xs text-gray-500">Set to 0 for free course</p>
          </div>
        </div>

        <!-- Thumbnail Card -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
          <h2 class="text-lg font-medium text-white mb-4">Thumbnail</h2>
          
          <div class="space-y-4">
            <div v-if="form.thumbnail" class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-800 mb-2">
              <img :src="form.thumbnail" alt="Course thumbnail" class="object-cover w-full h-full">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-400 mb-1">Image URL</label>
              <input 
                v-model="form.thumbnail"
                type="text" 
                placeholder="https://..."
                class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
              <p class="mt-1 text-xs text-gray-500">Enter a direct URL to an image</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '@/services/api'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const loading = ref(true)
const saving = ref(false)
const lastSaved = ref(null)

const form = ref({
  title: '',
  slug: '',
  description: '',
  short_description: '',
  price: 0,
  status: 'draft',
  difficulty_level: 'beginner',
  thumbnail: '',
  what_you_will_learn: [],
  requirements: []
})

const fetchCourse = async () => {
  loading.value = true
  try {
    const response = await api.get(`/instructor/courses/${route.params.id}`)
    const course = response.data
    form.value = {
      title: course.title,
      slug: course.slug,
      description: course.description || '',
      short_description: course.short_description || '',
      price: course.price,
      status: course.status,
      difficulty_level: course.difficulty_level,
      thumbnail: course.thumbnail || '',
      what_you_will_learn: course.what_you_will_learn || [],
      requirements: course.requirements || []
    }
  } catch (error) {
    toast.error('Failed to load course')
    router.push('/instructor/courses')
  } finally {
    loading.value = false
  }
}

const saveCourse = async () => {
  saving.value = true
  try {
    await api.put(`/instructor/courses/${route.params.id}`, form.value)
    toast.success('Course saved successfully')
    lastSaved.value = new Date().toLocaleTimeString()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to save course')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchCourse()
})
</script>
