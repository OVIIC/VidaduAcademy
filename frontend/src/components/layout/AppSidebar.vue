<template>
  <aside 
    class="bg-secondary-800 border-r border-secondary-700 min-h-screen hidden md:flex flex-col shrink-0 transition-all duration-300 ease-in-out"
    :class="[isCollapsed ? 'w-20' : 'w-64']"
  >
    <div class="p-6 flex items-center justify-between">
      <router-link v-if="!isCollapsed" to="/" class="flex items-center gap-2 overflow-hidden whitespace-nowrap">
        <span class="text-2xl font-bold text-white transition-all duration-300">
          Vidadu
        </span>
      </router-link>
      
      <button 
        @click="toggleCollapse" 
        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-600 hover:border-white text-gray-400 hover:text-white transition-all duration-200 focus:outline-none"
        :class="{ 'mx-auto': isCollapsed }"
      >
        <component :is="isCollapsed ? ChevronRightIcon : ChevronLeftIcon" class="h-4 w-4" />
      </button>
    </div>
    
    <nav class="flex-1 px-4 pt-4 space-y-1 overflow-y-auto">
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
    
    <div class="p-4 border-t border-gray-800 mt-auto relative">
      <button 
        @click="showUserMenu = !showUserMenu"
        class="w-full flex items-center gap-3 hover:bg-secondary-700 rounded-lg p-2 transition-colors"
        :class="{ 'justify-center': isCollapsed }"
      >
        <div class="h-8 w-8 rounded-full bg-gray-800 flex items-center justify-center text-primary-500 font-bold border border-gray-700 flex-shrink-0">
          {{ userInitials }}
        </div>
        <div 
          class="flex-1 min-w-0 transition-opacity duration-200 text-left"
          :class="[isCollapsed ? 'opacity-0 w-0 hidden' : 'opacity-100']"
        >
          <p class="text-sm font-medium text-white truncate">
            {{ authStore.user?.name || 'User' }}
          </p>
          <p class="text-xs text-gray-500 truncate">
            {{ authStore.user?.email }}
          </p>
        </div>
      </button>

      <!-- Dropdown Menu -->
      <div 
        v-if="showUserMenu && !isCollapsed"
        class="absolute bottom-full left-4 right-4 mb-2 bg-secondary-surface border border-gray-700 rounded-lg shadow-lg overflow-hidden"
      >
        <router-link
          to="/profile"
          @click="showUserMenu = false"
          class="flex items-center gap-3 px-4 py-3 hover:bg-secondary-700 transition-colors text-gray-300 hover:text-white"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span class="text-sm font-medium">Settings</span>
        </router-link>
        <button
          @click="handleLogout"
          class="w-full flex items-center gap-3 px-4 py-3 hover:bg-secondary-700 transition-colors text-gray-300 hover:text-white"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span class="text-sm font-medium">Logout</span>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { 
  HomeIcon, 
  BookOpenIcon, 
  UserIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()
const isCollapsed = ref(false)
const showUserMenu = ref(false)

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value
}

const navigation = [
  { name: 'Dashboard', href: '/dashboard', icon: HomeIcon },
  { name: 'Moje kurzy', href: '/my-courses', icon: BookOpenIcon },
  { name: 'Profil', href: '/profile', icon: UserIcon },
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

const handleLogout = async () => {
  showUserMenu.value = false
  await authStore.logout()
  router.push('/login')
}
</script>
