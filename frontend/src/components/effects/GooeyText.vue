<template>
  <div :class="['relative', className]">
    <svg class="absolute h-0 w-0" aria-hidden="true" focusable="false">
      <defs>
        <filter id="threshold">
          <feColorMatrix
            in="SourceGraphic"
            type="matrix"
            values="1 0 0 0 0
                    0 1 0 0 0
                    0 0 1 0 0
                    0 0 0 255 -140"
          />
        </filter>
      </defs>
    </svg>

    <div
      class="flex items-center justify-center w-full relative"
      style="filter: url(#threshold)"
    >
      <!-- Invisible placeholder for layout sizing -->
      <span
        :class="['invisible inline-block text-center select-none', textClassName]"
        v-html="longestText"
      ></span>
      
      <!-- Actual animated texts -->
      <span
        ref="text1Ref"
        :class="['absolute inset-0 flex flex-col justify-center items-center select-none text-center', textClassName]"
      ></span>
      <span
        ref="text2Ref"
        :class="['absolute inset-0 flex flex-col justify-center items-center select-none text-center', textClassName]"
      ></span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'

const props = defineProps({
  texts: {
    type: Array,
    required: true,
  },
  morphTime: {
    type: Number,
    default: 1,
  },
  cooldownTime: {
    type: Number,
    default: 0.25,
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

const text1Ref = ref(null)
const text2Ref = ref(null)
let animationFrameId = null

const longestText = computed(() => {
  // Find the text with the max raw length or just assume 
  // they are relatively similar. Here we use length of the string.
  return props.texts.reduce((a, b) => a.length > b.length ? a : b, '')
})

onMounted(() => {
  let textIndex = props.texts.length - 1
  let time = new Date()
  let morph = 0
  let cooldown = props.cooldownTime

  if (text1Ref.value && text2Ref.value) {
    text1Ref.value.innerHTML = props.texts[textIndex % props.texts.length]
    text2Ref.value.innerHTML = props.texts[(textIndex + 1) % props.texts.length]
  }

  const setMorph = (fraction) => {
    if (text1Ref.value && text2Ref.value) {
      text2Ref.value.style.filter = `blur(${Math.min(8 / fraction - 8, 100)}px)`
      text2Ref.value.style.opacity = `${Math.pow(fraction, 0.4) * 100}%`
      text2Ref.value.style.transform = `scale(${0.9 + 0.1 * fraction}) translateY(${(1 - fraction) * 20}px)`

      let f1 = 1 - fraction
      text1Ref.value.style.filter = `blur(${Math.min(8 / f1 - 8, 100)}px)`
      text1Ref.value.style.opacity = `${Math.pow(f1, 0.4) * 100}%`
      text1Ref.value.style.transform = `scale(${0.9 + 0.1 * f1}) translateY(${-(1 - f1) * 20}px)`
    }
  }

  const doCooldown = () => {
    morph = 0
    if (text1Ref.value && text2Ref.value) {
      text2Ref.value.style.filter = ''
      text2Ref.value.style.opacity = '100%'
      text2Ref.value.style.transform = 'scale(1) translateY(0px)'
      text1Ref.value.style.filter = ''
      text1Ref.value.style.opacity = '0%'
      text1Ref.value.style.transform = 'scale(0.9) translateY(-20px)'
    }
  }

  const doMorph = () => {
    morph -= cooldown
    cooldown = 0
    let fraction = morph / props.morphTime

    if (fraction > 1) {
      cooldown = props.cooldownTime
      fraction = 1
    }

    setMorph(fraction)
  }

  function animate() {
    animationFrameId = requestAnimationFrame(animate)
    const newTime = new Date()
    const shouldIncrementIndex = cooldown > 0
    const dt = (newTime.getTime() - time.getTime()) / 1000
    time = newTime

    cooldown -= dt

    if (cooldown <= 0) {
      if (shouldIncrementIndex) {
        textIndex = (textIndex + 1) % props.texts.length
        if (text1Ref.value && text2Ref.value) {
          text1Ref.value.innerHTML = props.texts[textIndex % props.texts.length]
          text2Ref.value.innerHTML = props.texts[(textIndex + 1) % props.texts.length]
        }
      }
      doMorph()
    } else {
      doCooldown()
    }
  }

  animate()
})

onUnmounted(() => {
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId)
  }
})
</script>
