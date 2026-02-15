<template>
  <div>
    <!-- Hero Course Section (Disney+ style) -->
    <div
      class="relative h-[50vh] md:h-[75vh] overflow-hidden z-10"
      :class="{ 'rounded-3xl mx-4 mt-4': isDashboard }"
    >
      <!-- Background Image with Smooth Transition -->
      <div class="absolute inset-0">
        <div class="relative w-full h-full">
          <transition name="hero-fade" mode="out-in">
            <img
              :key="course?.id || 'default'"
              :src="course?.thumbnail || 'https://placehold.co/1920x1080'"
              :alt="course?.title || 'Course'"
              class="w-full h-full object-cover"
            />
          </transition>
          <!-- Disney+ style gradient overlays -->
          <div
            class="absolute inset-0 bg-gradient-to-r from-dark-950/90 via-dark-950/50 to-transparent"
          ></div>
          <div
            class="absolute inset-0 bg-gradient-to-t from-dark-950/80 via-transparent to-transparent"
          ></div>

          <!-- Back Button for Dashboard -->
          <button
            v-if="backButton"
            @click="$emit('back')"
            class="absolute left-6 top-6 z-30 flex items-center space-x-2 text-white/80 hover:text-white bg-dark-900/40 hover:bg-dark-900/60 backdrop-blur-md px-4 py-2 rounded-xl transition-all border border-white/10 hover:border-white/30"
          >
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
            <span class="font-bold">Späť na katalóg</span>
          </button>

          <!-- Navigation Arrows (only if handled by parent via slots/events, or we pass list. For now, we keep it simple: single course view) -->
          <!-- Slot for overlays like navigation arrows -->
          <slot name="hero-overlay"></slot>
        </div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 h-full flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
          <transition name="hero-content-fade" mode="out-in">
            <div :key="course?.id || 'default'" class="max-w-3xl mt-16">
              <!-- Course Title (Large Disney+ style) -->
              <h1
                class="text-3xl md:text-5xl lg:text-6xl font-black mb-6 leading-none tracking-tight text-white drop-shadow-2xl"
              >
                {{ course?.title || "Načítavajú sa kurzy..." }}
              </h1>

              <!-- Course Info Bar (Disney+ style) -->
              <div
                v-if="course"
                class="flex items-center space-x-6 mb-6 text-lg font-medium"
              >
                <div class="flex items-center space-x-2">
                  <svg
                    class="w-5 h-5 text-yellow-400"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                  <span class="text-white">{{
                    course.instructor?.name || course.instructor
                  }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div
                    class="w-3 h-3 rounded-full"
                    :class="getDifficultyColor(course.difficulty)"
                  ></div>
                  <span class="capitalize text-white">{{
                    course.difficulty
                  }}</span>
                </div>
                <div class="text-dark-200">{{ course.duration }}</div>
                <div class="text-green-400 font-bold">€{{ course.price }}</div>
              </div>

              <!-- Course Description -->
              <p
                class="text-lg md:text-xl leading-relaxed mb-8 text-dark-200 max-w-2xl font-extralight shadow-black drop-shadow-md"
              >
                {{
                  course?.short_description ||
                  "Vyberte kurz pre zobrazenie detailov."
                }}
              </p>

              <!-- Action Buttons (Disney+ style) -->
              <div class="flex items-center space-x-4">
                <button
                  v-if="course"
                  @click="handlePurchase"
                  :disabled="loading"
                  class="bg-primary-600 hover:bg-primary-500 text-white disabled:opacity-50 px-10 py-4 rounded-2xl font-bold text-xl transition-all duration-200 flex items-center space-x-3 shadow-lg hover:shadow-primary-500/25"
                >
                  <svg
                    v-if="loading"
                    class="animate-spin w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                    ></path>
                  </svg>
                  <svg
                    v-else
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V9a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2z"
                    />
                  </svg>
                  <span>{{ loading ? "Spracováva sa..." : "Kúpiť kurz" }}</span>
                </button>

                <button
                  v-if="!isDashboard"
                  @click="toggleCourseDetails"
                  class="bg-dark-800/80 hover:bg-dark-800 text-white px-8 py-4 rounded-2xl font-bold text-xl transition-all duration-200 flex items-center space-x-3 border border-dark-700 hover:border-dark-600 backdrop-blur-sm"
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
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  <span>{{ showDetails ? "Skryť info" : "Viac info" }}</span>
                </button>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </div>

    <!-- Content Section -->
    <div
      class="relative z-20 bg-dark-950 rounded-t-[3rem] -mt-8 border-t border-dark-800/50"
      :class="{ 'min-h-screen': !isDashboard }"
    >
      <!-- Expandable Course Details Section -->
      <transition name="slide-down">
        <div v-if="showDetails && course" class="border-b border-dark-800">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              <!-- Main Content -->
              <div class="lg:col-span-2 space-y-8">
                <!-- Full Description -->
                <div>
                  <h3 class="text-2xl font-bold text-white mb-4">O kurze</h3>
                  <p class="text-dark-300 text-lg leading-relaxed">
                    {{ course.description }}
                  </p>
                </div>

                <!-- What You'll Learn -->
                <div
                  v-if="
                    course.what_you_will_learn &&
                    course.what_you_will_learn.length > 0
                  "
                >
                  <h3 class="text-2xl font-bold text-white mb-4">
                    Čo sa naučíte
                  </h3>
                  <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <li
                      v-for="(item, index) in course.what_you_will_learn"
                      :key="index"
                      class="flex items-start space-x-3"
                    >
                      <svg
                        class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span class="text-dark-300">{{ item }}</span>
                    </li>
                  </ul>
                </div>

                <!-- Requirements -->
                <div
                  v-if="course.requirements && course.requirements.length > 0"
                >
                  <h3 class="text-2xl font-bold text-white mb-4">Požiadavky</h3>
                  <ul class="space-y-2">
                    <li
                      v-for="(req, index) in course.requirements"
                      :key="index"
                      class="flex items-start space-x-3"
                    >
                      <svg
                        class="w-6 h-6 text-dark-400 flex-shrink-0 mt-0.5"
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
                      <span class="text-dark-300">{{ req }}</span>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Sidebar -->
              <div class="lg:col-span-1">
                <div
                  class="bg-dark-900 rounded-3xl p-6 border border-dark-800 sticky top-4 shadow-xl"
                >
                  <!-- Course Stats -->
                  <div class="space-y-4 mb-6">
                    <div class="flex items-center justify-between">
                      <span class="text-dark-400">Úroveň</span>
                      <span class="text-white capitalize font-medium">{{
                        course.difficulty_level
                      }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-dark-400">Dĺžka</span>
                      <span class="text-white font-medium">{{
                        course.duration
                      }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-dark-400">Cena</span>
                      <span class="text-green-400 font-bold text-xl"
                        >€{{ course.price }}</span
                      >
                    </div>
                  </div>

                  <div class="border-t border-dark-800 pt-6">
                    <!-- Instructor Info -->
                    <div class="mb-6">
                      <h4 class="text-sm font-medium text-dark-400 mb-3">
                        Inštruktor
                      </h4>
                      <div class="flex items-center space-x-3">
                        <div
                          class="w-12 h-12 rounded-full bg-dark-800 flex items-center justify-center text-primary-400 font-bold text-lg border border-dark-700"
                        >
                          {{
                            (
                              course.instructor?.name ||
                              course.instructor ||
                              ""
                            ).charAt(0)
                          }}
                        </div>
                        <div>
                          <p class="text-white font-medium">
                            {{ course.instructor?.name || course.instructor }}
                          </p>
                          <p class="text-dark-400 text-sm">
                            {{ course.instructor?.email || "" }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Purchase Button -->
                    <button
                      @click="handlePurchase"
                      :disabled="loading"
                      class="w-full bg-primary-600 hover:bg-primary-500 text-white px-6 py-3 rounded-xl font-bold transition-all duration-200 shadow-lg hover:shadow-primary-500/25"
                    >
                      {{ loading ? "Spracováva sa..." : "Kúpiť kurz" }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <!-- Slot for extra content like Grid or Reviews -->
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useCourseStore } from "@/stores/course";

const props = defineProps({
  course: {
    type: Object,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  backButton: {
    type: Boolean,
    default: false,
  },
  isDashboard: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["purchase", "back"]);

const courseStore = useCourseStore();
const showDetails = ref(props.isDashboard);
const fullCourseLoaded = ref(false);

// Difficulty color mapping
const getDifficultyColor = (difficulty) => {
  const colors = {
    beginner: "bg-green-500",
    intermediate: "bg-yellow-500",
    advanced: "bg-red-500",
    expert: "bg-purple-500",
  };
  return colors[difficulty?.toLowerCase()] || "bg-gray-500";
};

const toggleCourseDetails = async () => {
  if (!showDetails.value && !fullCourseLoaded.value) {
    // Fetch full course details if needed
    try {
      const fullCourse = await courseStore.fetchCourse(props.course.slug);

      // We want to mutate the object in place if possible or let parent handle it.
      // But props are readonly. Best is if parent already passes full object.
      // However here we are inside component.
      // We can rely on reactivity if parent state updates.
      // But props might be a shallow copy.

      // Simplest: just expand. If data missing, show what we have.
      // Ideally parent ensures data is detailed when passing to this component
      // OR this component fetches and emits update?

      // Let's assume parent passes whatever they have.
      // If we need to fetch more, we should use the store and update.
    } catch (error) {
      console.error(error);
    }
  }

  showDetails.value = !showDetails.value;

  if (showDetails.value) {
    setTimeout(() => {
      const detailsSection = document.querySelector(".slide-down-enter-active");
      if (detailsSection) {
        detailsSection.scrollIntoView({ behavior: "smooth", block: "nearest" });
      }
    }, 100);
  }
};

const handlePurchase = () => {
  emit("purchase", props.course);
};
</script>

<style scoped>
/* Smooth transitions */
.hero-fade-enter-active,
.hero-fade-leave-active {
  transition: opacity 0.3s ease-in-out;
}
.hero-fade-enter-from,
.hero-fade-leave-to {
  opacity: 0;
}
.hero-fade-enter-to,
.hero-fade-leave-from {
  opacity: 1;
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  max-height: 2000px;
  overflow: hidden;
}
.slide-down-enter-from,
.slide-down-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-20px);
}
.slide-down-enter-to,
.slide-down-leave-from {
  max-height: 2000px;
  opacity: 1;
  transform: translateY(0);
}
</style>
