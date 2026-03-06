import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useEnrollmentStore } from '@/stores/enrollment'
import { paymentService } from '@/services'
import { useToast } from 'vue-toastification'

export function usePurchase() {
  const router = useRouter()
  const authStore = useAuthStore()
  const enrollmentStore = useEnrollmentStore()
  const toast = useToast()

  const showEmailModal = ref(false)
  const checkoutCourse = ref(null)

  const handlePurchase = async (course, successRedirectUrl = null, cancelRedirectUrl = null) => {
    checkoutCourse.value = course
    showEmailModal.value = true
  }

  return {
    showEmailModal,
    checkoutCourse,
    handlePurchase
  }
}
