<template>
  <Popover class="relative">
    <PopoverButton class="relative w-full cursor-pointer rounded-xl bg-dark-800/50 backdrop-blur-sm py-3 pl-4 pr-10 text-left text-white shadow-lg border border-dark-700/50 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500/50 sm:text-sm transition-all duration-300 hover:bg-dark-800/80 hover:border-primary-500/30 hover:shadow-primary-500/10 group">
      <span class="block truncate font-medium text-gray-200 group-hover:text-white transition-colors">
        {{ displayLabel }}
      </span>
      <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
        <svg class="h-5 w-5 text-dark-400 group-hover:text-primary-400 transition-colors duration-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </span>
    </PopoverButton>

    <transition
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-1"
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
    >
      <PopoverPanel class="absolute z-50 mt-2 w-72 rounded-xl bg-dark-900/95 backdrop-blur-xl p-5 shadow-2xl ring-1 ring-black ring-opacity-5 border border-dark-700/50" v-slot="{ close }">
        <div class="mb-2 flex justify-between items-center">
            <span class="text-sm font-semibold text-white">Cenové rozpätie</span>
            <span class="text-xs text-primary-400 font-mono">€{{ localRange[0] }} - €{{ localRange[1] }}</span>
        </div>
        
        <div class="py-4 px-2">
            <PriceRangeSlider 
                v-model="localRange" 
                :min="min" 
                :max="max" 
                :step="step"
                @update:modelValue="onRangeChange"
            />
        </div>

        <div class="mt-2 flex justify-end items-center pt-2 border-t border-dark-700/50">
            <button 
                @click="reset"
                class="text-xs font-medium text-dark-400 hover:text-white transition-colors"
                v-if="isModified"
            >
                Resetovať filter
            </button>
        </div>
      </PopoverPanel>
    </transition>
  </Popover>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import PriceRangeSlider from './PriceRangeSlider.vue'

const props = defineProps({
  modelValue: {
    type: Object, // { min: number, max: number } or null
    default: null
  },
  min: {
    type: Number,
    default: 0
  },
  max: {
    type: Number,
    default: 200 // Reasonable default max price for courses
  },
  step: {
    type: Number,
    default: 5
  }
})

const emit = defineEmits(['update:modelValue'])

const localRange = ref([props.min, props.max])

// Initialize local range from prop if exists
watch(() => props.modelValue, (val) => {
    if (val && val.min !== null && val.max !== null) {
        localRange.value = [val.min, val.max]
    } else {
        localRange.value = [props.min, props.max]
    }
}, { immediate: true })

const displayLabel = computed(() => {
    if (props.modelValue && (props.modelValue.min !== null || props.modelValue.max !== null)) {
        const currentMin = props.modelValue.min ?? props.min
        const currentMax = props.modelValue.max ?? props.max
        return `€${currentMin} - €${currentMax}`
    }
    return 'Cena'
})

const isModified = computed(() => {
    return localRange.value[0] !== props.min || localRange.value[1] !== props.max
})

const onRangeChange = (val) => {
    // Val is [min, max] from slider
    emit('update:modelValue', { min: val[0], max: val[1] })
}

const reset = () => {
    localRange.value = [props.min, props.max]
    emit('update:modelValue', null)
}
</script>
