<template>
  <div class="virtual-scroll-container" :style="{ height: containerHeight + 'px' }">
    <div
      class="virtual-scroll-viewport"
      :style="{ height: totalHeight + 'px' }"
      @scroll="onScroll"
      ref="viewport"
    >
      <div
        class="virtual-scroll-content"
        :style="{ transform: `translateY(${offsetY}px)` }"
      >
        <slot
          :visibleItems="visibleItems"
          :startIndex="startIndex"
          :endIndex="endIndex"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { throttle } from '@/utils/performance'

const props = defineProps({
  items: {
    type: Array,
    required: true
  },
  itemHeight: {
    type: Number,
    required: true
  },
  containerHeight: {
    type: Number,
    default: 400
  },
  overscan: {
    type: Number,
    default: 3
  }
})

const viewport = ref(null)
const scrollTop = ref(0)

const startIndex = computed(() => {
  const index = Math.floor(scrollTop.value / props.itemHeight) - props.overscan
  return Math.max(0, index)
})

const endIndex = computed(() => {
  const index = startIndex.value + Math.ceil(props.containerHeight / props.itemHeight) + props.overscan * 2
  return Math.min(props.items.length - 1, index)
})

const visibleItems = computed(() => {
  return props.items.slice(startIndex.value, endIndex.value + 1)
})

const offsetY = computed(() => startIndex.value * props.itemHeight)

const totalHeight = computed(() => props.items.length * props.itemHeight)

const onScroll = throttle((event) => {
  scrollTop.value = event.target.scrollTop
}, 16) // ~60fps

// Auto-adjust container height based on viewport
const adjustHeight = () => {
  if (viewport.value) {
    const rect = viewport.value.getBoundingClientRect()
    if (rect.height > 0 && rect.height !== props.containerHeight) {
      emit('heightChange', rect.height)
    }
  }
}

const emit = defineEmits(['heightChange'])

onMounted(() => {
  adjustHeight()
  window.addEventListener('resize', adjustHeight)
})

onUnmounted(() => {
  window.removeEventListener('resize', adjustHeight)
})
</script>

<style scoped>
.virtual-scroll-container {
  position: relative;
  overflow: hidden;
}

.virtual-scroll-viewport {
  overflow-y: auto;
  overflow-x: hidden;
  position: relative;
}

.virtual-scroll-content {
  position: relative;
}
</style>
