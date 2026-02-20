<template>
  <div class="min-h-screen bg-dark-950 text-white overflow-x-hidden relative">
    <!-- Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
      <div
        class="absolute -top-[500px] -left-1/4 w-[1000px] h-[1000px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow"
      ></div>
      <div
        class="absolute top-[40%] -right-1/4 w-[800px] h-[800px] bg-secondary-600/10 rounded-full blur-[100px] opacity-30 animate-pulse-slow delay-500"
      ></div>
      <div
        class="absolute -bottom-[200px] -left-1/4 w-[800px] h-[800px] bg-primary-600/20 rounded-full blur-[120px] opacity-30 animate-pulse-slow delay-1000"
      ></div>
      <!-- Grid pattern removed -->
    </div>

    <!-- Course Detail Hero Section -->
    <div class="relative z-10">
      <CourseDetail
        :course="selectedCourse"
        :loading="
          showCheckoutLoading && checkoutCourse?.id === selectedCourse?.id
        "
        @purchase="handlePurchase"
      >
        <!-- Navigation Arrows overlaid on the hero image in CourseView only -->
        <template #controls>
          <div v-if="courses.length > 1" class="flex items-center gap-3">
            <button
              @click="navigateCourse('prev')"
              class="w-14 h-[60px] rounded-2xl bg-dark-800/80 hover:bg-dark-800 border border-dark-700 hover:border-dark-600 text-white flex items-center justify-center transition-all duration-200 backdrop-blur-sm shadow-lg"
              :disabled="currentCourseIndex === 0"
              :class="{
                'opacity-50 cursor-not-allowed': currentCourseIndex === 0,
                'hover:translate-y-[-2px]': currentCourseIndex !== 0
              }"
              aria-label="Predchádzajúci kurz"
            >
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"
                />
              </svg>
            </button>

            <button
              @click="navigateCourse('next')"
              class="w-14 h-[60px] rounded-2xl bg-dark-800/80 hover:bg-dark-800 border border-dark-700 hover:border-dark-600 text-white flex items-center justify-center transition-all duration-200 backdrop-blur-sm shadow-lg"
              :disabled="currentCourseIndex === courses.length - 1"
              :class="{
                'opacity-50 cursor-not-allowed':
                  currentCourseIndex === courses.length - 1,
                'hover:translate-y-[-2px]': currentCourseIndex !== courses.length - 1
              }"
              aria-label="Ďalší kurz"
            >
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </button>
          </div>
        </template>

        <!-- Course Gallery Section using CourseCatalog -->
        <div class="py-12 mt-8">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <CourseCatalog
              :selected-course-id="selectedCourse?.id"
              @select="selectCourse"
            />
          </div>
        </div>
      </CourseDetail>
    </div>

    <!-- Checkout Loading Modal -->
    <CheckoutLoadingModal
      :show="showCheckoutLoading"
      :courseTitle="checkoutCourse?.title"
      :coursePrice="checkoutCourse?.price"
    />
  </div>
</template>
<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useEnrollmentStore } from "@/stores/enrollment";
import { useCourseStore } from "@/stores/course";
import { usePurchase } from "@/composables/usePurchase";
import CheckoutLoadingModal from "@/components/ui/CheckoutLoadingModal.vue";
import CourseCatalog from "@/components/course/CourseCatalog.vue";
import CourseDetail from "@/components/course/CourseDetail.vue";
import { useHead } from '@unhead/vue';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const enrollmentStore = useEnrollmentStore();
const courseStore = useCourseStore();

// Use course store
const courses = computed(() => courseStore.courses);
// loading state is handled inside CourseCatalog for the grid,
// but we might use it here if we wanted to show a global loader,
// though CourseCatalog handles its own loading.
const {
  loading: purchaseLoading,
  checkoutCourse,
  handlePurchase: startPurchase,
} = usePurchase();

const showCheckoutLoading = computed(() => purchaseLoading.value);

const selectedCourse = ref(null);

const pageTitle = computed(() => {
  if (selectedCourse.value) {
    return `${selectedCourse.value.title} | VidaduAcademy`
  }
  return 'Kurzy | VidaduAcademy'
})

useHead({
  title: pageTitle,
  meta: [
    {
      name: 'description',
      content: computed(() => selectedCourse.value?.short_description || 'Prezrite si naše kurzy a naučte sa všetko o YouTube raste.')
    },
    {
      property: 'og:title',
      content: pageTitle
    },
     {
      property: 'og:description',
      content: computed(() => selectedCourse.value?.short_description || 'Prezrite si naše kurzy a naučte sa všetko o YouTube raste.')
    }
  ]
})

const currentCourseIndex = computed(() => {
  if (!selectedCourse.value || !courses.value.length) return -1;
  return courses.value.findIndex((c) => c.id === selectedCourse.value.id);
});

const navigateCourse = (direction) => {
  const currentIndex = currentCourseIndex.value;
  if (currentIndex === -1) return;

  let newIndex;
  if (direction === "prev") {
    newIndex = Math.max(0, currentIndex - 1);
  } else {
    newIndex = Math.min(courses.value.length - 1, currentIndex + 1);
  }

  if (newIndex !== currentIndex) {
    selectCourse(courses.value[newIndex]);
  }
};

const selectCourse = async (course) => {
  if (import.meta.env.DEV) console.log("Selecting course:", course.title);
  
  // Update URL to reflect selection without reloading
  if (route.query.slug !== course.slug) {
    router.push({ query: { ...route.query, slug: course.slug } });
  }

  selectedCourse.value = course;

  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });

  try {
    const fullCourse = await courseStore.fetchCourse(course.slug);
    // Ensure we don't overwrite if user switched quickly
    if (selectedCourse.value.slug === course.slug) {
        selectedCourse.value = { ...selectedCourse.value, ...fullCourse };
    }
  } catch (e) {
    console.error(e);
  }
};

// Watch for URL changes (back/forward navigation)
watch(
  () => route.query.slug,
  (newSlug) => {
    if (newSlug && courses.value.length > 0) {
      const found = courses.value.find((c) => c.slug === newSlug);
      if (found && selectedCourse.value?.id !== found.id) {
        selectCourse(found);
      }
    }
  }
);

const loadCourses = async () => {
  try {
    if (import.meta.env.DEV) console.log("Loading courses via course store...");

    await courseStore.fetchCourses();
    await courseStore.fetchCategories();

    if (import.meta.env.DEV)
      console.log("Courses loaded:", courses.value.length);

    if (route.query.slug) {
      const found = courses.value.find((c) => c.slug === route.query.slug);
      if (found) {
        selectedCourse.value = found;
        if (import.meta.env.DEV)
          console.log("Selected course from query param:", found.title);
      }
    }

    if (courses.value.length > 0 && !selectedCourse.value) {
      selectedCourse.value = courses.value[0];
      if (import.meta.env.DEV)
        console.log("Set first course as hero:", selectedCourse.value.title);
    }

    if (authStore.user && courses.value.length > 0) {
      await loadPurchaseStatus();
    }
  } catch (error) {
    console.error("Error loading courses:", error);
  }
};

const loadPurchaseStatus = async () => {
  try {
    for (const course of courses.value) {
      await enrollmentStore.checkCoursePurchaseStatus(course.id);
    }
  } catch (error) {
    console.error("Error loading purchase status:", error);
  }
};

const handlePurchase = (course) => {
  const successUrl = `${window.location.origin}/payment/success?session_id={CHECKOUT_SESSION_ID}`;
  const cancelUrl = `${window.location.origin}/courses?cancelled=true`;
  startPurchase(course, successUrl, cancelUrl);
};

onMounted(() => {
  loadCourses();
});
</script>

<style scoped>
/* Animations shared */
@keyframes pulse-slow {
  0%,
  100% {
    opacity: 0.3;
    transform: scale(1);
  }
  50% {
    opacity: 0.2;
    transform: scale(1.1);
  }
}

.animate-pulse-slow {
  animation: pulse-slow 8s infinite ease-in-out;
}

.delay-500 {
  animation-delay: 0.5s;
}
.delay-1000 {
  animation-delay: 1s;
}
</style>
