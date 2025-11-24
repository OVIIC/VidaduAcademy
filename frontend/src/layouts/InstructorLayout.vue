<template>
  <div class="h-screen flex bg-black overflow-hidden">
    <!-- Instructor Sidebar -->
    <aside 
      class="bg-black border-r border-gray-800 min-h-screen hidden md:flex flex-col shrink-0 transition-all duration-300 ease-in-out"
      :class="[isCollapsed ? 'w-20' : 'w-64']"
    >
      <div class="p-6 flex items-center justify-between">
        <router-link to="/instructor" class="flex items-center gap-2 overflow-hidden whitespace-nowrap">
          <span class="text-xl font-bold text-white transition-all duration-300">
            {{ isCollapsed ? 'I' : 'Instructor' }}
          </span>
        </router-link>
        
        <button 
          @click="toggleCollapse" 
          class="text-gray-500 hover:text-white transition-colors focus:outline-none"
          :class="{ 'mx-auto': isCollapsed }"
        >
          <component :is="isCollapsed ? ChevronRightIcon : ChevronLeftIcon" class="h-5 w-5" />
        </button>
      </div>
      
      <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
        <router-link 
          v-for="item in navigation" 
          :key="item.name"
          :to="item.href"
          class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
          :class="[
            $route.path === item.href
              ? 'bg-gray-800 text-white'
              : 'text-gray-400 hover:bg-gray-900 hover:text-white',
            isCollapsed ? 'justify-center' : ''
          ]"
          :title="isCollapsed ? item.name : ''"
        >
          <component 
            :is="item.icon" 
            class="h-5 w-5 flex-shrink-0 transition-colors"
            :class="[
              $route.path === item.href ? 'text-purple-500' : 'text-gray-500 group-hover:text-gray-300',
              isCollapsed ? '' : 'mr-3'
            ]"
          />
          <span 
            class="transition-opacity duration-200"
            :class="[isCollapsed ? 'opacity-0 w-0 hidden' : 'opacity-100']"
          >
            {{ item.name }}
          </span>
        </router-link>
      </nav>
      
      <div class="p-4 border-t border-gray-800 mt-auto">
        <div class="flex items-center gap-3" :class="{ 'justify-center': isCollapsed }">
          <div class="h-8 w-8 rounded-full bg-purple-900/30 flex items-center justify-center text-purple-500 font-bold border border-purple-900/50 flex-shrink-0">
            IN
          </div>
          <div 
            class="flex-1 min-w-0 transition-opacity duration-200"
            :class="[isCollapsed ? 'opacity-0 w-0 hidden' : 'opacity-100']"
          >
            <p class="text-sm font-medium text-white truncate">
              Instructor
            </p>
            <button @click="logout" class="text-xs text-gray-500 hover:text-white truncate text-left w-full">
              Sign out
            </button>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Mobile header -->
      <div class="md:hidden bg-black border-b border-gray-800 p-4 flex items-center justify-between">
        <span class="font-bold text-xl text-white">Instructor</span>
        <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="text-gray-400 hover:text-white">
          <span class="sr-only">Open sidebar</span>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <main class="flex-1 overflow-y-auto focus:outline-none bg-black">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { 
  HomeIcon, 
  BookOpenIcon, 
  AcademicCapIcon,
  CurrencyDollarIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()
const isCollapsed = ref(false)
const isMobileMenuOpen = ref(false)

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

const navigation = [
  { name: 'Dashboard', href: '/instructor', icon: HomeIcon },
  { name: 'My Courses', href: '/instructor/courses', icon: BookOpenIcon },
  { name: 'Students', href: '/instructor/students', icon: AcademicCapIcon },
  { name: 'Earnings', href: '/instructor/earnings', icon: CurrencyDollarIcon },
]
</script>
