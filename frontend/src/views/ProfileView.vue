<template>
  <div class="min-h-screen bg-black text-white py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Profil používateľa</h1>
        <p class="mt-2 text-gray-300">Spravujte svoje osobné údaje a nastavenia</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
          <nav class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-4">
            <ul class="space-y-2">
              <li>
                <button
                  @click="activeTab = 'personal'"
                  :class="[
                    'w-full text-left px-3 py-2 rounded-xl text-sm font-medium transition duration-200',
                    activeTab === 'personal' 
                      ? 'bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 text-primary-400 border border-primary-500/30' 
                      : 'text-gray-300 hover:bg-gray-800/50'
                  ]"
                >
                  <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                  Osobné údaje
                </button>
              </li>
              <li>
                <button
                  @click="activeTab = 'security'"
                  :class="[
                    'w-full text-left px-3 py-2 rounded-xl text-sm font-medium transition duration-200',
                    activeTab === 'security' 
                      ? 'bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 text-primary-400 border border-primary-500/30' 
                      : 'text-gray-300 hover:bg-gray-800/50'
                  ]"
                >
                  <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2z" clip-rule="evenodd" />
                  </svg>
                  Zabezpečenie
                </button>
              </li>
              <li>
                <button
                  @click="activeTab = 'notifications'"
                  :class="[
                    'w-full text-left px-3 py-2 rounded-xl text-sm font-medium transition duration-200',
                    activeTab === 'notifications' 
                      ? 'bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 text-primary-400 border border-primary-500/30' 
                      : 'text-gray-300 hover:bg-gray-800/50'
                  ]"
                >
                  <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                  </svg>
                  Notifikácie
                </button>
              </li>
              <li>
                <button
                  @click="activeTab = 'certificates'"
                  :class="[
                    'w-full text-left px-3 py-2 rounded-xl text-sm font-medium transition duration-200',
                    activeTab === 'certificates' 
                      ? 'bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 text-primary-400 border border-primary-500/30' 
                      : 'text-gray-300 hover:bg-gray-800/50'
                  ]"
                >
                  <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a2 2 0 002 2h4a2 2 0 002-2V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />
                  </svg>
                  Certifikáty
                </button>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Content Area -->
        <div class="lg:col-span-3">
          <!-- Personal Info Tab -->
          <div v-if="activeTab === 'personal'" class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Osobné údaje</h2>
            
            <form @submit.prevent="updateProfile" class="space-y-6">
              <!-- Profile Avatar -->
              <div class="flex items-center space-x-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary-500/20 via-secondary-500/20 to-primary-500/20 backdrop-blur-xl border border-primary-500/30 flex items-center justify-center text-primary-400 text-xl font-bold">
                  {{ getInitials(form.name) }}
                </div>
                <div>
                  <button
                    type="button"
                    class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition duration-200"
                  >
                    Zmeniť avatar
                  </button>
                  <p class="text-sm text-gray-400 mt-2">JPG, GIF alebo PNG. Max. veľkosť 2MB.</p>
                </div>
              </div>

              <!-- Basic Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-200 mb-2">
                    Meno a priezvisko *
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-600 bg-gray-800/50 text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent backdrop-blur-sm"
                    placeholder="Zadajte svoje meno"
                  />
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-200 mb-2">
                    Emailová adresa *
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-3 py-2 border border-gray-600 bg-gray-800/50 text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent backdrop-blur-sm"
                    placeholder="Zadajte svoju emailovú adresu"
                  />
                </div>

                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-200 mb-2">
                    Telefónne číslo
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-600 bg-gray-800/50 text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent backdrop-blur-sm"
                    placeholder="Zadajte svoje telefónne číslo"
                  />
                </div>

                <div>
                  <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Lokalita
                  </label>
                  <input
                    id="location"
                    v-model="form.location"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="Mesto, krajina"
                  />
                </div>
              </div>

              <div>
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                  O mne
                </label>
                <textarea
                  id="bio"
                  v-model="form.bio"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="Povedzte nám o sebe a svojich YouTube cieľoch..."
                ></textarea>
              </div>

              <!-- YouTube Information -->
              <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informácie o YouTube kanáli</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="youtube_channel" class="block text-sm font-medium text-gray-700 mb-2">
                      URL YouTube kanála
                    </label>
                    <input
                      id="youtube_channel"
                      v-model="form.youtube_channel"
                      type="url"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                      placeholder="https://youtube.com/@vaškanál"
                    />
                  </div>

                  <div>
                    <label for="subscriber_count" class="block text-sm font-medium text-gray-700 mb-2">
                      Súčasný počet odberateľov
                    </label>
                    <select
                      id="subscriber_count"
                      v-model="form.subscriber_count"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                      <option value="">Vyberte rozsah</option>
                      <option value="0-100">0 - 100</option>
                      <option value="100-1000">100 - 1 000</option>
                      <option value="1000-10000">1 000 - 10 000</option>
                      <option value="10000-100000">10 000 - 100 000</option>
                      <option value="100000+">100 000+</option>
                    </select>
                  </div>

                  <div>
                    <label for="content_niche" class="block text-sm font-medium text-gray-700 mb-2">
                      Oblasť obsahu
                    </label>
                    <select
                      id="content_niche"
                      v-model="form.content_niche"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                      <option value="">Vyberte oblasť</option>
                      <option value="gaming">Gaming</option>
                      <option value="education">Vzdelávanie</option>
                      <option value="entertainment">Zábava</option>
                      <option value="lifestyle">Životný štýl</option>
                      <option value="technology">Technológie</option>
                      <option value="beauty">Krása & Móda</option>
                      <option value="cooking">Varenie & Jedlo</option>
                      <option value="travel">Cestovanie</option>
                      <option value="fitness">Fitness & Zdravie</option>
                      <option value="music">Hudba</option>
                      <option value="other">Iné</option>
                    </select>
                  </div>

                  <div>
                    <label for="goals" class="block text-sm font-medium text-gray-700 mb-2">
                      Hlavný cieľ
                    </label>
                    <select
                      id="goals"
                      v-model="form.goals"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                      <option value="">Vyberte cieľ</option>
                      <option value="monetization">Monetizácia</option>
                      <option value="growth">Rast odberateľov</option>
                      <option value="engagement">Zvýšenie zapojenia</option>
                      <option value="content">Zlepšenie kvality obsahu</option>
                      <option value="analytics">Pochopenie analytiky</option>
                      <option value="branding">Budovanie značky</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="border-t border-gray-200 pt-6 flex justify-between">
                <button
                  type="button"
                  @click="resetForm"
                  class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200"
                >
                  Resetovať zmeny
                </button>
                <button
                  type="submit"
                  :disabled="loading"
                  class="px-6 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-lg transition duration-200"
                >
                  {{ loading ? 'Ukladá sa...' : 'Uložiť zmeny' }}
                </button>
              </div>
            </form>
          </div>

          <!-- Security Tab -->
          <div v-else-if="activeTab === 'security'" class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Zabezpečenie účtu</h2>
            
            <div class="space-y-6">
              <!-- Change Password -->
              <div class="border border-gray-600/50 bg-gray-800/30 rounded-xl p-4">
                <div class="flex items-center justify-between mb-4">
                  <div>
                    <h3 class="text-lg font-medium text-white">Zmena hesla</h3>
                    <p class="text-sm text-gray-300">Aktualizujte heslo svojho účtu</p>
                  </div>
                  <button
                    @click="showPasswordForm = !showPasswordForm"
                    class="text-primary-400 hover:text-primary-300 text-sm font-medium"
                  >
                    {{ showPasswordForm ? 'Zrušiť' : 'Zmeniť heslo' }}
                  </button>
                </div>

                <form v-if="showPasswordForm" @submit.prevent="updatePassword" class="space-y-4">
                  <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-200 mb-2">
                      Súčasné heslo
                    </label>
                    <input
                      id="current_password"
                      v-model="passwordForm.current_password"
                      type="password"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                      Nové heslo
                    </label>
                    <input
                      id="new_password"
                      v-model="passwordForm.new_password"
                      type="password"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                      Potvrdiť nové heslo
                    </label>
                    <input
                      id="confirm_password"
                      v-model="passwordForm.confirm_password"
                      type="password"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>

                  <div class="flex space-x-4">
                    <button
                      type="button"
                      @click="showPasswordForm = false"
                      class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200"
                    >
                      Zrušiť
                    </button>
                    <button
                      type="submit"
                      :disabled="passwordLoading"
                      class="px-6 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-lg transition duration-200"
                    >
                      {{ passwordLoading ? 'Aktualizuje sa...' : 'Aktualizovať heslo' }}
                    </button>
                  </div>
                </form>
              </div>

              <!-- Account Actions -->
              <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">Stiahnuť údaje</h4>
                    <p class="text-sm text-gray-600">Stiahnite kópiu údajov svojho účtu</p>
                  </div>
                  <button 
                    @click="exportData"
                    class="text-primary-600 hover:text-primary-800 text-sm font-medium"
                  >
                    Stiahnuť
                  </button>
                </div>

                <div class="flex items-center justify-between p-4 border border-red-200 rounded-lg bg-red-50">
                  <div>
                    <h4 class="text-sm font-medium text-red-900">Zmazať účet</h4>
                    <p class="text-sm text-red-600">Natrvalo odstrániť účet a všetky údaje</p>
                  </div>
                  <button 
                    @click="showDeleteConfirm = true"
                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                  >
                    Zmazať účet
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Notifications Tab -->
          <div v-else-if="activeTab === 'notifications'" class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Nastavenia notifikácií</h2>
            
            <form @submit.prevent="updateNotifications" class="space-y-6">
              <div class="space-y-4">
                <div class="flex items-center">
                  <input
                    id="email_notifications"
                    v-model="form.email_notifications"
                    type="checkbox"
                    class="h-4 w-4 text-primary-500 focus:ring-primary-500 border-gray-600 bg-gray-800 rounded"
                  />
                  <label for="email_notifications" class="ml-3 text-sm text-gray-200">
                    Emailové notifikácie o nových kurzoch a aktualizáciách
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="course_reminders"
                    v-model="form.course_reminders"
                    type="checkbox"
                    class="h-4 w-4 text-primary-500 focus:ring-primary-500 border-gray-600 bg-gray-800 rounded"
                  />
                  <label for="course_reminders" class="ml-3 text-sm text-gray-700">
                    Pripomienky pokroku v kurzoch
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="marketing_emails"
                    v-model="form.marketing_emails"
                    type="checkbox"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                  />
                  <label for="marketing_emails" class="ml-3 text-sm text-gray-700">
                    Marketingové emaily a špeciálne ponuky
                  </label>
                </div>
              </div>

              <div class="border-t border-gray-200 pt-6">
                <button
                  type="submit"
                  :disabled="loading"
                  class="px-6 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-lg transition duration-200"
                >
                  {{ loading ? 'Ukladá sa...' : 'Uložiť nastavenia' }}
                </button>
              </div>
            </form>
          </div>

          <!-- Certificates Tab -->
          <div v-else-if="activeTab === 'certificates'" class="bg-gradient-to-br from-gray-900/30 via-secondary-900/20 to-gray-900/40 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Moje certifikáty</h2>
            
            <div v-if="certificates.length > 0" class="space-y-4">
              <div
                v-for="certificate in certificates"
                :key="certificate.id"
                class="border border-gray-600/50 bg-gray-800/30 rounded-xl p-4 hover:bg-gray-800/50 transition duration-200"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-medium text-white">{{ certificate.course_title }}</h3>
                    <p class="text-sm text-gray-300">
                      Dokončené {{ formatDate(certificate.completed_at) }}
                    </p>
                  </div>
                  <button
                    @click="downloadCertificate(certificate.id)"
                    class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition duration-200"
                  >
                    Stiahnuť
                  </button>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-white">Žiadne certifikáty</h3>
              <p class="mt-1 text-sm text-gray-300">Dokončite kurz, aby ste získali svoj prvý certifikát.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Account Confirmation Modal -->
      <div v-if="showDeleteConfirm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
              <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Zmazať účet</h3>
            <div class="mt-2 px-7 py-3">
              <p class="text-sm text-gray-500">
                Ste si istí, že chcete zmazať svoj účet? Táto akcia je nevratná a všetky vaše údaje budú natrvalo odstránené.
              </p>
            </div>
            <div class="items-center px-4 py-3">
              <button
                @click="showDeleteConfirm = false"
                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-gray-600"
              >
                Zrušiť
              </button>
              <button
                @click="deleteAccount"
                class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-32 hover:bg-red-600"
              >
                Zmazať účet
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { api } from '@/services/api'
import { useToast } from 'vue-toastification'

export default {
  name: 'ProfileView',
  setup() {
    const authStore = useAuthStore()
    const toast = useToast()
    const activeTab = ref('personal')
    const loading = ref(false)
    const passwordLoading = ref(false)
    const showPasswordForm = ref(false)
    const showDeleteConfirm = ref(false)
    const certificates = ref([])

    const form = reactive({
      name: '',
      email: '',
      phone: '',
      location: '',
      bio: '',
      youtube_channel: '',
      subscriber_count: '',
      content_niche: '',
      goals: '',
      email_notifications: true,
      course_reminders: true,
      marketing_emails: false
    })

    const passwordForm = reactive({
      current_password: '',
      new_password: '',
      confirm_password: ''
    })

    const getInitials = (name) => {
      if (!name) return 'U'
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2)
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'Neznámy dátum'
      const date = new Date(dateString)
      return date.toLocaleDateString('sk-SK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const loadUserData = async () => {
      try {
        const response = await api.get('/user/profile')
        const user = response.data.user
        
        Object.keys(form).forEach(key => {
          if (user[key] !== undefined) {
            form[key] = user[key]
          }
        })
        
        // Update auth store with fresh data
        authStore.setUser(user)
      } catch (error) {
        console.error('Error loading user data:', error)
        // Fallback to auth store data
        const user = authStore.user
        if (user) {
          Object.keys(form).forEach(key => {
            if (user[key] !== undefined) {
              form[key] = user[key]
            }
          })
        }
      }
    }

    const loadCertificates = async () => {
      try {
        const response = await api.get('/user/certificates')
        certificates.value = response.data.certificates
      } catch (error) {
        console.error('Error loading certificates:', error)
        certificates.value = []
      }
    }

    const updateProfile = async () => {
      loading.value = true
      try {
        const response = await api.put('/user/profile', form)
        authStore.setUser(response.data.user)
        
        toast.success('Profil bol úspešne aktualizovaný!')
      } catch (error) {
        console.error('Error updating profile:', error)
        
        toast.error('Chyba pri aktualizácii profilu. Skúste to znovu.')
      } finally {
        loading.value = false
      }
    }

    const updateNotifications = async () => {
      await updateProfile()
    }

    const updatePassword = async () => {
      if (passwordForm.new_password !== passwordForm.confirm_password) {
        toast.error('Nové heslá sa nezhodujú!')
        return
      }

      passwordLoading.value = true
      try {
        await api.put('/user/password', {
          current_password: passwordForm.current_password,
          new_password: passwordForm.new_password,
          new_password_confirmation: passwordForm.confirm_password
        })
        
        // Reset form
        Object.keys(passwordForm).forEach(key => {
          passwordForm[key] = ''
        })
        showPasswordForm.value = false
        
        toast.success('Heslo bolo úspešne aktualizované!')
      } catch (error) {
        console.error('Error updating password:', error)
        
        let errorText = 'Chyba pri aktualizácii hesla. Skontrolujte súčasné heslo a skúste to znovu.'
        if (error.response?.data?.message) {
          errorText = error.response.data.message
        }
        
        toast.error(errorText)
      } finally {
        passwordLoading.value = false
      }
    }

    const downloadCertificate = async (certificateId) => {
      try {
        const response = await api.get(`/user/certificate/${certificateId}/download`)
        console.log('Certificate download:', response.data)
        
        toast.info('Funkcia sťahovania certifikátov bude čoskoro implementovaná.')
      } catch (error) {
        console.error('Error downloading certificate:', error)
      }
    }

    const exportData = async () => {
      try {
        const response = await api.get('/user/export-data')
        
        // Create and download JSON file
        const dataStr = JSON.stringify(response.data.data, null, 2)
        const dataBlob = new Blob([dataStr], { type: 'application/json' })
        const url = URL.createObjectURL(dataBlob)
        const link = document.createElement('a')
        link.href = url
        link.download = `vidadu-academy-data-${new Date().toISOString().split('T')[0]}.json`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        URL.revokeObjectURL(url)
        
        toast.success('Údaje boli úspešne stiahnuté!')
      } catch (error) {
        console.error('Error exporting data:', error)
      }
    }

    const deleteAccount = async () => {
      try {
        await api.delete('/user/account', {
          password: 'dummy', // This would need proper implementation
          confirmation: 'DELETE_ACCOUNT'
        })
        
        // Logout and redirect
        authStore.logout()
        this.$router.push('/')
      } catch (error) {
        console.error('Error deleting account:', error)
      }
    }

    const resetForm = () => {
      loadUserData()
    }

    onMounted(() => {
      loadUserData()
      loadCertificates()
    })

    return {
      activeTab,
      form,
      passwordForm,
      loading,
      passwordLoading,
      showPasswordForm,
      showDeleteConfirm,
      certificates,
      getInitials,
      formatDate,
      updateProfile,
      updateNotifications,
      updatePassword,
      downloadCertificate,
      exportData,
      deleteAccount,
      resetForm
    }
  }
}
</script>
