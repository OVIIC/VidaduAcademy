<template>
  <div class="relative w-full h-12 flex items-center select-none">
    <!-- Track -->
    <div class="absolute w-full h-1.5 bg-dark-700/50 rounded-full overflow-hidden">
      <!-- Active Range -->
      <div 
        class="absolute h-full bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full"
        :style="{
          left: `${(minValue - min) / (max - min) * 100}%`,
          right: `${100 - (maxValue - min) / (max - min) * 100}%`
        }"
      ></div>
    </div>

    <!-- Min Thumb -->
    <div 
      class="absolute w-5 h-5 bg-white rounded-full shadow-lg border-2 border-primary-500 cursor-grab active:cursor-grabbing hover:scale-110 transition-transform z-10 focus:outline-none focus:ring-2 focus:ring-primary-500/50"
      :style="{ left: `calc(${(minValue - min) / (max - min) * 100}% - 10px)` }"
      @mousedown="startDrag('min', $event)"
      @touchstart="startDrag('min', $event)"
    >
      <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-dark-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
        €{{ minValue }}
      </div>
    </div>

    <!-- Max Thumb -->
    <div 
      class="absolute w-5 h-5 bg-white rounded-full shadow-lg border-2 border-secondary-500 cursor-grab active:cursor-grabbing hover:scale-110 transition-transform z-20 focus:outline-none focus:ring-2 focus:ring-secondary-500/50"
      :style="{ left: `calc(${(maxValue - min) / (max - min) * 100}% - 10px)` }"
      @mousedown="startDrag('max', $event)"
      @touchstart="startDrag('max', $event)"
    >
      <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-dark-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
        €{{ maxValue }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, toRefs } from 'vue'

const props = defineProps({
  min: {
    type: Number,
    default: 0
  },
  max: {
    type: Number,
    default: 1000
  },
  modelValue: {
    type: Array,
    default: () => [0, 1000]
  },
  step: {
    type: Number,
    default: 10
  }
})

const emit = defineEmits(['update:modelValue'])

const { min, max, step } = toRefs(props)

// Local state for smooth dragging
const minValue = ref(props.modelValue[0])
const maxValue = ref(props.modelValue[1])

watch(() => props.modelValue, (newVal) => {
  minValue.value = newVal[0]
  maxValue.value = newVal[1]
})

const dragging = ref(null)

const startDrag = (thumb, event) => {
  event.preventDefault()
  dragging.value = thumb
  document.addEventListener('mousemove', onDrag)
  document.addEventListener('mouseup', stopDrag)
  document.addEventListener('touchmove', onDrag)
  document.addEventListener('touchend', stopDrag)
}

const onDrag = (event) => {
  const clientX = event.type.includes('mouse') ? event.clientX : event.touches[0].clientX
  const slider = event.target.closest('.relative').getBoundingClientRect()
  const percentage = Math.min(Math.max((clientX - slider.left) / slider.width, 0), 1)
  
  let rawValue = min.value + percentage * (max.value - min.value)
  // Snap to step
  let value = Math.round(rawValue / step.value) * step.value
  
  if (dragging.value === 'min') {
    value = Math.min(value, maxValue.value - step.value)
    value = Math.max(value, min.value)
    minValue.value = value
  } else {
    value = Math.max(value, minValue.value + step.value)
    value = Math.min(value, max.value)
    maxValue.value = value
  }
}

const stopDrag = () => {
  dragging.value = null
  emit('update:modelValue', [minValue.value, maxValue.value])
  document.removeEventListener('mousemove', onDrag)
  document.removeEventListener('mouseup', stopDrag)
  document.removeEventListener('touchmove', onDrag)
  document.removeEventListener('touchend', stopDrag)
}
</script>
