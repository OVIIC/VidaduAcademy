import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import HomeView from '@/views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: HomeView,
    },
    {
      path: '/courses',
      name: 'Courses',
      component: () => import('@/views/CoursesView.vue'),
    },
    {
      path: '/course/:slug',
      redirect: to => ({ name: 'Courses', query: { slug: to.params.slug } }),
    },
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { requiresGuest: true },
    },
    {
      path: '/register',
      name: 'Register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: { requiresGuest: true },
    },
    {
      path: '/auth/callback/:provider',
      name: 'SocialCallback',
      component: () => import('@/views/auth/SocialCallbackView.vue'),
      meta: { requiresGuest: true },
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: () => import('@/views/DashboardView.vue'),
      meta: { requiresAuth: true, layout: 'dashboard' },
    },
    {
      path: '/my-courses',
      name: 'MyCourses',
      component: () => import('@/views/MyCoursesView.vue'),
      meta: { requiresAuth: true, layout: 'dashboard' },
    },
    {
      path: '/study/:slug',
      name: 'CourseStudy',
      component: () => import('@/views/CourseStudyView.vue'),
      meta: { requiresAuth: true, layout: 'dashboard' },
      props: true,
    },
    {
      path: '/checkout',
      name: 'StripeCheckout',
      component: () => import('@/views/checkout/StripeCheckoutView.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/payment/success',
      name: 'PaymentSuccess',
      component: () => import('@/views/PaymentSuccessView.vue'),
      // Remove auth requirement for payment success page
      // meta: { requiresAuth: true },
    },
    {
      path: '/profile',
      name: 'Profile',
      component: () => import('@/views/ProfileView.vue'),
      meta: { requiresAuth: true, layout: 'dashboard' },
    },


    {
      path: '/kontakt',
      name: 'Contact',
      component: () => import('@/views/ContactView.vue'),
    },
    {
      path: '/ochrana-osobnych-udajov',
      name: 'PrivacyPolicy',
      component: () => import('@/views/legal/PrivacyPolicyView.vue'),
    },
    {
      path: '/podmienky-sluzby',
      name: 'TermsOfService',
      component: () => import('@/views/legal/TermsOfServiceView.vue'),
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/views/NotFoundView.vue'),
    },
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  },
})

// Navigation guards
router.beforeEach((to, from, next) => {
  try {
    const authStore = useAuthStore()
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
      next({ name: 'Login', query: { redirect: to.fullPath } })
    } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
      next({ name: 'Dashboard' })
    } else if (to.meta.roles) {
      // Check for role requirements
      const hasRequiredRole = to.meta.roles.some(role => authStore.hasRole(role))
      
      // Special check for instructor boolean flag if role is missing
      const isInstructorOverride = to.meta.roles.includes('instructor') && authStore.isInstructor
      
      if (hasRequiredRole || isInstructorOverride) {
        next()
      } else {
        // Redirect to dashboard if unauthorized
        console.warn('Unauthorized access to role-protected route', to.path)
        next({ name: 'Dashboard' })
      }
    } else {
      next()
    }
  } catch (error) {
    console.error('Navigation guard error:', error)
    // Fallback: allow navigation if there's an error
    next()
  }
})

// Error handling
router.onError((error) => {
  console.error('Router error:', error)
})

export default router
