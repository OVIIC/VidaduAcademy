<template>
  <div class="theme-toggle-container">
    <!-- Desktop Theme Toggle - Only Dropdown Selector -->
    <div class="hidden md:flex items-center">
      <div class="relative" ref="dropdownContainer">
        <button
          @click.stop="toggleMenu"
          class="theme-dropdown-btn"
          :aria-expanded="isMenuOpen"
          :title="currentThemeLabel"
        >
          <SunIcon 
            v-if="!themeStore.isSystemPreference && themeStore.currentTheme === 'light'"
            class="w-4 h-4" 
          />
          <MoonIcon 
            v-else-if="!themeStore.isSystemPreference && themeStore.currentTheme === 'dark'"
            class="w-4 h-4" 
          />
          <ComputerDesktopIcon 
            v-else
            class="w-4 h-4" 
          />
          <span class="ml-2 text-sm font-medium">
            {{ currentThemeLabel }}
          </span>
          <ChevronDownIcon class="w-4 h-4 ml-2" />
        </button>
        
        <transition name="dropdown">
          <div 
            v-if="isMenuOpen"
            class="theme-dropdown"
            @click.stop
          >
            <button
              @click.stop="setLightTheme"
              class="theme-option"
              :class="{ 'theme-option-active': themeStore.currentTheme === 'light' && !themeStore.isSystemPreference }"
            >
              <SunIcon class="w-4 h-4" />
              <span>Svetlý</span>
            </button>
            
            <button
              @click.stop="setDarkTheme"
              class="theme-option"
              :class="{ 'theme-option-active': themeStore.currentTheme === 'dark' && !themeStore.isSystemPreference }"
            >
              <MoonIcon class="w-4 h-4" />
              <span>Tmavý</span>
            </button>
            
            <button
              @click.stop="setSystemTheme"
              class="theme-option"
              :class="{ 'theme-option-active': themeStore.isSystemPreference }"
            >
              <ComputerDesktopIcon class="w-4 h-4" />
              <span>Systémový</span>
            </button>
          </div>
        </transition>
      </div>
    </div>

    <!-- Mobile Theme Toggle (Simplified) - Never show standalone mobile toggle -->
    <!-- This mobile toggle is only used within navigation menu -->

    <!-- Theme Toggle for Navigation Menu -->
    <div v-if="inNavigation" class="mobile-nav-theme-toggle">
      <div class="flex items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
          Téma rozhrania
        </span>
        <div class="flex items-center space-x-2">
          <button
            @click="setLightTheme"
            class="theme-mini-btn"
            :class="{ 'theme-mini-btn-active': themeStore.currentTheme === 'light' && !themeStore.isSystemPreference }"
            title="Svetlá téma"
          >
            <SunIcon class="w-4 h-4" />
          </button>
          <button
            @click="setDarkTheme"
            class="theme-mini-btn"
            :class="{ 'theme-mini-btn-active': themeStore.currentTheme === 'dark' && !themeStore.isSystemPreference }"
            title="Tmavá téma"
          >
            <MoonIcon class="w-4 h-4" />
          </button>
          <button
            @click="setSystemTheme"
            class="theme-mini-btn"
            :class="{ 'theme-mini-btn-active': themeStore.isSystemPreference }"
            title="Systémová téma"
          >
            <ComputerDesktopIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import {
  SunIcon,
  MoonIcon,
  ComputerDesktopIcon,
  ChevronDownIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  showAdvanced: {
    type: Boolean,
    default: false
  },
  inNavigation: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  }
})

// Store
const themeStore = useThemeStore()

// State
const isMenuOpen = ref(false)
const dropdownContainer = ref(null)

// Computed
const themeToggleLabel = computed(() => {
  if (themeStore.isSystemPreference) {
    return 'Prepnúť na manuálnu tému'
  }
  return themeStore.currentTheme === 'dark' 
    ? 'Prepnúť na svetlú tému' 
    : 'Prepnúť na tmavú tému'
})

const currentThemeLabel = computed(() => {
  if (themeStore.isSystemPreference) {
    return 'Systémová'
  }
  return themeStore.currentTheme === 'dark' ? 'Tmavá' : 'Svetlá'
})

// Methods
const toggleTheme = () => {
  themeStore.toggleTheme()
}

const setLightTheme = () => {
  themeStore.setTheme('light')
  closeMenu()
}

const setDarkTheme = () => {
  themeStore.setTheme('dark')
  closeMenu()
}

const setSystemTheme = () => {
  themeStore.setSystemPreference()
  closeMenu()
}

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}

const handleClickOutside = (event) => {
  if (isMenuOpen.value && 
      dropdownContainer.value && 
      !dropdownContainer.value.contains(event.target)) {
    closeMenu()
  }
}

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Theme Toggle Button */
.theme-toggle-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.5rem;
  border: 1px solid rgb(209 213 219);
  background-color: white;
  color: rgb(55 65 81);
  transition: all 0.2s ease;
  cursor: pointer;
}

.theme-toggle-btn:hover {
  background-color: rgb(249 250 251);
  border-color: rgb(156 163 175);
}

.theme-toggle-btn:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.5);
}

/* Dark mode theme toggle */
.dark .theme-toggle-btn {
  border-color: rgb(75 85 99);
  background-color: rgb(31 41 55);
  color: rgb(209 213 219);
}

.dark .theme-toggle-btn:hover {
  background-color: rgb(55 65 81);
  border-color: rgb(107 114 128);
}

/* Mobile Toggle Button */
.theme-toggle-btn-mobile {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  color: rgb(55 65 81);
  border-radius: 0.5rem;
  transition: all 0.2s;
  width: 100%;
}

.theme-toggle-btn-mobile:hover {
  color: rgb(79 70 229);
  background-color: rgb(249 250 251);
}

/* Theme Dropdown Button */
.theme-dropdown-btn {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid rgb(209 213 219);
  background-color: white;
  color: rgb(55 65 81);
  font-size: 0.875rem;
  transition: all 0.2s ease;
  cursor: pointer;
  min-width: 120px;
}

.theme-dropdown-btn:hover {
  background-color: rgb(249 250 251);
  border-color: rgb(156 163 175);
}

.theme-dropdown-btn:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.5);
}

/* Dark mode dropdown button */
.dark .theme-dropdown-btn {
  border-color: rgb(75 85 99);
  background-color: rgb(31 41 55);
  color: rgb(209 213 219);
}

.dark .theme-dropdown-btn:hover {
  background-color: rgb(55 65 81);
  border-color: rgb(107 114 128);
}

/* Theme Menu */
.theme-menu-trigger {
  padding: 0.25rem;
  border-radius: 0.25rem;
  color: rgb(107 114 128);
  transition: color 0.2s;
}

.theme-menu-trigger:hover {
  color: rgb(55 65 81);
}

.theme-dropdown {
  position: absolute;
  right: 0;
  margin-top: 0.5rem;
  width: 12rem;
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  border: 1px solid rgb(229 231 235);
  padding: 0.25rem 0;
  z-index: 50;
  pointer-events: auto;
}

.theme-option {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  color: rgb(55 65 81);
  transition: all 0.2s;
  width: 100%;
  border: none;
  background: none;
  text-align: left;
}

.theme-option:hover {
  background-color: rgb(243 244 246);
}

.theme-option-active {
  background-color: rgb(238 242 255);
  color: rgb(79 70 229);
  font-weight: 500;
}

/* Mini Theme Buttons */
.theme-mini-btn {
  padding: 0.5rem;
  border-radius: 0.375rem;
  border: 1px solid rgb(209 213 219);
  background-color: white;
  color: rgb(55 65 81);
  transition: all 0.2s;
}

.theme-mini-btn:hover {
  background-color: rgb(249 250 251);
}

.theme-mini-btn-active {
  border-color: rgb(79 70 229);
  background-color: rgb(238 242 255);
  color: rgb(79 70 229);
}

/* Mobile Navigation Theme Toggle */
.mobile-nav-theme-toggle {
  margin-top: auto;
}

/* Dropdown Transitions */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.dropdown-enter-to,
.dropdown-leave-from {
  opacity: 1;
  transform: scale(1);
}

/* Dark mode styles */
.dark .theme-toggle-btn-mobile {
  color: rgb(209 213 219);
}

.dark .theme-toggle-btn-mobile:hover {
  color: rgb(129 140 248);
  background-color: rgb(55 65 81);
}

.dark .theme-dropdown {
  background-color: rgb(31 41 55);
  border-color: rgb(75 85 99);
}

.dark .theme-option {
  color: rgb(209 213 219);
}

.dark .theme-option:hover {
  background-color: rgb(55 65 81);
}

.dark .theme-option-active {
  background-color: rgb(30 27 75);
  color: rgb(129 140 248);
}

.dark .theme-mini-btn {
  border-color: rgb(75 85 99);
  background-color: rgb(31 41 55);
  color: rgb(209 213 219);
}

.dark .theme-mini-btn:hover {
  background-color: rgb(55 65 81);
}

.dark .theme-mini-btn-active {
  border-color: rgb(79 70 229);
  background-color: rgb(30 27 75);
  color: rgb(129 140 248);
}
</style>
