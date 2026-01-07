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

  const loading = ref(false)
  const checkoutCourse = ref(null)

  const handlePurchase = async (course, successRedirectUrl = null, cancelRedirectUrl = null) => {
    if (!authStore.user) {
      toast.info('Pre nákup kurzu sa musíte prihlásiť.')
      router.push('/login')
      return
    }

    const isPurchased = enrollmentStore.hasPurchasedCourse(course.id)
    
    if (isPurchased) {
      toast.info('Tento kurz už máte zakúpený a nachádza sa v sekcii "Moje kurzy".')
      return
    }

    try {
      loading.value = true
      checkoutCourse.value = course
      
      const successUrl = successRedirectUrl || `${window.location.origin}/payment/success?session_id={CHECKOUT_SESSION_ID}`
      const cancelUrl = cancelRedirectUrl || window.location.href
      
      const response = await paymentService.createCheckoutSession(course.id, successUrl, cancelUrl)
      
      if (response.checkout_url) {
        window.location.href = response.checkout_url
      } else {
        throw new Error('No checkout URL received')
      }
    } catch (error) {
      console.error('Error creating checkout session:', error)
      loading.value = false
      checkoutCourse.value = null
      
      if (import.meta.env.DEV) {
          const checkoutUrl = `/checkout?courseTitle=${encodeURIComponent(course.title)}&coursePrice=${course.price}&courseId=${course.id}&courseSlug=${encodeURIComponent(course.slug)}`
          router.push(checkoutUrl)
      } else {
          toast.error('Chyba pri vytváraní platby. Skúste to prosím neskôr.')
      }
    }
  }

  return {
    loading,
    checkoutCourse,
    handlePurchase
  }
}
