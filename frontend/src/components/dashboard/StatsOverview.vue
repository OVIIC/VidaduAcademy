<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <!-- Enrolled Courses -->
    <div class="group bg-dark-900 border border-dark-800 hover:border-primary-500/30 rounded-3xl p-6 transition-all duration-300 hover:bg-dark-800/50">
      <div class="flex items-center">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-500/10 to-purple-500/10 flex items-center justify-center mr-6 group-hover:scale-110 transition-transform duration-300">
          <BookOpenIcon class="w-10 h-10 text-primary-400" />
        </div>
        <div>
          <h2 class="text-sm text-dark-400 font-bold uppercase tracking-wider mb-1">Zapísané kurzy</h2>
          <p class="text-3xl font-black text-white">{{ enrolledCount }}</p>
        </div>
      </div>
    </div>
    
    <!-- Completed Courses -->
    <div class="group bg-dark-900 border border-dark-800 hover:border-secondary-500/30 rounded-3xl p-6 transition-all duration-300 hover:bg-dark-800/50">
      <div class="flex items-center">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-secondary-500/10 to-orange-500/10 flex items-center justify-center mr-6 group-hover:scale-110 transition-transform duration-300">
           <CheckBadgeIcon class="w-10 h-10 text-secondary-400" />
        </div>
        <div>
          <h2 class="text-sm text-dark-400 font-bold uppercase tracking-wider mb-1">Dokončené</h2>
          <p class="text-3xl font-black text-white">{{ completedCount }}</p>
        </div>
      </div>
    </div>

    <!-- Certificates -->
    <div class="group bg-dark-900 border border-dark-800 hover:border-emerald-500/30 rounded-3xl p-6 transition-all duration-300 hover:bg-dark-800/50">
      <div class="flex items-center">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-500/10 to-green-500/10 flex items-center justify-center mr-6 group-hover:scale-110 transition-transform duration-300">
           <AcademicCapIcon class="w-10 h-10 text-emerald-400" />
        </div>
        <div>
          <h2 class="text-sm text-dark-400 font-bold uppercase tracking-wider mb-1">Certifikáty</h2>
          <p class="text-3xl font-black text-white">{{ certificatesCount }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { BookOpenIcon, CheckBadgeIcon, AcademicCapIcon } from '@heroicons/vue/24/outline'

defineProps({
  enrolledCount: {
    type: Number,
    required: true,
    default: 0
  },
  completedCount: {
    type: Number,
    required: true,
    default: 0
  },
  certificatesCount: {
    type: Number,
    required: true,
    default: 0
  }
})
</script>
