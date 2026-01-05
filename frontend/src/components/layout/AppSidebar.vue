<template>
  <aside 
    class="relative z-30 m-4 h-[calc(100vh-2rem)] hidden md:flex flex-col shrink-0 transition-all duration-300 ease-in-out p-4 rounded-3xl border border-dark-700/50 bg-dark-900/80 backdrop-blur-xl shadow-2xl select-none"
    :class="[isCollapsed ? 'w-24' : 'w-72']"
  >
    <!-- Header -->
    <button 
      @click="toggleCollapse" 
      class="absolute right-0 top-8 transform translate-x-1/2 z-50 w-8 h-8 flex items-center justify-center rounded-xl bg-dark-800 border border-dark-700 text-gray-400 hover:text-white hover:border-primary-500 transition-all duration-200 focus:outline-none shadow-xl"
    >
      <component :is="isCollapsed ? ChevronRightIcon : ChevronLeftIcon" class="h-4 w-4" />
    </button>

    <!-- Header -->
    <div 
      class="flex items-center mb-8 px-2 pt-2 transition-all duration-300 h-10"
      :class="[isCollapsed ? 'justify-center' : 'justify-start']"
    >
      <router-link v-if="!isCollapsed" to="/" class="flex items-center gap-2 overflow-hidden whitespace-nowrap shrink-0">
        <span class="text-2xl font-bold bg-gradient-to-r from-primary-400 to-primary-600 bg-clip-text text-transparent transition-all duration-300">
          Vidadu
        </span>
      </router-link>
      <div v-else class="flex justify-center shrink-0 w-full">
         <span class="text-xl font-bold text-primary-500">V</span>
      </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 space-y-2 overflow-y-auto custom-scrollbar">
      <router-link 
        v-for="item in navigation" 
        :key="item.name"
        :to="item.href"
        custom
        v-slot="{ href, navigate }"
      >
        <a
          :href="href"
          @click="navigate"
          class="group flex items-center px-4 py-3 text-sm font-medium rounded-2xl transition-all duration-300 whitespace-nowrap relative overflow-hidden focus:outline-none focus:ring-0"
          :class="[
            isItemActive(item.href)
              ? 'bg-white/10 text-white shadow-inner'
              : 'text-gray-400 hover:bg-white/5 hover:text-white',
            isCollapsed ? 'justify-center px-0 w-12 h-12 mx-auto' : ''
          ]"
          :title="isCollapsed ? item.name : ''"
        >
          <component 
            :is="item.icon" 
            class="h-5 w-5 flex-shrink-0 transition-colors duration-200"
            :class="[
              isItemActive(item.href) ? 'text-white' : 'text-gray-500 group-hover:text-white',
              isCollapsed ? '' : 'mr-3'
            ]"
          />
          
          <span 
            class="transition-all duration-200 relative z-10"
            :class="[isCollapsed ? 'opacity-0 w-0 hidden' : 'opacity-100']"
          >
            {{ item.name }}
          </span>

          <!-- Active Indicator (Removed distinct pill background, using class based bg) -->
        </a>
      </router-link>
    </nav>
    
    <!-- User Menu -->
    <div ref="userMenuRef" class="mt-auto relative pt-4 border-t border-dark-800">
      <button 
        @click="showUserMenu = !showUserMenu"
        class="w-full flex items-center gap-3 hover:bg-dark-800 rounded-2xl p-2 transition-all duration-200"
        :class="{ 'justify-center': isCollapsed }"
      >
        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-500 to-secondary-600 p-[2px] flex-shrink-0">
          <div class="h-full w-full rounded-full bg-dark-900 flex items-center justify-center text-white text-sm font-bold border-2 border-transparent">
             {{ userInitials }}
          </div>
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
        
        <div v-if="!isCollapsed" class="text-gray-500">
           <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
           </svg>
        </div>
      </button>

      <!-- Dropdown Menu -->
      <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <div 
          v-if="showUserMenu"
          class="absolute bg-dark-800 border border-dark-700 rounded-2xl shadow-xl overflow-hidden backdrop-blur-xl z-50"
          :class="[
            isCollapsed 
              ? 'left-full bottom-0 ml-4 w-60 origin-bottom-left' 
              : 'bottom-full left-0 right-0 mb-2 origin-bottom'
          ]"
        >
          <router-link
            to="/profile"
            @click="showUserMenu = false"
            class="flex items-center gap-3 px-4 py-3 hover:bg-dark-700 transition-colors text-gray-300 hover:text-white"
          >
            <UserIcon class="w-5 h-5" />
            <span class="text-sm font-medium">Profil</span>
          </router-link>
          <button
            @click="handleLogout"
            class="w-full flex items-center gap-3 px-4 py-3 hover:bg-dark-700 transition-colors text-red-400 hover:text-red-300 border-t border-dark-700"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="text-sm font-medium">Odhlásiť sa</span>
          </button>
        </div>
      </transition>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { 
  HomeIcon, 
  BookOpenIcon, 
  UserIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  Squares2X2Icon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const isCollapsed = ref(false)
const showUserMenu = ref(false)
const userMenuRef = ref(null)

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value
}

const navigation = [
  { name: 'Dashboard', href: '/dashboard', icon: HomeIcon },
  { name: 'Katalóg kurzov', href: '/dashboard?tab=catalog', icon: Squares2X2Icon },
  { name: 'Moje kurzy', href: '/my-courses', icon: BookOpenIcon },
  { name: 'Profil', href: '/profile', icon: UserIcon },
]

const isItemActive = (href) => {
  // If href contains query params, check full match
  if (href.includes('?')) {
    const [path, query] = href.split('?')
    const params = new URLSearchParams(query)
    const tab = params.get('tab')
    return route.path === path && route.query.tab === tab
  }
  
  // Special case for Dashboard root (should act as 'overview' default)
  if (href === '/dashboard') {
    return route.path === '/dashboard' && (!route.query.tab || route.query.tab === 'overview')
  }

  // Default exact match on path (or starts with for subroutes, but here paths are distinct)
  return route.path.startsWith(href)
}

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

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
