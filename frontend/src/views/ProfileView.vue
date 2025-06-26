<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
        <p class="mt-2 text-gray-600">Manage your account information and preferences</p>
      </div>

      <!-- Profile Form -->
      <div class="bg-white shadow-sm rounded-lg">
        <form @submit.prevent="updateProfile" class="space-y-6 p-6">
          <!-- Profile Picture -->
          <div class="flex items-center space-x-6">
            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
              {{ getInitials(form.name) }}
            </div>
            <div>
              <h3 class="text-lg font-medium text-gray-900">Profile Picture</h3>
              <p class="text-sm text-gray-600">Update your profile picture to personalize your account</p>
              <button
                type="button"
                class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium"
              >
                Change Picture
              </button>
            </div>
          </div>

          <!-- Personal Information -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                  Full Name
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter your full name"
                />
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                  Email Address
                </label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter your email address"
                />
              </div>

              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                  Phone Number
                </label>
                <input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter your phone number"
                />
              </div>

              <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                  Location
                </label>
                <input
                  id="location"
                  v-model="form.location"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="City, Country"
                />
              </div>
            </div>

            <div class="mt-6">
              <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                Bio
              </label>
              <textarea
                id="bio"
                v-model="form.bio"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Tell us about yourself and your YouTube goals..."
              ></textarea>
            </div>
          </div>

          <!-- YouTube Information -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">YouTube Channel Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="youtube_channel" class="block text-sm font-medium text-gray-700 mb-2">
                  YouTube Channel URL
                </label>
                <input
                  id="youtube_channel"
                  v-model="form.youtube_channel"
                  type="url"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="https://youtube.com/@yourchannel"
                />
              </div>

              <div>
                <label for="subscriber_count" class="block text-sm font-medium text-gray-700 mb-2">
                  Current Subscriber Count
                </label>
                <select
                  id="subscriber_count"
                  v-model="form.subscriber_count"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Select range</option>
                  <option value="0-100">0 - 100</option>
                  <option value="100-1000">100 - 1,000</option>
                  <option value="1000-10000">1,000 - 10,000</option>
                  <option value="10000-100000">10,000 - 100,000</option>
                  <option value="100000+">100,000+</option>
                </select>
              </div>

              <div>
                <label for="content_niche" class="block text-sm font-medium text-gray-700 mb-2">
                  Content Niche
                </label>
                <select
                  id="content_niche"
                  v-model="form.content_niche"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Select niche</option>
                  <option value="gaming">Gaming</option>
                  <option value="education">Education</option>
                  <option value="entertainment">Entertainment</option>
                  <option value="lifestyle">Lifestyle</option>
                  <option value="technology">Technology</option>
                  <option value="beauty">Beauty & Fashion</option>
                  <option value="cooking">Cooking & Food</option>
                  <option value="travel">Travel</option>
                  <option value="fitness">Fitness & Health</option>
                  <option value="music">Music</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div>
                <label for="goals" class="block text-sm font-medium text-gray-700 mb-2">
                  Primary Goal
                </label>
                <select
                  id="goals"
                  v-model="form.goals"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Select goal</option>
                  <option value="monetization">Monetization</option>
                  <option value="growth">Subscriber Growth</option>
                  <option value="engagement">Increase Engagement</option>
                  <option value="content">Improve Content Quality</option>
                  <option value="analytics">Better Analytics Understanding</option>
                  <option value="branding">Build Personal Brand</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Notification Preferences -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Preferences</h3>
            <div class="space-y-4">
              <div class="flex items-center">
                <input
                  id="email_notifications"
                  v-model="form.email_notifications"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label for="email_notifications" class="ml-3 text-sm text-gray-700">
                  Email notifications about new courses and updates
                </label>
              </div>

              <div class="flex items-center">
                <input
                  id="course_reminders"
                  v-model="form.course_reminders"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label for="course_reminders" class="ml-3 text-sm text-gray-700">
                  Course progress reminders
                </label>
              </div>

              <div class="flex items-center">
                <input
                  id="marketing_emails"
                  v-model="form.marketing_emails"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label for="marketing_emails" class="ml-3 text-sm text-gray-700">
                  Marketing emails and special offers
                </label>
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
              Reset Changes
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-lg transition duration-200"
            >
              {{ loading ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Account Actions -->
      <div class="mt-8 bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Account Actions</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
            <div>
              <h4 class="text-sm font-medium text-gray-900">Change Password</h4>
              <p class="text-sm text-gray-600">Update your account password</p>
            </div>
            <button
              @click="showPasswordForm = !showPasswordForm"
              class="text-blue-600 hover:text-blue-800 text-sm font-medium"
            >
              Change Password
            </button>
          </div>

          <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
            <div>
              <h4 class="text-sm font-medium text-gray-900">Download Data</h4>
              <p class="text-sm text-gray-600">Download a copy of your account data</p>
            </div>
            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
              Download
            </button>
          </div>

          <div class="flex items-center justify-between p-4 border border-red-200 rounded-lg bg-red-50">
            <div>
              <h4 class="text-sm font-medium text-red-900">Delete Account</h4>
              <p class="text-sm text-red-600">Permanently delete your account and all data</p>
            </div>
            <button class="text-red-600 hover:text-red-800 text-sm font-medium">
              Delete Account
            </button>
          </div>
        </div>
      </div>

      <!-- Password Change Form -->
      <div v-if="showPasswordForm" class="mt-6 bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
        <form @submit.prevent="updatePassword" class="space-y-4">
          <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
              Current Password
            </label>
            <input
              id="current_password"
              v-model="passwordForm.current_password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
              New Password
            </label>
            <input
              id="new_password"
              v-model="passwordForm.new_password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
              Confirm New Password
            </label>
            <input
              id="confirm_password"
              v-model="passwordForm.confirm_password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div class="flex space-x-4">
            <button
              type="button"
              @click="showPasswordForm = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="passwordLoading"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-lg transition duration-200"
            >
              {{ passwordLoading ? 'Updating...' : 'Update Password' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { api } from '@/services/api'

export default {
  name: 'ProfileView',
  setup() {
    const authStore = useAuthStore()
    const loading = ref(false)
    const passwordLoading = ref(false)
    const showPasswordForm = ref(false)

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

    const loadUserData = () => {
      const user = authStore.user
      if (user) {
        Object.keys(form).forEach(key => {
          if (user[key] !== undefined) {
            form[key] = user[key]
          }
        })
      }
    }

    const updateProfile = async () => {
      loading.value = true
      try {
        const response = await api.put('/user/profile', form)
        authStore.setUser(response.data.user)
        alert('Profile updated successfully!')
      } catch (error) {
        console.error('Error updating profile:', error)
        alert('Error updating profile. Please try again.')
      } finally {
        loading.value = false
      }
    }

    const updatePassword = async () => {
      if (passwordForm.new_password !== passwordForm.confirm_password) {
        alert('New passwords do not match!')
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
        alert('Password updated successfully!')
      } catch (error) {
        console.error('Error updating password:', error)
        alert('Error updating password. Please check your current password and try again.')
      } finally {
        passwordLoading.value = false
      }
    }

    const resetForm = () => {
      loadUserData()
    }

    onMounted(() => {
      loadUserData()
    })

    return {
      form,
      passwordForm,
      loading,
      passwordLoading,
      showPasswordForm,
      getInitials,
      updateProfile,
      updatePassword,
      resetForm
    }
  }
}
</script>
