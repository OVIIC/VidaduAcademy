<template>
  <!-- Scroll detector area -->
  <div 
    ref="scrollDetector"
    class="fixed top-0 left-0 right-0 h-20 z-40 pointer-events-none"
    @mouseenter="showNavigation"
  ></div>
  
  <nav 
    ref="navigation"
    class="fixed top-0 left-0 right-0 z-50 safe-area-inset-top transition-all duration-300 ease-in-out"
    :class="{ 
      'nav-hidden': isNavHidden, 
      'nav-visible': !isNavHidden,
      'bg-dark-950/90 backdrop-blur-md shadow-lg': isScrolled,
      'bg-gradient-to-b from-black/80 to-transparent': !isScrolled
    }"
    role="navigation"
    aria-label="Hlavná navigácia"
    @mouseenter="showNavigation"
  >
    <div class="container-responsive">
      <div class="flex items-center justify-between h-16 px-4">
        <!-- Logo -->
        <router-link 
          to="/" 
          class="flex items-center space-x-2 text-xl font-bold text-white hover:text-primary-500 transition-colors"
          aria-label="VidaduAcademy domov"
        >
          <div class="w-8 h-8 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">V</span>
          </div>
          <span class="hidden xs:inline">VidaduAcademy</span>
        </router-link>

        <!-- Desktop Navigation -->
        <ul class="hidden md:flex items-center space-x-8">
          <li>
            <router-link 
              to="/" 
              class="nav-link"
              :class="{ 'nav-link-active': $route.name === 'Home' }"
            >
              Domov
            </router-link>
          </li>
          <li>
            <router-link 
              to="/courses" 
              class="nav-link"
              :class="{ 'nav-link-active': $route.name === 'Courses' }"
            >
              Kurzy
            </router-link>
          </li>
        </ul>
        <!-- Auth Section - Desktop -->
        <ul class="hidden md:flex items-center space-x-4">
          <template v-if="authStore.isAuthenticated">
            <li>
              <router-link 
                to="/dashboard" 
                class="nav-link"
              >
                Môj účet
              </router-link>
            </li>
            <li>
              <button 
                @click="handleLogout"
                class="btn-outline-sm"
              >
                Odhlásiť
              </button>
            </li>
          </template>
          <template v-else>
            <li>
              <router-link 
                to="/login" 
                class="nav-link"
              >
                Prihlásiť
              </router-link>
            </li>
            <li>
              <router-link 
                to="/register" 
                class="btn-primary-sm"
              >
                Registrácia
              </router-link>
            </li>
          </template>
        </ul>

        <!-- Mobile Menu Button -->
        <button 
          @click="toggleMobileMenu"
          class="md:hidden relative w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
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
    >        <div 
          v-if="isMobileMenuOpen"
          id="mobile-menu"
          class="md:hidden bg-secondary-800 border-t border-secondary-700 shadow-lg transition-colors duration-200"
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

          <!-- Divider -->
          <div class="border-t border-gray-600 my-4"></div>

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
              class="mobile-nav-link text-red-400 hover:bg-red-900/20 hover:text-red-300"
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
import {
  HomeIcon,
  AcademicCapIcon,
  UserIcon,
  BookOpenIcon,
  ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()

// Navigation state
const navigation = ref(null)
const scrollDetector = ref(null)
const isNavHidden = ref(false)
const isScrolled = ref(false)
const lastScrollY = ref(0)
const scrollThreshold = 100

// Mobile menu state
const isMobileMenuOpen = ref(false)

// Navigation Methods
const showNavigation = () => {
  isNavHidden.value = false
}

const hideNavigation = () => {
  if (!isMobileMenuOpen.value) {
    isNavHidden.value = true
  }
}

const handleScroll = () => {
  const currentScrollY = window.scrollY
  
  // Show navigation if scrolling up or at top
  if (currentScrollY < lastScrollY.value || currentScrollY < scrollThreshold) {
    showNavigation()
  } 
  // Hide navigation if scrolling down and past threshold
  else if (currentScrollY > scrollThreshold && currentScrollY > lastScrollY.value) {
    hideNavigation()
  }
  
  isScrolled.value = currentScrollY > 20
  lastScrollY.value = currentScrollY
}

// Mobile menu methods
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
  window.addEventListener('scroll', handleScroll, { passive: true })
  lastScrollY.value = window.scrollY
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
  window.removeEventListener('scroll', handleScroll)
  document.body.style.overflow = '' // Cleanup
})

// Close menu on route change
router.afterEach(() => {
  closeMobileMenu()
})
</script>

<style scoped>
/* Navigation Fade Animation */
.nav-visible {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.nav-hidden {
  opacity: 0;
  transform: translateY(-100%);
  pointer-events: none;
}

/* Navigation gradient fade effect - Removed in favor of dynamic classes */
/* nav {
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.8) 40%, rgba(0, 0, 0, 0.6) 60%, rgba(0, 0, 0, 0.3) 80%, rgba(0, 0, 0, 0) 100%);
} */

/* Navigation Links */
.nav-link {
  @apply text-gray-200;
  font-weight: 500;
  transition: color 0.2s;
  padding: 0.5rem 0;
}

.nav-link:hover {
  @apply text-primary-500;
}

.nav-link-active {
  @apply text-primary-500;
  font-weight: 600;
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  @apply text-gray-200;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: all 0.2s;
}

.mobile-nav-link:hover {
  @apply text-primary-500;
  @apply bg-secondary-900/30;
}

.mobile-nav-link-active {
  @apply text-primary-500;
  @apply bg-secondary-900;
  font-weight: 600;
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
  @apply bg-white;
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
  @apply bg-primary-500;
  color: white;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: background-color 0.2s;
}

.btn-primary-sm:hover {
  @apply bg-primary-600;
}

.btn-primary-sm:focus {
  outline: none;
  @apply ring-2 ring-primary-500/50;
}

.btn-outline-sm {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  @apply border border-gray-600;
  @apply text-gray-200;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: background-color 0.2s;
}

.btn-outline-sm:hover {
  @apply bg-gray-700;
}

.btn-outline-sm:focus {
  outline: none;
  @apply ring-2 ring-primary-500/50;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  @apply bg-primary-500;
  color: white;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  @apply bg-primary-600;
}

.btn-primary:focus {
  outline: none;
  @apply ring-2 ring-primary-500/50;
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
