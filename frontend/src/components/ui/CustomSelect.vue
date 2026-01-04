<template>
  <Listbox as="div" :modelValue="modelValue" :multiple="multiple" @update:modelValue="$emit('update:modelValue', $event)">
    <div class="relative">
      <ListboxButton 
        class="relative w-full cursor-pointer rounded-xl bg-dark-800/50 backdrop-blur-sm py-3 pl-4 pr-10 text-left text-white shadow-lg border border-dark-700/50 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500/50 sm:text-sm transition-all duration-300 hover:bg-dark-800/80 hover:border-primary-500/30 hover:shadow-primary-500/10 group"
      >
        <span class="block truncate font-medium text-gray-200 group-hover:text-white transition-colors">{{ displayLabel }}</span>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
          <svg 
            class="h-5 w-5 text-dark-400 group-hover:text-primary-400 transition-colors duration-300" 
            viewBox="0 0 20 20" 
            fill="currentColor" 
            aria-hidden="true"
          >
            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </span>
      </ListboxButton>

      <transition
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-1"
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
      >
        <ListboxOptions class="absolute z-50 mt-2 max-h-60 w-full overflow-auto rounded-xl bg-dark-900/95 backdrop-blur-xl py-1 text-base shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm border border-dark-700/50 scrollbar-thin scrollbar-thumb-dark-700 scrollbar-track-transparent">
          <ListboxOption 
            as="template" 
            v-for="option in options" 
            :key="option.value" 
            :value="option.value" 
            v-slot="{ active, selected }"
          >
            <li 
              :class="[
                active ? 'bg-primary-500/10 text-white' : 'text-dark-300', 
                selected ? 'bg-primary-500/20 text-primary-200' : '',
                'relative cursor-pointer select-none py-3 pl-4 pr-4 transition-all duration-200 flex items-center gap-3'
              ]"
            >
              <!-- Checkbox (Visual only) -->
              <div 
                class="flex-shrink-0 w-5 h-5 rounded border flex items-center justify-center transition-colors duration-200"
                :class="[
                  selected ? 'bg-primary-500 border-primary-500' : 'border-dark-500 bg-dark-800/50',
                  active && !selected ? 'border-primary-400' : ''
                ]"
              >
                <svg v-if="selected" class="w-3.5 h-3.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>

              <span :class="[selected ? 'font-bold' : 'font-normal', 'block truncate flex-1']">
                {{ option.label }}
                <span v-if="option.count !== undefined" class="ml-1 text-xs opacity-60" :class="selected ? 'text-primary-300' : 'text-dark-500'">({{ option.count }})</span>
              </span>
              
              <!-- Active Highlight Bar -->
              <div v-if="active && !selected" class="absolute left-0 top-1/2 -translate-y-1/2 h-4 w-1 bg-primary-500 rounded-r-full"></div>
            </li>
          </ListboxOption>
        </ListboxOptions>
      </transition>
    </div>
  </Listbox>
</template>

<script setup>
import { computed } from 'vue'
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'

const props = defineProps({
  modelValue: {
    type: [String, Number, Array, null],
    default: ''
  },
  options: {
    type: Array,
    required: true,
  },
  placeholder: {
    type: String,
    default: 'Select option'
  },
  multiple: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])

const displayLabel = computed(() => {
  if (props.multiple && Array.isArray(props.modelValue) && props.modelValue.length > 0) {
    if (props.modelValue.length === 1) {
       const selected = props.options.find(opt => opt.value === props.modelValue[0])
       return selected ? selected.label : props.modelValue[0]
    }
    // Polish: Handle case where all options are selected? Or just "X items"
    return `${props.modelValue.length} vybrané` // 'selected' in Slovak
  }
  
  const selected = props.options.find(opt => opt.value === props.modelValue)
  return selected ? selected.label : props.placeholder
})
</script>
