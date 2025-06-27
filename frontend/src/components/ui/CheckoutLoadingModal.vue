<template>
  <!-- Modal Overlay -->
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="loading-modal-title"
    role="dialog"
    aria-modal="true"
  >
    <!-- Background overlay -->
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        aria-hidden="true"
      ></div>

      <!-- This element is to trick the browser into centering the modal contents. -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal panel -->
      <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
        <!-- Loading content -->
        <div class="text-center">
          <!-- Loading spinner -->
          <div class="mx-auto flex items-center justify-center h-16 w-16 mb-4">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-primary-600"></div>
          </div>
          
          <!-- Title -->
          <h3 class="text-lg font-medium text-gray-900 mb-2" id="loading-modal-title">
            Presmerovanie na platbu
          </h3>
          
          <!-- Description -->
          <p class="text-sm text-gray-500 mb-4">
            Pripravujeme váš Stripe checkout. Prosím čakajte...
          </p>
          
          <!-- Course info if provided -->
          <div v-if="courseTitle" class="bg-gray-50 rounded-lg p-3">
            <p class="text-sm font-medium text-gray-900">{{ courseTitle }}</p>
            <p v-if="coursePrice" class="text-sm text-gray-500">€{{ coursePrice }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  },
  courseTitle: {
    type: String,
    default: null
  },
  coursePrice: {
    type: [String, Number],
    default: null
  }
})
</script>

<style scoped>
/* Custom spinner animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
