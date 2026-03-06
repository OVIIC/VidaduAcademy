<template>
  <!-- Modal Overlay -->
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
  >
    <!-- Background overlay -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        aria-hidden="true"
        @click="closeModal"
      ></div>

      <!-- This element is to trick the browser into centering the modal contents. -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal panel -->
      <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full sm:p-6">
        <!-- Close button -->
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button
            @click="closeModal"
            type="button"
            class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <span class="sr-only">Zavrieť</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="sm:flex sm:items-start pl-2 pt-2">
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full pr-4">
            <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-6" id="modal-title">
              Získajte kurz
            </h3>
            
            <div class="mt-2">
              <p class="text-lg text-gray-700 leading-relaxed mb-6 font-medium">
                Ak si chceš zakúpiť kurz, tak nám, prosím, zanechaj svoj e-mail a počuli sme, že ti naň príde super zľavička... alebo možno aj kurz zadarmo 😉.
              </p>
              
              <form @submit.prevent="submitEmail" class="mt-4">
                <div class="mb-4">
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Váš E-mail</label>
                  <input 
                    type="email" 
                    id="email" 
                    v-model="email" 
                    required
                    placeholder="napr. jozko@mrkvicka.sk"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-lg border-gray-300 rounded-md py-3 px-4"
                    :disabled="loading"
                  />
                </div>
                
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                  <button
                    type="submit"
                    :disabled="loading || !email"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-3 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-lg sm:w-auto disabled:opacity-50 transition-colors"
                  >
                    <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Odoslať</span>
                  </button>
                  <button
                    type="button"
                    @click="closeModal"
                    class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-lg sm:w-auto mt-3 sm:mt-0 transition-colors"
                  >
                    Zrušiť
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import api from '@/services/api' /* assuming this is the axios instance */

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  courseId: {
    type: [Number, String],
    default: null
  }
})

const emit = defineEmits(['close'])

const toast = useToast()
const email = ref('')
const loading = ref(false)

const closeModal = () => {
  emit('close')
  email.value = ''
}

const submitEmail = async () => {
  if (!email.value) return
  
  loading.value = true
  try {
    // We try to use the common api service pattern in this project
    await api.post('/course-emails', {
      email: email.value,
      course_id: props.courseId
    })
    
    toast.success('Ďakujeme! Váš e-mail bol úspešne zaznamenaný.')
    closeModal()
  } catch (error) {
    console.error('Error submitting email:', error)
    toast.error('Ospravedlňujeme sa, vyskytla sa chyba. Skúste to prosím znova.')
  } finally {
    loading.value = false
  }
}
</script>
