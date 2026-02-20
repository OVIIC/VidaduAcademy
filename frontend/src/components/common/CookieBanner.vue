<template>
  <div class="cookie-banner-wrapper pointer-events-none relative z-[100]">
    <!-- Bottom Banner -->
    <transition
      name="slide-up"
      enter-active-class="transition ease-out duration-300 transform"
      enter-from-class="translate-y-full opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition ease-in duration-200 transform"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-full opacity-0"
    >
      <div v-show="isBannerVisible" class="fixed inset-x-0 bottom-0 pointer-events-none">
        <div class="pointer-events-auto bg-gray-900 shadow-xl pb-safe-bottom">
          <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              
              <div class="flex-1 pr-2">
                <h2 class="text-white font-semibold mb-1">Ceníme si vaše súkromie</h2>
                <p class="text-sm text-gray-300">
                  Na našej webstránke používame súbory cookie, aby sme vám poskytli najlepší zážitok z nakupovania a štúdia. Niektoré sú nevyhnutné, iné nám pomáhajú zlepšovať našu stránku a vaše zážitky.
                  <router-link to="/ochrana-osobnych-udajov" class="underline text-gray-100 hover:text-white transition-colors">
                    Viac informácií
                  </router-link>
                </p>
              </div>
              
              <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto shrink-0">
                <button
                  @click="openPreferences"
                  class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white hover:text-gray-100 bg-gray-800 hover:bg-gray-700 rounded-md border border-gray-700 transition-colors whitespace-nowrap"
                >
                  Nastavenia
                </button>
                
                <button
                  @click="handleRejectAll"
                  class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white hover:text-gray-100 bg-gray-800 hover:bg-gray-700 rounded-md border border-gray-700 transition-colors whitespace-nowrap"
                >
                  Len nevyhnutné
                </button>
                
                <button
                  @click="handleAcceptAll"
                  class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md shadow-sm transition-colors whitespace-nowrap"
                >
                  Prijať všetky
                </button>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </transition>
    
    <div class="pointer-events-auto">
      <CookiePreferencesModal
        :is-open="isPreferencesOpen"
        :initial-preferences="consentState"
        @close="closePreferences"
        @save="handleSavePreferences"
      />
    </div>
  </div>
</template>

<script setup>
import { useCookieConsent } from '@/composables/useCookieConsent'
import CookiePreferencesModal from './CookiePreferencesModal.vue'

const { 
  isBannerVisible, 
  consentState, 
  acceptAll, 
  rejectAll, 
  saveConsent,
  isPreferencesOpen,
  openPreferences,
  closePreferences
} = useCookieConsent()

const handleRejectAll = () => {
  rejectAll()
}

const handleAcceptAll = () => {
  acceptAll()
}

const handleSavePreferences = (preferences) => {
  saveConsent(preferences)
  closePreferences()
}
</script>
