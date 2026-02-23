<template>
  <footer class="relative bg-black text-white overflow-hidden">
    <!-- Frosted glass gradient background -->
    <div class="absolute inset-0 bg-gradient-to-br from-secondary/20 via-black/80 to-black backdrop-blur-xl"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Company Info -->
        <div class="col-span-1 md:col-span-2">
          <div class="flex items-center space-x-2 mb-4">
            <img src="/images/video-academy-logo.png" alt="Vidadu Academy" class="h-8 w-auto object-contain" />
          </div>
          <p class="text-gray-300 mb-6 max-w-md leading-relaxed">
            Pomáhame tvorcom pri budovaní úspešných YouTube kanálov prostredníctvom kurzov vedených expertmi 
            a praktických stratégií.
          </p>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-primary transition-all duration-300 hover:scale-110">
              <YoutubeIcon class="h-6 w-6" />
            </a>
            <a href="#" class="text-gray-400 hover:text-primary transition-all duration-300 hover:scale-110">
              <TwitterIcon class="h-6 w-6" />
            </a>
            <a href="#" class="text-gray-400 hover:text-primary transition-all duration-300 hover:scale-110">
              <InstagramIcon class="h-6 w-6" />
            </a>
            <a href="#" class="text-gray-400 hover:text-primary transition-all duration-300 hover:scale-110">
              <DiscordIcon class="h-6 w-6" />
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-lg font-semibold mb-4 text-white">Rýchle odkazy</h3>
          <ul class="space-y-2">
            <li>
              <router-link to="/courses" class="text-gray-300 hover:text-primary transition-all duration-300 hover:translate-x-1">
                Všetky kurzy
              </router-link>
            </li>

          </ul>
        </div>

        <!-- Support -->
        <div>
          <h3 class="text-lg font-semibold mb-4 text-white">Podpora</h3>
          <ul class="space-y-2">

            <li>
              <router-link to="/kontakt" class="text-gray-300 hover:text-primary transition-all duration-300 hover:translate-x-1">
                Kontaktujte nás
              </router-link>
            </li>

            <li>
              <router-link to="/ochrana-osobnych-udajov" class="text-gray-300 hover:text-primary transition-all duration-300 hover:translate-x-1">
                Zásady ochrany súkromia
              </router-link>
            </li>
            <li>
              <router-link to="/podmienky-sluzby" class="text-gray-300 hover:text-primary transition-all duration-300 hover:translate-x-1">
                Podmienky služby
              </router-link>
            </li>
          </ul>
        </div>
      </div>

      <!-- Newsletter Signup -->
      <div class="border-t border-gray-700/50 mt-8 pt-8">
        <div class="max-w-md mx-auto text-center">
          <h3 class="text-lg font-semibold mb-2 text-white">Zostaňte informovaní</h3>
          <p class="text-gray-300 mb-4">Získajte najnovšie tipy na rast YouTube a aktualizácie kurzov</p>
          <form class="flex rounded-xl overflow-hidden backdrop-blur-sm bg-white/5 border border-white/10">
            <input
              type="email"
              placeholder="Zadajte váš email"
              class="flex-1 bg-transparent border-none px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/50"
            >
            <button
              type="submit"
              class="bg-primary hover:bg-primary/90 px-6 py-3 font-medium transition-all duration-300 hover:shadow-lg hover:shadow-primary/25"
            >
              Odoberať
            </button>
          </form>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="border-t border-gray-700/50 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
        <p class="text-gray-300 text-sm">
          © {{ currentYear }} VidaduAcademy. Všetky práva vyhradené.
        </p>
        <div class="flex space-x-6 mt-4 md:mt-0">
          <router-link to="/ochrana-osobnych-udajov" class="text-gray-300 hover:text-primary text-sm transition-all duration-300">
            Zásady ochrany súkromia
          </router-link>
          <router-link to="/podmienky-sluzby" class="text-gray-300 hover:text-primary text-sm transition-all duration-300">
            Podmienky služby
          </router-link>
          <a href="#" class="text-gray-300 hover:text-primary text-sm transition-all duration-300">
            Zásady cookies
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'

// Social Media Icons (simplified SVG icons)
const YoutubeIcon = {
  template: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>`
}

const TwitterIcon = {
  template: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>`
}

const InstagramIcon = {
  template: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.596-3.205-1.533-.757-.936-1.179-2.18-1.179-3.455s.422-2.519 1.179-3.455c.757-.937 1.908-1.533 3.205-1.533s2.448.596 3.205 1.533c.757.936 1.179 2.18 1.179 3.455s-.422 2.519-1.179 3.455c-.757.937-1.908 1.533-3.205 1.533z"/></svg>`
}

const DiscordIcon = {
  template: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>`
}

const currentYear = computed(() => new Date().getFullYear())
</script>
