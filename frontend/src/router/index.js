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
      name: 'CourseDetail',
      component: () => import('@/views/CourseDetailView.vue'),
      props: true,
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
      path: '/dashboard',
      name: 'Dashboard',
      component: () => import('@/views/DashboardView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/my-courses',
      name: 'MyCourses',
      component: () => import('@/views/MyCoursesView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/study/:slug',
      name: 'CourseStudy',
      component: () => import('@/views/CourseStudyView.vue'),
      meta: { requiresAuth: true },
      props: true,
    },
    {
      path: '/checkout',
      name: 'StripeCheckout',
      component: () => import('@/views/checkout/StripeCheckoutView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/learn/:courseSlug',
      name: 'Learn',
      component: () => import('@/views/LearnView.vue'),
      meta: { requiresAuth: true },
      props: true,
    },
    {
      path: '/learn/:courseSlug/:lessonSlug',
      name: 'Lesson',
      component: () => import('@/views/LessonView.vue'),
      meta: { requiresAuth: true },
      props: true,
    },
    {
      path: '/payment/success',
      name: 'PaymentSuccess',
      component: () => import('@/views/PaymentSuccessView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/profile',
      name: 'Profile',
      component: () => import('@/views/ProfileView.vue'),
      meta: { requiresAuth: true },
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
