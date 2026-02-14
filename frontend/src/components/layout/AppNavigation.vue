<template>
  <!-- Scroll detector area -->
  <div 
    ref="scrollDetector"
    class="fixed top-0 left-0 right-0 h-20 z-40 pointer-events-none"
  ></div>
  
  <nav 
    ref="navigation"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-in-out px-4 pt-4"
    :class="{ 
      'nav-hidden': isNavHidden, 
      'nav-visible': !isNavHidden
    }"
    role="navigation"
    aria-label="Hlavná navigácia"
  >
    <div 
      class="mx-auto max-w-5xl transition-all duration-300"
      :class="[
        isScrolled ? 'w-full' : 'w-full'
      ]"
    >
      <div 
        class="bg-dark-950/80 backdrop-blur-xl border border-white/10 shadow-2xl rounded-full px-6 py-3 flex items-center justify-between transition-all duration-300 relative overflow-hidden group"
      >


        <!-- Logo -->
        <router-link 
          to="/" 
          class="flex items-center space-x-2 text-xl font-bold text-white hover:text-primary-400 transition-colors z-10 focus:outline-none"
          aria-label="VidaduAcademy domov"
        >
          <img src="/images/video-academy-logo.png" alt="Vidadu Academy" class="h-8 w-auto object-contain" />
        </router-link>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center absolute left-1/2 -translate-x-1/2">
          <ul class="flex items-center space-x-1 bg-white/5 rounded-full px-2 py-1 border border-white/5">
            <li>
              <router-link 
                to="/" 
                class="nav-pill"
                :class="{ 'nav-pill-active': $route.name === 'Home' }"
              >
                Domov
              </router-link>
            </li>
            <li>
              <router-link 
                to="/courses" 
                class="nav-pill"
                :class="{ 'nav-pill-active': $route.name === 'Courses' }"
              >
                Kurzy
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Auth Section - Desktop -->
        <div class="hidden md:flex items-center space-x-3 z-10">
          <template v-if="authStore.isAuthenticated">
            <router-link 
              to="/dashboard" 
              class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors focus:outline-none"
            >
              <div class="w-8 h-8 rounded-full bg-dark-800 border border-dark-700 flex items-center justify-center">
                 <UserIcon class="w-4 h-4" />
              </div>
            </router-link>
            <button 
              @click="handleLogout"
              class="text-sm font-medium text-gray-400 hover:text-red-400 transition-colors focus:outline-none"
            >
              Odhlásiť
            </button>
          </template>
          <template v-else>
            <router-link 
              to="/login" 
              class="text-sm font-medium text-gray-300 hover:text-white transition-colors px-3 py-2 focus:outline-none"
            >
              Prihlásiť
            </router-link>
            <router-link 
              to="/register" 
              class="btn-glow text-sm px-4 py-2 rounded-full bg-[rgb(237,111,85)] text-white font-medium hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-300 flex items-center gap-2 focus:outline-none"
            >
              <span>Registrácia</span>
              <ArrowRightIcon class="w-4 h-4" />
            </router-link>
          </template>
        </div>

        <!-- Mobile Menu Button -->
        <div class="md:hidden flex items-center gap-4 z-10">
          <button 
            @click="toggleMobileMenu"
            class="relative w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/5 transition-colors focus:outline-none"
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
        class="md:hidden absolute top-full left-4 right-4 mt-2 bg-dark-900/95 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-2xl origin-top"
      >
        <div class="p-4 space-y-2">
          <!-- Main Navigation -->
          <router-link 
            to="/" 
            @click="closeMobileMenu"
            class="mobile-nav-link focus:outline-none"
            :class="{ 'mobile-nav-link-active': $route.name === 'Home' }"
          >
            <HomeIcon class="w-5 h-5" />
            <span>Domov</span>
          </router-link>
          
          <router-link 
            to="/courses" 
            @click="closeMobileMenu"
            class="mobile-nav-link focus:outline-none"
            :class="{ 'mobile-nav-link-active': $route.name === 'Courses' }"
          >
            <AcademicCapIcon class="w-5 h-5" />
            <span>Kurzy</span>
          </router-link>

          <!-- Divider -->
          <div class="border-t border-white/10 my-4"></div>

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
            
            <button 
              @click="handleLogout"
              class="mobile-nav-link w-full text-red-400 hover:bg-red-900/20 hover:text-red-300 focus:outline-none"
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
              class="flex items-center justify-center gap-2 w-full py-3 mt-4 bg-[rgb(237,111,85)] rounded-xl font-medium text-white shadow-lg active:scale-95 transition-transform focus:outline-none"
            >
              <span>Zaregistrovať sa</span>
              <ArrowRightIcon class="w-4 h-4" />
            </router-link>
          </template>
        </div>
      </div>
    </transition>
  </nav>

  <!-- Mobile Menu Overlay -->
  <transition name="overlay">
    <div 
      v-if="isMobileMenuOpen"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden"
      @click="closeMobileMenu"
      aria-hidden="true"
    ></div>
  </transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  HomeIcon,
  AcademicCapIcon,
  UserIcon,
  ArrowRightOnRectangleIcon,
  ArrowRightIcon
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
  el.style.opacity = '0'
  el.style.transform = 'translateY(-10px) scale(0.95)'
  // Force reflow
  el.offsetHeight
  
  el.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)'
  el.style.opacity = '1'
  el.style.transform = 'translateY(0) scale(1)'
}

const onMobileMenuLeave = (el) => {
  el.style.transition = 'all 0.2s cubic-bezier(0.16, 1, 0.3, 1)'
  el.style.opacity = '0'
  el.style.transform = 'translateY(-10px) scale(0.95)'
}

// Handle escape key
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
  transform: translateY(-150%);
  pointer-events: none;
}

/* Nav Pills */
.nav-pill {
  @apply text-gray-400;
  display: inline-block;
  padding: 0.5rem 1.25rem;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 9999px;
  transition: all 0.2s ease-in-out;
  @apply focus:outline-none;
}

.nav-pill:hover {
  @apply text-white bg-white/5;
}

.nav-pill-active {
  @apply text-white bg-white/10 shadow-inner;
  font-weight: 600;
}

/* Mobile Nav Links */
.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  @apply text-gray-300;
  font-weight: 500;
  border-radius: 0.75rem;
  transition: all 0.2s;
  @apply focus:outline-none;
}

.mobile-nav-link:hover {
  @apply text-white bg-white/5;
}

.mobile-nav-link-active {
  @apply text-primary-400 bg-primary-500/10;
  font-weight: 600;
}

/* Hamburger Menu Animation */
.hamburger-icon {
  position: relative;
  width: 1.25rem;
  height: 1.25rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.hamburger-line {
  display: block;
  width: 1.25rem;
  height: 2px;
  @apply bg-white rounded-full;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.hamburger-line:nth-child(1) {
  margin-bottom: 4px;
}

.hamburger-line:nth-child(2) {
  margin-bottom: 4px;
}

.hamburger-line-active-1 {
  transform: rotate(45deg) translateY(4px) translateX(4px);
  width: 1.4rem;
}

.hamburger-line-active-2 {
  opacity: 0;
  transform: translateX(-10px);
}

.hamburger-line-active-3 {
  transform: rotate(-45deg) translateY(-4px) translateX(4px);
  width: 1.4rem;
}

/* Overlay Transitions */
.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.3s ease;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}

.btn-glow {
  position: relative;
  overflow: hidden;
}

.btn-glow::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
  transform: translateX(-100%);
  transition: transform 0.6s;
}

.btn-glow:hover::after {
  transform: translateX(100%);
}
</style>
