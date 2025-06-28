import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  // State
  const isDarkMode = ref(false)
  const isSystemPreference = ref(true)

  // Getters
  const currentTheme = computed(() => {
    if (isSystemPreference.value) {
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }
    return isDarkMode.value ? 'dark' : 'light'
  })

  const themeIcon = computed(() => {
    switch (currentTheme.value) {
      case 'dark':
        return 'moon'
      case 'light':
        return 'sun'
      default:
        return 'computer'
    }
  })

  // Actions
  const initializeTheme = () => {
    // Check localStorage first
    const savedTheme = localStorage.getItem('vidadu-theme')
    const savedSystemPref = localStorage.getItem('vidadu-theme-system')
    
    if (savedSystemPref !== null) {
      isSystemPreference.value = JSON.parse(savedSystemPref)
    }

    if (savedTheme && !isSystemPreference.value) {
      isDarkMode.value = savedTheme === 'dark'
    } else {
      // Use system preference
      isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }

    applyTheme()
    
    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)')
      .addEventListener('change', handleSystemThemeChange)
  }

  const toggleTheme = () => {
    if (isSystemPreference.value) {
      // First toggle switches to manual mode
      isSystemPreference.value = false
      isDarkMode.value = !window.matchMedia('(prefers-color-scheme: dark)').matches
    } else {
      // Subsequent toggles switch between light/dark
      isDarkMode.value = !isDarkMode.value
    }
    
    saveThemePreference()
    applyTheme()
  }

  const setTheme = (theme) => {
    isSystemPreference.value = false
    isDarkMode.value = theme === 'dark'
    saveThemePreference()
    applyTheme()
  }

  const setSystemPreference = () => {
    isSystemPreference.value = true
    isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    saveThemePreference()
    applyTheme()
  }

  const applyTheme = () => {
    const root = document.documentElement
    const body = document.body
    
    if (currentTheme.value === 'dark') {
      root.classList.add('dark')
      body.classList.add('dark')
    } else {
      root.classList.remove('dark')
      body.classList.remove('dark')
    }

    // Update meta theme-color for mobile browsers
    updateThemeColorMeta()
  }

  const updateThemeColorMeta = () => {
    const themeColorMeta = document.querySelector('meta[name="theme-color"]')
    if (themeColorMeta) {
      themeColorMeta.content = currentTheme.value === 'dark' ? '#1f2937' : '#6366f1'
    }
  }

  const saveThemePreference = () => {
    localStorage.setItem('vidadu-theme', currentTheme.value)
    localStorage.setItem('vidadu-theme-system', JSON.stringify(isSystemPreference.value))
  }

  const handleSystemThemeChange = (e) => {
    if (isSystemPreference.value) {
      isDarkMode.value = e.matches
      applyTheme()
    }
  }

  // Watch for theme changes
  watch(currentTheme, () => {
    applyTheme()
  })

  return {
    // State
    isDarkMode,
    isSystemPreference,
    
    // Getters
    currentTheme,
    themeIcon,
    
    // Actions
    initializeTheme,
    toggleTheme,
    setTheme,
    setSystemPreference,
    applyTheme
  }
})
