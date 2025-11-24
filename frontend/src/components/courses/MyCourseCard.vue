<template>
  <div class="bg-secondary-surface rounded-lg shadow-highlight overflow-hidden hover:shadow-md transition duration-200">
    <!-- Course Image -->
    <div class="aspect-video bg-gray-200 relative">
      <img
        v-if="course.thumbnail"
        :src="course.thumbnail"
        :alt="course.title"
        class="w-full h-full object-cover"
      />
      <div class="absolute inset-0 bg-black bg-opacity-20"></div>

      <!-- Progress overlay -->
      <div
        class="absolute bottom-0 left-0 right-0 bg-secondary-700 bg-opacity-90 p-2"
      >
        <div class="flex items-center justify-between text-xs">
          <span class="text-gray-300">Pokrok</span>
          <span class="font-semibold text-primary-600"
            >{{ progressPercentage }}%</span
          >
        </div>
        <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
          <div
            class="bg-primary-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: progressPercentage + '%' }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Course Content -->
    <div class="p-4">
      <div class="flex items-start justify-between mb-2">
        <h3 class="text-lg font-semibold text-white line-clamp-2">
          {{ course.title }}
        </h3>
        <span
          :class="statusBadgeClass"
          class="ml-2 px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap"
        >
          {{ statusText }}
        </span>
      </div>

      <p class="text-sm text-gray-300 mb-3 line-clamp-2">
        {{ course.short_description }}
      </p>

      <!-- Instructor info -->
      <div class="flex items-center mb-3 text-sm text-gray-400">
        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path
            fill-rule="evenodd"
            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
            clip-rule="evenodd"
          />
        </svg>
        <span>{{ course.instructor?.name }}</span>
      </div>

      <!-- Enrollment info -->
      <div class="flex items-center justify-between text-xs text-gray-400 mb-4">
        <span
          >Zapísaný: {{ formatDate(course.enrollment_data?.enrolled_at) }}</span
        >
        <span v-if="course.enrollment_data?.last_accessed_at">
          Posledný prístup:
          {{ formatDate(course.enrollment_data.last_accessed_at) }}
        </span>
      </div>

      <!-- Action buttons -->
      <div class="flex space-x-2">
        <router-link
          v-if="course.slug"
          :to="{ name: 'CourseStudy', params: { slug: course.slug } }"
          class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-highlight text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
        >
          Pokračovať
        </router-link>

        <div
          v-else
          :class="[
            'flex-1 text-center px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed opacity-50',
            'bg-gray-300 text-gray-500',
          ]"
        >
          Kurz nedostupný
        </div>

        <button
          v-if="isCompleted"
          @click="viewCertificate"
          class="px-4 py-2 border border-gray-600 rounded-lg text-sm font-medium text-gray-300 hover:bg-secondary-700 transition duration-200 shadow-highlight"
        >
          Certifikát
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  course: {
    type: Object,
    required: true,
  },
});

const progressPercentage = computed(() => {
  return props.course.enrollment_data?.progress_percentage || 0;
});

const isCompleted = computed(() => {
  return progressPercentage.value >= 100;
});

const statusText = computed(() => {
  if (isCompleted.value) return "Dokončené";
  if (progressPercentage.value > 0) return "Prebieha";
  return "Nezačaté";
});

const statusBadgeClass = computed(() => {
  if (isCompleted.value) return "bg-green-500/20 text-green-300";
  if (progressPercentage.value > 0) return "bg-primary-500/20 text-primary-300";
  return "bg-gray-500/20 text-gray-300";
});

const formatDate = (dateString) => {
  if (!dateString) return "";

  const date = new Date(dateString);
  return date.toLocaleDateString("sk-SK", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const viewCertificate = () => {
  if (!props.course.enrollment_data?.id) return;

  // Use the direct URL approach to open in new tab
  // We need to append the auth token if using Sanctum via headers, but since we are opening in new tab,
  // we might need a different approach or rely on cookie auth if it was set up that way.
  // However, since we are using token based auth in headers, opening a new tab directly won't send headers.
  // A better approach for SPA is to fetch the blob and create a local URL, OR use a download token.
  // For this simple implementation, let's try fetching the HTML blob and showing it.

  // Actually, let's use the service to get the blob/text and open a window
  // But wait, the backend returns HTML.

  // Let's try to fetch it using our authenticated api client, then open a window with the content.
  import("@/services").then(({ authService }) => {
    // We can construct the URL but we need to pass the token.
    // Since we can't easily pass headers in a simple link, we will fetch the content and display it.
    // Or simpler: just assume the user is logged in (cookies) if we were using cookies.
    // But we are using Sanctum tokens in headers likely.

    // Let's go with: fetch content -> open window -> write content
    const enrollmentId = props.course.enrollment_data.id;
    authService
      .getCertificate(enrollmentId)
      .then((response) => {
        const win = window.open("", "_blank");
        if (win) {
          win.document.write(response);
          win.document.close();
        }
      })
      .catch((err) => {
        console.error("Error fetching certificate", err);
      });
  });
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
