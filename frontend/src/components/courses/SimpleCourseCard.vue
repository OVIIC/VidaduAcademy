<template>
  <div class="card bg-white border rounded-lg p-4 shadow-sm">
    <h3 class="text-lg font-semibold mb-2">{{ course.title }}</h3>
    <p class="text-gray-600 text-sm mb-2">{{ course.short_description }}</p>
    <div class="flex justify-between items-center">
      <span class="text-xl font-bold text-primary-600">{{ formatPrice(course.price) }}</span>
      <button class="bg-primary-600 text-white px-4 py-2 rounded">Zistiť viac</button>
    </div>
  </div>
</template>

<script setup>
import { formatPrice } from '@/utils/helpers'

defineProps({
  course: {
    type: Object,
    required: true,
  },
})
</script>
