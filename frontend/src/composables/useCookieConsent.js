import { ref } from 'vue'

const CONSENT_KEY = 'vidadu_cookie_consent'

// Global state so it can be shared across multiple components if needed
const consentState = ref({
  essential: true, // Always true
  analytics: false,
  marketing: false,
  answered: false
})

const isBannerVisible = ref(false)
const isPreferencesOpen = ref(false)

// Initialize state from localStorage
const initConsent = () => {
  const savedConsent = localStorage.getItem(CONSENT_KEY)
  if (savedConsent) {
    try {
      const parsed = JSON.parse(savedConsent)
      consentState.value = { ...consentState.value, ...parsed }
      // If they haven't answered yet, show banner
      if (!consentState.value.answered) {
        isBannerVisible.value = true
      }
    } catch (e) {
      console.error('Failed to parse cookie consent', e)
      isBannerVisible.value = true
    }
  } else {
    isBannerVisible.value = true
  }
}

// Save to localStorage and potentially trigger scripts
const saveConsent = (newConsent) => {
  consentState.value = { 
    ...consentState.value, 
    ...newConsent,
    essential: true, // Force essential
    answered: true 
  }
  
  localStorage.setItem(CONSENT_KEY, JSON.stringify(consentState.value))
  isBannerVisible.value = false
  
  applyConsent(consentState.value)
}

const acceptAll = () => {
  saveConsent({
    analytics: true,
    marketing: true
  })
}

const rejectAll = () => {
  saveConsent({
    analytics: false,
    marketing: false
  })
}

const showBanner = () => {
  isBannerVisible.value = true
}

const openPreferences = () => {
  isPreferencesOpen.value = true
}

const closePreferences = () => {
  isPreferencesOpen.value = false
}

// Watcher to dynamically inject scripts based on consent
// In a real app, this is where you'd inject Google Analytics, Meta Pixel, etc.
const applyConsent = (state) => {
  if (state.analytics) {
    console.log('[CookieConsent] Analytics accepted. Injecting GA...')
    // injectGoogleAnalytics()
  } else {
    console.log('[CookieConsent] Analytics rejected. Ensure GA is disabled.')
  }
  
  if (state.marketing) {
    console.log('[CookieConsent] Marketing accepted. Injecting Meta Pixel...')
    // injectMetaPixel()
  } else {
    console.log('[CookieConsent] Marketing rejected. Ensure Pixel is disabled.')
  }
}

export function useCookieConsent() {
  return {
    consentState,
    isBannerVisible,
    isPreferencesOpen,
    initConsent,
    saveConsent,
    acceptAll,
    rejectAll,
    showBanner,
    openPreferences,
    closePreferences,
    applyConsent
  }
}
