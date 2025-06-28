<template>
  <nav 
    class="fixed top-0 left-0 right-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 safe-area-inset-top transition-colors duration-200"
    role="navigation"
    aria-label="Hlavná navigácia"
  >
    <div class="container-responsive">
      <div class="flex items-center justify-between h-16 px-4">
        <!-- Logo -->
        <router-link 
          to="/" 
          class="flex items-center space-x-2 text-xl font-bold text-gray-900 hover:text-primary-600 transition-colors"
          aria-label="VidaduAcademy domov"
        >
          <div class="w-8 h-8 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">V</span>
          </div>
          <span class="hidden xs:inline">VidaduAcademy</span>
        </router-link>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-8">
          <router-link 
            to="/" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.name === 'Home' }"
          >
            Domov
          </router-link>
          <router-link 
            to="/courses" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.name === 'Courses' }"
          >
            Kurzy
          </router-link>
        </div>
        <!-- Auth Section - Desktop -->
        <div class="hidden md:flex items-center space-x-4">
          <!-- Theme Toggle -->
          <ThemeToggle />
          
          <template v-if="authStore.isAuthenticated">
            <router-link 
              to="/dashboard" 
              class="nav-link"
            >
              Môj účet
            </router-link>
            <button 
              @click="handleLogout"
              class="btn-outline-sm"
            >
              Odhlásiť
            </button>
          </template>
          <template v-else>
            <router-link 
              to="/login" 
              class="nav-link"
            >
              Prihlásiť
            </router-link>
            <router-link 
              to="/register" 
              class="btn-primary-sm"
            >
              Registrácia
            </router-link>
          </template>
        </div>

        <!-- Mobile Menu Button -->
        <button 
          @click="toggleMobileMenu"
          class="md:hidden relative w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
          :aria-expanded="isMobileMenuOpen"
          aria-controls="mobile-menu"
          aria-label="Otvoriť hlavné menu"
        >
          <div class="hamburger-icon">
            <span 
              class="hamburger-line"
              :class="{ 'hamburger-line-active-1': isMobileMenuOpen }"
            ></span>
            <span 
              class="hamburger-line"
              :class="{ 'hamburger-line-active-2': isMobileMenuOpen }"
            ></span>
            <span 
              class="hamburger-line"
              :class="{ 'hamburger-line-active-3': isMobileMenuOpen }"
            ></span>
          </div>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <transition 
      name="mobile-menu"
      @enter="onMobileMenuEnter"
      @leave="onMobileMenuLeave"
    >
      <div 
        v-if="isMobileMenuOpen"
        id="mobile-menu"
        class="md:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg transition-colors duration-200"
      >
        <div class="container-responsive px-4 py-4 space-y-2">
          <!-- Main Navigation -->
          <router-link 
            to="/" 
            @click="closeMobileMenu"
            class="mobile-nav-link"
            :class="{ 'mobile-nav-link-active': $route.name === 'Home' }"
          >
            <HomeIcon class="w-5 h-5" />
            <span>Domov</span>
          </router-link>
          
          <router-link 
            to="/courses" 
            @click="closeMobileMenu"
            class="mobile-nav-link"
            :class="{ 'mobile-nav-link-active': $route.name === 'Courses' }"
          >
            <AcademicCapIcon class="w-5 h-5" />
            <span>Kurzy</span>
          </router-link>
          
          <router-link 
          </router-link>
          
          <router-link 
          </router-link>

          <!-- Divider -->
          <div class="border-t border-gray-200 my-4"></div>

          <!-- Auth Section -->
          <template v-if="authStore.isAuthenticated">
            <router-link 
              to="/dashboard" 
              @click="closeMobileMenu"
              class="mobile-nav-link"
            >
              <UserIcon class="w-5 h-5" />
              <span>Môj účet</span>
            </router-link>
            
            <router-link 
              to="/my-courses" 
              @click="closeMobileMenu"
              class="mobile-nav-link"
            >
              <BookOpenIcon class="w-5 h-5" />
              <span>Moje kurzy</span>
            </router-link>
            
            <button 
              @click="handleLogout"
              class="mobile-nav-link text-red-600 hover:bg-red-50"
            >
              <ArrowRightOnRectangleIcon class="w-5 h-5" />
              <span>Odhlásiť sa</span>
            </button>
          </template>
          
          <template v-else>
            <router-link 
              to="/login" 
              @click="closeMobileMenu"
              class="mobile-nav-link"
            >
              <ArrowRightOnRectangleIcon class="w-5 h-5" />
              <span>Prihlásiť sa</span>
            </router-link>
            
            <router-link 
              to="/register" 
              @click="closeMobileMenu"
              class="btn-primary w-full justify-center mt-4"
            >
              Zaregistrovať sa
            </router-link>
          </template>
          
          <!-- Theme Toggle Section -->
          <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-4">
            <ThemeToggle :in-navigation="true" />
          </div>
        </div>
      </div>
    </transition>

    <!-- Mobile Menu Overlay -->
    <transition name="overlay">
      <div 
        v-if="isMobileMenuOpen"
        class="fixed inset-0 bg-black bg-opacity-25 md:hidden"
        style="top: 64px;"
        @click="closeMobileMenu"
        aria-hidden="true"
      ></div>
    </transition>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import ThemeToggle from '@/components/ui/ThemeToggle.vue'
import {
  HomeIcon,
  AcademicCapIcon,
  InformationCircleIcon,
  EnvelopeIcon,
  UserIcon,
  BookOpenIcon,
  ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()

// Mobile menu state
const isMobileMenuOpen = ref(false)

// Methods
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
  
  // Prevent body scroll when menu is open
  if (isMobileMenuOpen.value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
  document.body.style.overflow = ''
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    closeMobileMenu()
    router.push('/')
  } catch (error) {
    console.error('Logout error:', error)
  }
}

// Transition handlers
const onMobileMenuEnter = (el) => {
  el.style.height = '0'
  el.offsetHeight // Force reflow
  el.style.height = el.scrollHeight + 'px'
}

const onMobileMenuLeave = (el) => {
  el.style.height = el.scrollHeight + 'px'
  el.offsetHeight // Force reflow
  el.style.height = '0'
}

// Handle escape key and outside clicks
const handleKeydown = (event) => {
  if (event.key === 'Escape' && isMobileMenuOpen.value) {
    closeMobileMenu()
  }
}

// Lifecycle
onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
  document.body.style.overflow = '' // Cleanup
})

// Close menu on route change
router.afterEach(() => {
  closeMobileMenu()
})
</script>

<style scoped>
/* Navigation Links */
.nav-link {
  color: rgb(55 65 81);
  font-weight: 500;
  transition: color 0.2s;
  padding: 0.5rem 0;
}

.nav-link:hover {
  color: rgb(79 70 229);
}

.nav-link-active {
  color: rgb(79 70 229);
  font-weight: 600;
}

/* Dark mode nav-link styles */
.dark .nav-link {
  color: rgb(209 213 219);
}

.dark .nav-link:hover {
  color: rgb(129 140 248);
}

.dark .nav-link-active {
  color: rgb(129 140 248);
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  color: rgb(55 65 81);
  font-weight: 500;
  border-radius: 0.5rem;
  transition: all 0.2s;
}

.mobile-nav-link:hover {
  color: rgb(79 70 229);
  background-color: rgb(249 250 251);
}

.mobile-nav-link-active {
  color: rgb(79 70 229);
  background-color: rgb(238 242 255);
  font-weight: 600;
}

/* Dark mode mobile-nav-link styles */
.dark .mobile-nav-link {
  color: rgb(209 213 219);
}

.dark .mobile-nav-link:hover {
  color: rgb(129 140 248);
  background-color: rgb(55 65 81);
}

.dark .mobile-nav-link-active {
  color: rgb(129 140 248);
  background-color: rgb(30 27 75);
}

/* Hamburger Menu Animation */
.hamburger-icon {
  position: relative;
  width: 1.5rem;
  height: 1.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.hamburger-line {
  display: block;
  width: 1.5rem;
  height: 0.125rem;
  background-color: rgb(17 24 39);
  transition: all 0.3s ease-in-out;
}

.hamburger-line:nth-child(1) {
  margin-bottom: 0.25rem;
}

.hamburger-line:nth-child(2) {
  margin-bottom: 0.25rem;
}

.hamburger-line-active-1 {
  transform: rotate(45deg) translateY(0.5rem);
}

.hamburger-line-active-2 {
  opacity: 0;
}

.hamburger-line-active-3 {
  transform: rotate(-45deg) translateY(-0.5rem);
}

/* Mobile Menu Transitions */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: all 0.3s ease-in-out;
  overflow: hidden;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  height: 0;
}

.mobile-menu-enter-to,
.mobile-menu-leave-from {
  opacity: 1;
}

/* Overlay Transitions */
.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.3s;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}

/* Safe Area Support */
.safe-area-inset-top {
  padding-top: max(env(safe-area-inset-top), 0px);
}

/* Button Styles */
.btn-primary-sm {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  background-color: rgb(79 70 229);
  color: white;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: background-color 0.2s;
}

.btn-primary-sm:hover {
  background-color: rgb(67 56 202);
}

.btn-primary-sm:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.5);
}

.btn-outline-sm {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border: 1px solid rgb(209 213 219);
  color: rgb(55 65 81);
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: background-color 0.2s;
}

.btn-outline-sm:hover {
  background-color: rgb(249 250 251);
}

.btn-outline-sm:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.5);
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  background-color: rgb(79 70 229);
  color: white;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: rgb(67 56 202);
}

.btn-primary:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.5);
}

/* Responsive Container */
.container-responsive {
  max-width: 80rem;
  margin: 0 auto;
}

/* Focus Styles for Accessibility */
.mobile-nav-link:focus,
.nav-link:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.5);
  border-radius: 0.25rem;
}
</style>
