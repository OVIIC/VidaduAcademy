<template>
  <div
    class="bg-dark-900/40 backdrop-blur-md border border-dark-700/50 rounded-2xl overflow-hidden transition-all duration-300 group"
    :class="[
      enableHoverEffects
        ? 'hover:shadow-2xl hover:shadow-primary-500/10 hover:border-primary-500/30'
        : '',
    ]"
  >
    <div class="relative overflow-hidden">
      <LazyImage
        :src="course.thumbnail || '/images/video-academy-logo.png'"
        :alt="course.title"
        container-class="w-full h-48"
        :image-class="[
          course.thumbnail ? 'object-cover' : 'object-contain p-8 bg-dark-800',
          enableHoverEffects ? 'group-hover:scale-105' : '',
        ]"
        :fallback-src="'/images/video-academy-logo.png'"
      />
      <div v-if="course.featured" class="absolute top-3 right-3">
        <StarIcon class="h-5 w-5 text-yellow-400 fill-current" />
      </div>
    </div>

    <div class="p-6">
      <!-- Instructor -->
      <div v-if="course.instructor" class="flex items-center mb-3">
        <div
          class="w-8 h-8 bg-dark-700 rounded-full flex items-center justify-center mr-3"
        >
          <span class="text-xs font-medium text-dark-300">
            {{ getInstructorInitials(course.instructor?.name) }}
          </span>
        </div>
        <div>
          <p class="text-sm font-medium text-white">
            {{ course.instructor?.name }}
          </p>
          <p
            v-if="course.instructor?.subscribers_count"
            class="text-xs text-dark-400"
          >
            {{
              formatSubscribers(course.instructor?.subscribers_count)
            }}
            odberateľov
          </p>
        </div>
      </div>

      <!-- Course Title -->
      <h3
        class="text-lg font-semibold text-white mb-2 line-clamp-2 transition-colors"
        :class="[enableHoverEffects ? 'group-hover:text-primary-500' : '']"
      >
        {{ course.title }}
      </h3>

      <!-- Short Description -->
      <p class="text-dark-300 text-sm mb-4 line-clamp-2">
        {{ course.short_description }}
      </p>

      <!-- Course Meta (Duration only) -->
      <div v-if="showDuration" class="flex items-center text-sm text-dark-400">
        <div class="flex items-center">
          <ClockIcon class="h-4 w-4 mr-1" />
          {{ formatDuration(course.duration_minutes) }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { StarIcon, ClockIcon } from "@heroicons/vue/24/outline";
import LazyImage from "@/components/ui/LazyImage.vue";

defineProps({
  course: {
    type: Object,
    required: true,
  },
  showDuration: {
    type: Boolean,
    default: true,
  },
  enableHoverEffects: {
    type: Boolean,
    default: true,
  },
});

const getInstructorInitials = (name) => {
  if (!name) return "NN";
  return name
    .split(" ")
    .map((word) => word[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
};

const formatSubscribers = (count) => {
  if (!count || count === 0) return "0";
  if (count >= 1000000) {
    return `${(count / 1000000).toFixed(1)}M`;
  } else if (count >= 1000) {
    return `${(count / 1000).toFixed(1)}K`;
  }
  return count.toString();
};

const formatDuration = (minutes) => {
  if (!minutes || minutes === 0) return "0m";
  if (minutes < 60) {
    return `${minutes}m`;
  }
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}m` : `${hours}h`;
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
