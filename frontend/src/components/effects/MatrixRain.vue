<template>
  <div class="matrix-rain-container">
    <canvas ref="matrixCanvas" class="matrix-canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const matrixCanvas = ref(null)
let animationId = null
let ctx = null
let drops = []

const props = defineProps({
  fontSize: {
    type: Number,
    default: 16
  },
  color: {
    type: String,
    default: '#7A65B4'
  },
  speed: {
    type: Number,
    default: 33
  }
})

const initMatrix = () => {
  console.log('🎬 Matrix Rain: Initializing...')
  const canvas = matrixCanvas.value
  if (!canvas) {
    console.error('❌ Matrix Rain: Canvas not found!')
    return
  }

  console.log('✅ Matrix Rain: Canvas found, setting up context...')
  ctx = canvas.getContext('2d')
  
  // Set canvas size
  const resizeCanvas = () => {
    canvas.width = canvas.offsetWidth
    canvas.height = canvas.offsetHeight
    console.log(`📐 Matrix Rain: Canvas resized to ${canvas.width}x${canvas.height}`)
    
    // Initialize drops array
    const columns = canvas.width / props.fontSize
    drops.length = 0
    for (let x = 0; x < columns; x++) {
      drops[x] = Math.random() * canvas.height
    }
    console.log(`💧 Matrix Rain: Created ${columns} drops`)
  }
  
  resizeCanvas()
  window.addEventListener('resize', resizeCanvas)
  
  // Start animation
  console.log('🎮 Matrix Rain: Starting animation...')
  animate()
}

const animate = () => {
  if (!ctx || !matrixCanvas.value) return
  
  const canvas = matrixCanvas.value
  
  // Create fade effect
  ctx.fillStyle = 'rgba(59, 49, 87, 0.08)' // #3B3157 with low opacity
  ctx.fillRect(0, 0, canvas.width, canvas.height)
  
  // Set text properties
  ctx.font = `${props.fontSize}px 'Courier New', monospace`
  
  // Draw characters
  for (let i = 0; i < drops.length; i++) {
    // Random character (mix of letters, numbers, and some coding chars)
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789<>{}[]()+=/*-_|\\:;.,?!@#$%^&*'
    const text = chars[Math.floor(Math.random() * chars.length)]
    
    // Add some variation in opacity for depth effect
    const alpha = Math.random() * 0.4 + 0.6 // Between 0.6 and 1
    const hexAlpha = Math.floor(alpha * 255).toString(16).padStart(2, '0')
    ctx.fillStyle = props.color + hexAlpha
    
    // Draw the character
    ctx.fillText(text, i * props.fontSize, drops[i] * props.fontSize)
    
    // Move drop down with variable speed
    if (drops[i] * props.fontSize > canvas.height && Math.random() > 0.975) {
      drops[i] = 0
    }
    drops[i] += 0.4 + Math.random() * 0.6 // Variable speed between 0.4 and 1
  }
  
  animationId = requestAnimationFrame(animate)
}

onMounted(() => {
  console.log('🚀 Matrix Rain: Component mounted!')
  setTimeout(initMatrix, 100) // Small delay to ensure canvas is rendered
})

onUnmounted(() => {
  if (animationId) {
    cancelAnimationFrame(animationId)
  }
  window.removeEventListener('resize', () => {})
})
</script>

<style scoped>
.matrix-rain-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  pointer-events: none;
  z-index: 1;
  /* Debug: temporary red background to see if container is there */
  background: rgba(255, 0, 0, 0.1);
}

.matrix-canvas {
  width: 100%;
  height: 100%;
  display: block;
}
</style>
