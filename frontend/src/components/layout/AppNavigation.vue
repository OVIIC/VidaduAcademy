<template>
  <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-sm">V</span>
            </div>
            <span class="text-xl font-bold text-gray-900">VidaduAcademy</span>
          </router-link>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:block">
          <div class="ml-10 flex items-baseline space-x-8">
            <router-link
              to="/"
              class="text-gray-900 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              :class="{ 'text-primary-600': $route.name === 'Home' }"
            >
              Domov
            </router-link>
            <router-link
              to="/courses"
              class="text-gray-900 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              :class="{ 'text-primary-600': $route.name === 'Courses' }"
            >
              Kurzy
            </router-link>
            <router-link
              v-if="authStore.isAuthenticated"
              to="/my-courses"
              class="text-gray-900 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              :class="{ 'text-primary-600': $route.name === 'MyCourses' }"
            >
              Moje kurzy
            </router-link>
          </div>
        </div>

        <!-- User Menu / Auth Buttons -->
        <div class="flex items-center space-x-4">
          <!-- Search (Desktop) -->
          <div class="hidden md:block">
            <div class="relative">
              <input
                v-model="searchQuery"
                @keyup.enter="performSearch"
                type="text"
                placeholder="Hľadať kurzy..."
                class="input-field w-64 pl-10 pr-4 py-2"
              >
              <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
            </div>
          </div>

          <!-- Authenticated User Menu -->
          <div v-if="authStore.isAuthenticated" class="relative">
            <Menu as="div" class="relative inline-block text-left">
              <div>
                <MenuButton class="flex items-center space-x-2 bg-white rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-medium">
                      {{ authStore.userInitials }}
                    </span>
                  </div>
                  <ChevronDownIcon class="h-4 w-4 text-gray-400" />
                </MenuButton>
              </div>

              <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <MenuItems class="absolute right-0 mt-2 w-56 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                  <div class="px-4 py-3">
                    <p class="text-sm">Prihlásený ako</p>
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ authStore.user?.email }}
                    </p>
                  </div>
                  
                  <div class="py-1">
                    <MenuItem v-slot="{ active }">
                      <router-link
                        to="/dashboard"
                        :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']"
                      >
                        Nástenka
                      </router-link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <router-link
                        to="/profile"
                        :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']"
                      >
                        Profil
                      </router-link>
                    </MenuItem>
                  </div>
                  
                  <div class="py-1">
                    <MenuItem v-slot="{ active }">
                      <button
                        @click="handleLogout"
                        :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left px-4 py-2 text-sm']"
                      >
                        Odhlásiť sa
                      </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </div>

          <!-- Guest Buttons -->
          <div v-else class="flex items-center space-x-2">
            <router-link to="/login" class="btn-secondary">
              Prihlásiť sa
            </router-link>
            <router-link to="/register" class="btn-primary">
              Zaregistrovať sa
            </router-link>
          </div>

          <!-- Mobile Menu Button -->
          <div class="md:hidden">
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="bg-gray-50 p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
            >
              <Bars3Icon v-if="!mobileMenuOpen" class="h-6 w-6" />
              <XMarkIcon v-else class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div v-show="mobileMenuOpen" class="md:hidden">
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-gray-200">
        <!-- Mobile Search -->
        <div class="px-3 py-2">
          <input
            v-model="searchQuery"
            @keyup.enter="performSearch"
            type="text"
            placeholder="Hľadať kurzy..."
            class="input-field w-full pl-10 pr-4 py-2"
          >
        </div>

        <router-link
          to="/"
          @click="mobileMenuOpen = false"
          class="text-gray-900 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
        >
          Domov
        </router-link>
        <router-link
          to="/courses"
          @click="mobileMenuOpen = false"
          class="text-gray-900 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
        >
          Kurzy
        </router-link>
        
        <template v-if="authStore.isAuthenticated">
          <router-link
            to="/my-courses"
            @click="mobileMenuOpen = false"
            class="text-gray-900 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
          >
            Moje kurzy
          </router-link>
          <router-link
            to="/dashboard"
            @click="mobileMenuOpen = false"
            class="text-gray-900 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
          >
            Nástenka
          </router-link>
          <button
            @click="handleLogout"
            class="text-gray-900 hover:text-primary-600 block w-full text-left px-3 py-2 rounded-md text-base font-medium"
          >
            Odhlásiť sa
          </button>
        </template>
        
        <template v-else>
          <router-link
            to="/login"
            @click="mobileMenuOpen = false"
            class="text-gray-900 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
          >
            Prihlásiť sa
          </router-link>
          <router-link
            to="/register"
            @click="mobileMenuOpen = false"
            class="text-gray-900 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
          >
            Zaregistrovať sa
          </router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import {
  Bars3Icon,
  XMarkIcon,
  MagnifyingGlassIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const mobileMenuOpen = ref(false)
const searchQuery = ref('')

const handleLogout = async () => {
  await authStore.logout()
  router.push('/')
  mobileMenuOpen.value = false
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({
      name: 'Courses',
      query: { search: searchQuery.value.trim() }
    })
    mobileMenuOpen.value = false
  }
}
</script>
