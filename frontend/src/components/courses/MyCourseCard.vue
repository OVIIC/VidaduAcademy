<template>
  <div class="bg-dark-900 rounded-2xl overflow-hidden shadow-lg border border-dark-800 hover:border-primary-500/50 hover:shadow-primary-500/10 flex flex-col group transition-all duration-300 h-full">
    <!-- Course Image -->
    <router-link :to="{ name: 'CourseStudy', params: { slug: course.slug } }" class="block relative aspect-video overflow-hidden">
      <img
        v-if="course.thumbnail"
        :src="course.thumbnail"
        :alt="course.title"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
      />
      <div v-else class="w-full h-full bg-dark-800 flex items-center justify-center">
        <svg class="w-12 h-12 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
      
      <!-- Progress Overlay (Bottom of image) -->
      <div class="absolute bottom-0 left-0 right-0 bg-dark-900/90 backdrop-blur-sm p-3 border-t border-dark-700/50">
        <div class="flex items-center justify-between text-xs mb-1.5">
          <span class="text-dark-300 font-medium">Pokrok</span>
          <span :class="['font-bold', isCompleted ? 'text-green-400' : 'text-primary-400']">
            {{ progressPercentage }}%
          </span>
        </div>
        <div class="w-full bg-dark-700 rounded-full h-1.5 overflow-hidden">
          <div
            :class="['h-full rounded-full transition-all duration-500', isCompleted ? 'bg-green-500' : 'bg-gradient-to-r from-primary-500 to-secondary-500']"
            :style="{ width: progressPercentage + '%' }"
          ></div>
        </div>
      </div>
      
      <!-- Status Badge -->
      <div class="absolute top-3 right-3">
          <span :class="['px-2.5 py-1 rounded-lg text-xs font-bold border backdrop-blur-md shadow-sm', statusBadgeClass]">
            {{ statusText }}
          </span>
      </div>
    </router-link>

    <!-- Course Content -->
    <div class="p-5 flex-grow flex flex-col">
      <div class="flex-grow">
        <router-link :to="{ name: 'CourseStudy', params: { slug: course.slug } }">
            <h3 class="font-bold text-lg mb-2 text-white line-clamp-2 group-hover:text-primary-400 transition-colors">
            {{ course.title }}
            </h3>
        </router-link>

        <p class="text-dark-400 text-sm line-clamp-2 mb-4">
            {{ course.short_description || 'Žiadny popis' }}
        </p>
      </div>

      <!-- Instructor info -->
      <div class="flex items-center mb-4 pt-4 border-t border-dark-800">
        <div class="w-8 h-8 rounded-full bg-dark-800 flex items-center justify-center text-xs font-bold text-dark-300 border border-dark-700 mr-3">
            {{ (course.instructor?.name || 'V').charAt(0) }}
        </div>
        <div>
            <div class="text-xs text-dark-400">Inštruktor</div>
            <div class="text-sm font-medium text-dark-200">{{ course.instructor?.name || 'Vidadu Team' }}</div>
        </div>
      </div>

      <!-- Footer Info -->
      <div class="flex items-center justify-between text-xs text-dark-500 mb-4">
        <span>Zapísaný: {{ formatDate(course.enrollment_data?.enrolled_at) }}</span>
      </div>

      <!-- Action buttons -->
      <div class="flex gap-3 mt-auto">
        <router-link
          v-if="course.slug"
          :to="{ name: 'CourseStudy', params: { slug: course.slug } }"
          class="flex-1 inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-bold rounded-xl text-white bg-primary-600 hover:bg-primary-500 transition-all shadow-lg hover:shadow-primary-500/25"
        >
          {{ isCompleted ? 'Opakovať kurz' : 'Pokračovať' }}
        </router-link>

        <div
          v-else
          class="flex-1 text-center px-4 py-2.5 rounded-xl text-sm font-bold bg-dark-800 text-dark-500 cursor-not-allowed border border-dark-700"
        >
          Nedostupné
        </div>

        <button
          v-if="isCompleted"
          @click="viewCertificate"
          class="px-4 py-2.5 border border-dark-600 rounded-xl text-sm font-bold text-dark-300 hover:bg-dark-800 hover:text-white transition-colors"
          title="Stiahnuť certifikát"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { authService } from "@/services";

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
  if (isCompleted.value) return "bg-green-500/10 text-green-400 border-green-500/20";
  if (progressPercentage.value > 0) return "bg-primary-500/10 text-primary-400 border-primary-500/20";
  return "bg-dark-800/80 text-dark-400 border-dark-700";
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
