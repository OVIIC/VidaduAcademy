<template>
  <aside 
    class="bg-secondary-800 border-r border-secondary-700 min-h-screen hidden md:flex flex-col shrink-0 transition-all duration-300 ease-in-out"
    :class="[isCollapsed ? 'w-20' : 'w-64']"
  >
    <div class="p-6 flex items-center justify-between">
      <router-link to="/" class="flex items-center gap-2 overflow-hidden whitespace-nowrap">
        <span class="text-2xl font-bold text-white transition-all duration-300">
          {{ isCollapsed ? 'V' : 'Vidadu' }}
        </span>
      </router-link>
      
      <button 
        @click="toggleCollapse" 
        class="text-gray-400 hover:text-white transition-colors focus:outline-none"
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
            ? 'bg-secondary-700 text-white'
            : 'text-gray-400 hover:bg-secondary-700/50 hover:text-white',
          isCollapsed ? 'justify-center' : ''
        ]"
        :title="isCollapsed ? item.name : ''"
      >
        <component 
          :is="item.icon" 
          class="h-5 w-5 flex-shrink-0 transition-colors"
          :class="[
            $route.path === item.href ? 'text-primary-500' : 'text-gray-500 group-hover:text-gray-300',
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
        <div class="h-8 w-8 rounded-full bg-gray-800 flex items-center justify-center text-primary-500 font-bold border border-gray-700 flex-shrink-0">
          {{ userInitials }}
        </div>
        <div 
          class="flex-1 min-w-0 transition-opacity duration-200"
          :class="[isCollapsed ? 'opacity-0 w-0 hidden' : 'opacity-100']"
        >
          <p class="text-sm font-medium text-white truncate">
            {{ authStore.user?.name || 'User' }}
          </p>
          <p class="text-xs text-gray-500 truncate">
            {{ authStore.user?.email }}
          </p>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { 
  HomeIcon, 
  BookOpenIcon, 
  UserIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const isCollapsed = ref(false)

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value
}

const navigation = [
  { name: 'Dashboard', href: '/dashboard', icon: HomeIcon },
  { name: 'My Courses', href: '/my-courses', icon: BookOpenIcon },
  { name: 'Profile', href: '/profile', icon: UserIcon },
]

const userInitials = computed(() => {
  const name = authStore.user?.name || ''
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})
</script>
