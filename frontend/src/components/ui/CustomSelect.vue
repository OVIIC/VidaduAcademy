<template>
  <Listbox as="div" :modelValue="modelValue" :multiple="multiple" @update:modelValue="$emit('update:modelValue', $event)">
    <div class="relative">
      <ListboxButton class="relative w-full cursor-default rounded-lg bg-dark-800 py-2.5 pl-3 pr-10 text-left text-white shadow-sm border border-dark-700 focus:outline-none focus:ring-2 focus:ring-primary-500 sm:text-sm transition-all duration-200 hover:bg-dark-700">
        <span class="block truncate">{{ displayLabel }}</span>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
          <svg class="h-5 w-5 text-dark-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </span>
      </ListboxButton>

      <transition
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <ListboxOptions class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-dark-800 py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm border border-dark-700">
          <ListboxOption 
            as="template" 
            v-for="option in options" 
            :key="option.value" 
            :value="option.value" 
            v-slot="{ active, selected }"
          >
            <li :class="[active ? 'bg-dark-700 text-white' : 'text-dark-300', 'relative cursor-default select-none py-2 pl-10 pr-4 transition-colors duration-150']">
              <span :class="[selected ? 'font-semibold text-white' : 'font-normal', 'block truncate']">
                {{ option.label }}
                <!-- Show count only for single select or if needed, maybe hiding it in multi select to avoid clutter? Keeping it for now. -->
                <span v-if="option.count !== undefined" class="ml-1 text-dark-500 text-xs">({{ option.count }})</span>
              </span>

              <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3 text-primary-500">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </span>
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
    return `${props.modelValue.length} checked`
  }
  
  const selected = props.options.find(opt => opt.value === props.modelValue)
  return selected ? selected.label : props.placeholder
})
</script>
