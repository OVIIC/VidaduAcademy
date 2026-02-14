<template>
  <div 
    class="bg-dark-900 rounded-2xl overflow-hidden shadow-lg border border-dark-800 hover:border-primary-500/50 hover:shadow-primary-500/10 h-full flex flex-col group transition-all duration-300"
    :class="[
      isSelected ? 'ring-2 ring-primary-500' : '',
      hoverEffect ? 'transform hover:-translate-y-1' : ''
    ]"
    @click="$emit('click', course)"
  >
    <!-- Course Thumbnail -->
    <div class="aspect-video overflow-hidden relative">
      <img 
        :src="course.thumbnail || '/images/placeholder.svg'"
        :alt="course.title"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
      />
      <!-- Price Badge -->
      <div class="absolute top-3 right-3 bg-black/70 backdrop-blur-sm text-white px-2 py-1 rounded-md text-xs font-bold border border-white/10">
        {{ course.price > 0 ? `€${course.price}` : 'Zadarmo' }}
      </div>
    </div>
    
    <!-- Course Info -->
    <div class="p-5 flex-grow flex flex-col">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-medium px-2 py-1 rounded-full bg-dark-800 text-dark-300 border border-dark-700 capitalize">
          {{ course.difficulty }}
        </span>
        <div class="flex items-center text-xs text-dark-400">
          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ course.duration }}
        </div>
      </div>

      <h3 class="font-bold text-lg mb-2 text-white line-clamp-2 group-hover:text-primary-400 transition-colors">{{ course.title }}</h3>
      
      <p class="text-dark-400 text-sm line-clamp-2 mb-4 flex-grow">{{ course.short_description || course.description }}</p>
      
      <div class="flex items-center justify-between mt-auto pt-4 border-t border-dark-800">
        <div class="flex items-center space-x-2">
          <div class="w-6 h-6 rounded-full bg-dark-800 flex items-center justify-center text-xs font-bold text-dark-300 border border-dark-700">
            {{ (course.instructor?.name || course.instructor || '').charAt(0) }}
          </div>
          <span class="text-xs text-dark-300 truncate max-w-[100px]">{{ course.instructor?.name || course.instructor }}</span>
        </div>
        <button 
          v-if="!hidePurchaseButton"
          @click.stop="$emit('purchase', course)"
          class="text-primary-400 hover:text-primary-300 text-sm font-medium transition-colors"
        >
          Kúpiť
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  course: {
    type: Object,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  },
  hoverEffect: {
    type: Boolean,
    default: true
  },
  hidePurchaseButton: {
    type: Boolean,
    default: false
  }
})

defineEmits(['click', 'purchase'])
</script>

<style scoped>
/* Line clamp utilities */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
