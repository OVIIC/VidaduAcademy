<template>
  <div :class="['relative inline-flex flex-col items-center justify-center w-full', className]" style="perspective: 1000px">
    <!-- Invisible placeholder to maintain container dimensions based on the largest text -->
    <span
      :class="['invisible inline-block text-center select-none', textClassName]"
      v-html="longestText"
    ></span>
    
    <!-- Rotating texts -->
    <div class="absolute inset-0 flex items-center justify-center w-full h-full" style="transform-style: preserve-3d">
      <Transition name="rotate" v-for="(text, index) in Array.isArray(texts) ? texts : []" :key="index">
        <div
          v-if="currentIndex === index"
          class="absolute inset-0 flex flex-col justify-center items-center w-full"
        >
          <div :class="['inline-flex flex-wrap justify-center w-full text-center', textClassName]">
            <span
              class="inline-block animate-phrase"
              v-html="text"
            ></span>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  texts: {
    type: Array,
    required: true,
  },
  interval: {
    type: Number,
    default: 3000,
  },
  className: {
    type: String,
    default: '',
  },
  textClassName: {
    type: String,
    default: '',
  },
})

const currentIndex = ref(0)
let timer = null

const longestText = computed(() => {
  return props.texts.reduce((a, b) => a.length > b.length ? a : b, '')
})

onMounted(() => {
  // Start the rotation interval
  timer = setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % props.texts.length
  }, props.interval)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<style scoped>
/* Phrase Animation */
.animate-phrase {
  display: inline-block;
  opacity: 0;
  transform: translateY(50%) rotateX(-90deg) scale(0.8);
  filter: blur(10px);
  animation: phrase-rotate-in 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

@keyframes phrase-rotate-in {
  0% {
    opacity: 0;
    transform: translateY(50%) rotateX(-90deg) scale(0.8);
    filter: blur(10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0) rotateX(0deg) scale(1);
    filter: blur(0px);
  }
}

/* 3D Rotate Transition Container (Handles leaving) */
.rotate-enter-active {
  /* Entrance is handled by individual word animations now */
  position: absolute;
  width: 100%;
}

.rotate-leave-active {
  transition: all 0.6s cubic-bezier(0.82, 0.08, 0.38, 0.87);
  position: absolute;
  width: 100%;
}

.rotate-enter-from {
  opacity: 1; /* Keep 1 so words can animate themselves */
}

.rotate-leave-to {
  opacity: 0;
  transform: translateY(-50%) rotateX(90deg) scale(0.9);
  filter: blur(10px);
}
</style>
