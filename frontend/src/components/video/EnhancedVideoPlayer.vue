// Enhanced YouTube Video Player Component
<template>
  <div class="relative">
    <!-- Enhanced Video Container -->
    <div class="aspect-video bg-black rounded-lg overflow-hidden relative group">
      <!-- YouTube IFrame with API -->
      <iframe
        ref="youtubePlayer"
        :src="enhancedEmbedUrl"
        class="w-full h-full"
        frameborder="0"
        allowfullscreen
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        title="Lesson Video"
      ></iframe>
      
      <!-- Custom Overlay Controls -->
      <div class="absolute inset-0 pointer-events-none">
        <!-- Progress Bar Overlay -->
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent p-4">
          <div class="flex items-center space-x-4 text-white">
            <!-- Custom Progress Bar -->
            <div class="flex-1 bg-white/20 rounded-full h-1">
              <div 
                class="bg-primary-500 h-1 rounded-full transition-all duration-300"
                :style="{ width: progressPercentage + '%' }"
              ></div>
            </div>
            
            <!-- Time Display -->
            <span class="text-sm font-medium">
              {{ formatTime(currentTime) }} / {{ formatTime(duration) }}
            </span>
          </div>
        </div>
        
        <!-- Custom Action Buttons -->
        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
          <div class="flex space-x-2">
            <!-- Bookmark Button -->
            <button
              @click="addBookmark"
              class="bg-black/50 hover:bg-black/70 text-white p-2 rounded-lg pointer-events-auto"
              title="Pridať záložku"
            >
              <BookmarkIcon class="w-4 h-4" />
            </button>
            
            <!-- Notes Button -->
            <button
              @click="openNotes"
              class="bg-black/50 hover:bg-black/70 text-white p-2 rounded-lg pointer-events-auto"
              title="Otvoriť poznámky"
            >
              <NotesIcon class="w-4 h-4" />
            </button>
            
            <!-- Fullscreen Button -->
            <button
              @click="toggleFullscreen"
              class="bg-black/50 hover:bg-black/70 text-white p-2 rounded-lg pointer-events-auto"
              title="Celá obrazovka"
            >
              <FullscreenIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Video Controls Panel -->
    <div class="mt-4 bg-white rounded-lg border p-4">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Video ovládanie</h3>
        <div class="flex items-center space-x-2">
          <!-- Auto-continue Toggle -->
          <label class="flex items-center">
            <input
              v-model="autoPlayNext"
              type="checkbox"
              class="mr-2"
            />
            <span class="text-sm">Auto-pokračovanie</span>
          </label>
        </div>
      </div>
      
      <!-- Bookmarks List -->
      <div v-if="bookmarks.length > 0" class="mb-4">
        <h4 class="text-sm font-medium text-gray-900 mb-2">Záložky</h4>
        <div class="space-y-2 max-h-32 overflow-y-auto">
          <div
            v-for="bookmark in bookmarks"
            :key="bookmark.id"
            class="flex items-center justify-between bg-gray-50 rounded p-2"
          >
            <button
              @click="seekToBookmark(bookmark.time)"
              class="flex-1 text-left text-sm hover:text-primary-600"
            >
              <span class="font-medium">{{ formatTime(bookmark.time) }}</span>
              <span class="text-gray-500 ml-2">{{ bookmark.title }}</span>
            </button>
            <button
              @click="removeBookmark(bookmark.id)"
              class="text-gray-400 hover:text-red-500 p-1"
            >
              <XIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
      
      <!-- Quick Actions -->
      <div class="flex space-x-2">
        <button
          @click="seekToBeginning"
          class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm"
        >
          Začiatok
        </button>
        <button
          @click="skip(-10)"
          class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm"
        >
          -10s
        </button>
        <button
          @click="skip(10)"
          class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm"
        >
          +10s
        </button>
        <button
          @click="togglePlayPause"
          class="px-3 py-1 bg-primary-100 hover:bg-primary-200 text-primary-700 rounded text-sm font-medium"
        >
          {{ isPlaying ? 'Pauza' : 'Prehrať' }}
        </button>
      </div>
    </div>
    
    <!-- Notes Panel (when open) -->
    <div v-if="showNotes" class="mt-4 bg-white rounded-lg border p-4">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Poznámky k videu</h3>
        <button
          @click="showNotes = false"
          class="text-gray-400 hover:text-gray-600"
        >
          <XIcon class="w-5 h-5" />
        </button>
      </div>
      
      <!-- Add Note Form -->
      <div class="mb-4 p-3 bg-gray-50 rounded">
        <div class="flex items-center space-x-2 mb-2">
          <span class="text-sm font-medium">Čas:</span>
          <span class="text-sm text-primary-600">{{ formatTime(currentTime) }}</span>
        </div>
        <textarea
          v-model="newNoteText"
          placeholder="Napíšte poznámku..."
          class="w-full p-2 border rounded resize-none"
          rows="2"
        ></textarea>
        <button
          @click="addNote"
          :disabled="!newNoteText.trim()"
          class="mt-2 px-3 py-1 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-300 text-white rounded text-sm"
        >
          Pridať poznámku
        </button>
      </div>
      
      <!-- Notes List -->
      <div class="space-y-3 max-h-64 overflow-y-auto">
        <div
          v-for="note in sortedNotes"
          :key="note.id"
          class="border-l-4 border-primary-500 pl-3 py-2"
        >
          <div class="flex items-center justify-between mb-1">
            <button
              @click="seekToTime(note.timestamp)"
              class="text-sm font-medium text-primary-600 hover:text-primary-700"
            >
              {{ formatTime(note.timestamp) }}
            </button>
            <button
              @click="removeNote(note.id)"
              class="text-gray-400 hover:text-red-500"
            >
              <XIcon class="w-4 h-4" />
            </button>
          </div>
          <p class="text-sm text-gray-700">{{ note.text }}</p>
          <p class="text-xs text-gray-500 mt-1">{{ formatDate(note.createdAt) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
  videoUrl: String,
  lessonId: [String, Number],
  courseSlug: String
})

const emit = defineEmits(['video-ended', 'progress-update'])

// Video state
const youtubePlayer = ref(null)
const isPlaying = ref(false)
const currentTime = ref(0)
const duration = ref(0)
const progressPercentage = ref(0)
const autoPlayNext = ref(true)

// UI state
const showNotes = ref(false)
const newNoteText = ref('')

// Data
const bookmarks = ref([])
const notes = ref([])

// Enhanced embed URL with API parameters
const enhancedEmbedUrl = computed(() => {
  if (!props.videoUrl) return ''
  
  const baseUrl = getEmbedUrl(props.videoUrl)
  const params = new URLSearchParams({
    enablejsapi: '1',
    origin: window.location.origin,
    rel: '0',
    modestbranding: '1',
    showinfo: '0'
  })
  
  return `${baseUrl}?${params.toString()}`
})

const sortedNotes = computed(() => {
  return [...notes.value].sort((a, b) => a.timestamp - b.timestamp)
})

// Video control functions
const togglePlayPause = () => {
  // Would use YouTube API here
  if (import.meta.env.DEV) console.log('Toggle play/pause')
}

const skip = (seconds) => {
  // Would use YouTube API here
  if (import.meta.env.DEV) console.log('Skip', seconds, 'seconds')
}

const seekToBeginning = () => {
  // Would use YouTube API here
  if (import.meta.env.DEV) console.log('Seek to beginning')
}

const seekToTime = (time) => {
  // Would use YouTube API here
  if (import.meta.env.DEV) console.log('Seek to', time)
}

const seekToBookmark = (time) => {
  seekToTime(time)
}

// Bookmark functions
const addBookmark = () => {
  const bookmark = {
    id: Date.now(),
    time: currentTime.value,
    title: `Záložka ${bookmarks.value.length + 1}`,
    lessonId: props.lessonId
  }
  
  bookmarks.value.push(bookmark)
  saveBookmarks()
}

const removeBookmark = (id) => {
  bookmarks.value = bookmarks.value.filter(b => b.id !== id)
  saveBookmarks()
}

// Notes functions
const openNotes = () => {
  showNotes.value = true
}

const addNote = () => {
  if (!newNoteText.value.trim()) return
  
  const note = {
    id: Date.now(),
    timestamp: currentTime.value,
    text: newNoteText.value.trim(),
    lessonId: props.lessonId,
    createdAt: new Date()
  }
  
  notes.value.push(note)
  newNoteText.value = ''
  saveNotes()
}

const removeNote = (id) => {
  notes.value = notes.value.filter(n => n.id !== id)
  saveNotes()
}

// Storage functions
const saveBookmarks = () => {
  localStorage.setItem('lesson-bookmarks', JSON.stringify(bookmarks.value))
}

const loadBookmarks = () => {
  const saved = localStorage.getItem('lesson-bookmarks')
  if (saved) {
    bookmarks.value = JSON.parse(saved).filter(b => b.lessonId === props.lessonId)
  }
}

const saveNotes = () => {
  localStorage.setItem('lesson-notes', JSON.stringify(notes.value))
}

const loadNotes = () => {
  const saved = localStorage.getItem('lesson-notes')
  if (saved) {
    notes.value = JSON.parse(saved).filter(n => n.lessonId === props.lessonId)
  }
}

// Utility functions
const formatTime = (seconds) => {
  if (!seconds || isNaN(seconds)) return '0:00'
  
  const mins = Math.floor(seconds / 60)
  const secs = Math.floor(seconds % 60)
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('sk-SK')
}

const getEmbedUrl = (url) => {
  // Same function as before
  if (url.includes('youtube.com/watch?v=')) {
    const videoId = url.split('v=')[1].split('&')[0]
    return `https://www.youtube.com/embed/${videoId}`
  }
  // ... other URL types
  return url
}

const toggleFullscreen = () => {
  if (youtubePlayer.value) {
    if (document.fullscreenElement) {
      document.exitFullscreen()
    } else {
      youtubePlayer.value.parentElement.requestFullscreen()
    }
  }
}

// Auto-save progress
watch([currentTime, duration], () => {
  if (duration.value > 0) {
    progressPercentage.value = (currentTime.value / duration.value) * 100
    
    // Save progress every 5 seconds
    if (Math.floor(currentTime.value) % 5 === 0) {
      const progressData = {
        lessonId: props.lessonId,
        currentTime: currentTime.value,
        duration: duration.value,
        progressPercentage: progressPercentage.value
      }
      
      localStorage.setItem(`lesson-progress-${props.lessonId}`, JSON.stringify(progressData))
      emit('progress-update', progressData)
    }
  }
})

onMounted(() => {
  loadBookmarks()
  loadNotes()
  
  // Load saved progress
  const savedProgress = localStorage.getItem(`lesson-progress-${props.lessonId}`)
  if (savedProgress) {
    const progress = JSON.parse(savedProgress)
    // Would seek to saved position using YouTube API
    if (import.meta.env.DEV) console.log('Restored progress:', progress)
  }
})
</script>
