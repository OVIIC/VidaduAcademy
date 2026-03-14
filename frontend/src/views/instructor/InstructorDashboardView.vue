<template>
  <div class="text-white font-sans selection:bg-primary-500 selection:text-white">
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-24">
      
      <!-- Header -->
      <div class="mb-8 transition-all duration-300">
        <h1 class="text-3xl sm:text-4xl font-black text-white mb-2">Lektorský Panel, {{ user?.name }}!</h1>
        <p class="text-xl text-dark-300 font-extralight">Spravujte svoje kurzy a študentov</p>
      </div>

      <div class="transition-opacity duration-300 animate-fade-in">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-dark-800/50 rounded-2xl p-6 border border-dark-700">
            <h3 class="text-dark-400 text-sm font-medium mb-1">Celkovo Študentov</h3>
            <p class="text-3xl font-bold">{{ stats.total_students }}</p>
          </div>
          <div class="bg-dark-800/50 rounded-2xl p-6 border border-dark-700">
            <h3 class="text-dark-400 text-sm font-medium mb-1">Aktivne Kurzy</h3>
            <p class="text-3xl font-bold">{{ stats.active_courses }}</p>
          </div>
          <div class="bg-dark-800/50 rounded-2xl p-6 border border-dark-700">
            <h3 class="text-dark-400 text-sm font-medium mb-1">Celkové Príjmy</h3>
            <p class="text-3xl font-bold text-primary-400">€{{ stats.total_revenue }}</p>
          </div>
          <div class="bg-dark-800/50 rounded-2xl p-6 border border-dark-700">
            <h3 class="text-dark-400 text-sm font-medium mb-1">Všetky Kurzy</h3>
            <p class="text-3xl font-bold">{{ stats.total_courses }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Vaše Kurzy</h2>
                    <button 
                         @click="createNewCourse"
                         class="bg-primary-600 hover:bg-primary-500 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        + Vytvoriť Kurz
                    </button>
                </div>

                <div v-if="loading" class="text-center py-10 text-dark-400">
                    Načítavam kurzy...
                </div>
                
                <div v-else-if="courses.length === 0" class="bg-dark-800/30 rounded-2xl p-8 border border-dark-700 text-center">
                    <p class="text-dark-300 mb-4">Zatiaľ nemáte žiadne kurzy.</p>
                    <button @click="createNewCourse" class="text-primary-400 hover:text-primary-300 font-medium">
                        Vytvorte si svoj prvý kurz
                    </button>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div v-for="course in courses" :key="course.id" class="bg-dark-800/50 rounded-2xl border border-dark-700 overflow-hidden group">
                        <div class="aspect-video bg-dark-700 relative">
                            <img v-if="course.thumbnail" :src="course.thumbnail" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" />
                            <div v-else class="w-full h-full flex items-center justify-center text-dark-500">
                                Bez obrázku
                            </div>
                            <div class="absolute top-2 right-2 px-2 py-1 text-xs font-bold rounded" :class="course.status === 'published' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400'">
                                {{ course.status === 'published' ? 'Publikovaný' : 'Koncept' }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-lg mb-1 truncate">{{ course.title }}</h3>
                            <div class="flex items-center text-sm text-dark-400 mb-4">
                                <span class="mr-4">Študenti: {{ course.enrollments_count || 0 }}</span>
                                <span>Cena: {{ course.price > 0 ? `€${course.price}` : 'Zadarmo' }}</span>
                            </div>
                            <!-- Currently a placeholder for edit link, can route to standard admin or frontend builder later -->
                            <a :href="`/admin/courses/${course.id}/edit`" target="_blank" class="block w-full text-center bg-dark-700 hover:bg-dark-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                Upraviť Kurz v Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-dark-900 border border-dark-800 rounded-3xl p-6 sticky top-24">
                    <h3 class="text-xl font-black text-white mb-6">Rýchle akcie</h3>
                    <div class="space-y-3">
                        <button
                            @click="createNewCourse"
                            class="w-full bg-primary-600/20 hover:bg-primary-600/30 border border-primary-500/30 text-primary-400 px-5 py-3.5 rounded-xl text-sm font-bold transition-all block text-center focus:outline-none focus:ring-2"
                        >
                            Nový Kurz
                        </button>
                        <router-link
                            to="/profile"
                            class="w-full bg-dark-800/50 hover:bg-dark-800 border border-dark-700 hover:border-dark-600 text-white px-5 py-3.5 rounded-xl text-sm font-bold transition-all block text-center backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-dark-600"
                        >
                            Upraviť profil
                        </router-link>
                        <a 
                            href="/admin" 
                            target="_blank"
                            class="w-full bg-dark-800/50 hover:bg-dark-800 border border-dark-700 hover:border-dark-600 text-white px-5 py-3.5 rounded-xl text-sm font-bold transition-all block text-center backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-dark-600"
                        >
                            Otvoriť Admin Panel
                        </a>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { api } from '@/services/api'
import { useToast } from 'vue-toastification'

const authStore = useAuthStore()
const toast = useToast()
const user = computed(() => authStore.user)

const loading = ref(true)
const courses = ref([])
const stats = ref({
    total_students: 0,
    active_courses: 0,
    total_revenue: 0,
    total_courses: 0
})

const fetchDashboardData = async () => {
    loading.value = true
    try {
        const response = await api.get('/instructor/dashboard')
        stats.value = response.data.stats
        courses.value = response.data.recent_courses
    } catch (error) {
        console.error('Failed to load instructor dashboard', error)
        toast.error('Nepodarilo sa načítať dáta lektora.')
    } finally {
        loading.value = false
    }
}

const createNewCourse = async () => {
    toast.info('Vytváranie kurzov je dostupné v admin paneli.')
    window.open('/admin/courses/create', '_blank')
}

onMounted(() => {
    fetchDashboardData()
})
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
