<template>
  <div class="min-h-screen bg-dark-950 text-white font-sans selection:bg-primary-500 selection:text-white">


    <div class="relative z-10">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
      </div>

      <!-- Course Content -->
      <div v-else-if="course" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Course Header -->
        <div class="bg-dark-900 border border-dark-800 rounded-3xl p-6 mb-8 hover:border-primary-500/30 transition-all duration-300">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <nav class="text-sm text-dark-400 mb-2 font-medium">
                <router-link to="/my-courses" class="hover:text-primary-500 transition-colors">Moje kurzy</router-link>
                <span class="mx-2 text-dark-600">/</span>
                <span class="text-white">{{ course.title }}</span>
              </nav>
              
              <h1 class="text-3xl font-black text-white mb-4 tracking-tight">{{ course.title }}</h1>
              
              <div class="flex items-center space-x-6 text-sm text-dark-300">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                  <span class="font-medium">{{ course.instructor?.name }}</span>
                </div>
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="font-medium">{{ formatDuration(course.duration_minutes) }}</span>
                </div>
                <div class="flex items-center">
                  <span :class="getDifficultyBadgeClass(course.difficulty_level)">
                    {{ formatDifficulty(course.difficulty_level) }}
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Progress Circle -->
            <div class="flex-shrink-0 ml-6 hidden sm:block">
              <div class="relative w-24 h-24">
                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                  <path
                    class="text-dark-800"
                    stroke="currentColor"
                    stroke-width="3"
                    fill="none"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  />
                  <path
                    class="text-primary-500"
                    stroke="currentColor"
                    stroke-width="3"
                    stroke-linecap="round"
                    fill="none"
                    :stroke-dasharray="`${progressPercentage}, 100`"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                  <span class="text-xl font-bold text-white">{{ Math.round(progressPercentage) }}%</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Course Content (Left Column) -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Selected Lesson Content -->
            <div v-if="selectedLesson" ref="lessonContentRef" class="bg-dark-900 border border-dark-800 rounded-3xl shadow-lg overflow-hidden transition-all duration-300 hover:border-dark-700">
              <!-- Lesson Header -->
              <div class="bg-gradient-to-r from-primary-600 to-secondary-600 px-8 py-5 text-white">
                <div class="flex items-center justify-between">
                  <div>
                    <h2 class="text-xl font-bold">{{ selectedLesson.title }}</h2>
                    <p class="text-white/80 mt-1 font-medium">Lekcia {{ selectedLessonIndex + 1 }} z {{ lessons.length }}</p>
                  </div>
                  <div class="flex items-center space-x-3">
                    <button
                      v-if="selectedLessonIndex > 0"
                      @click="selectLesson(lessons[selectedLessonIndex - 1])"
                      :disabled="lessonSwitching"
                      class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed backdrop-blur-sm"
                      title="Predchádzajúca lekcia"
                    >
                      <div v-if="lessonSwitching" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                      </svg>
                    </button>
                    <button
                      v-if="selectedLessonIndex < lessons.length - 1"
                      @click="selectLesson(lessons[selectedLessonIndex + 1])"
                      :disabled="lessonSwitching"
                      class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed backdrop-blur-sm"
                      title="Ďalšia lekcia"
                    >
                      <div v-if="lessonSwitching" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Basic Video Player -->
              <div v-if="selectedLesson.video_url" class="bg-black">
                <div class="aspect-video w-full">
                  <iframe
                    :src="getEmbedUrl(selectedLesson.video_url)"
                    class="w-full h-full"
                    frameborder="0"
                    allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    title="Lesson Video"
                  ></iframe>
                </div>
              </div>
              
              <!-- Lesson Content Body -->
              <div class="p-8">
                <div v-if="selectedLesson.description" class="mb-8">
                  <h3 class="text-lg font-bold text-white mb-3">Popis lekcie</h3>
                  <p class="text-dark-300 leading-relaxed font-light">{{ selectedLesson.description }}</p>
                </div>

                <div v-if="selectedLesson.content" class="mb-8">
                  <h3 class="text-lg font-bold text-white mb-3">Obsah lekcie</h3>
                  <div class="prose max-w-none text-dark-300" v-html="selectedLesson.content"></div>
                </div>

                <!-- Lesson Resources -->
                <div v-if="selectedLesson.resources && selectedLesson.resources.length > 0" class="mb-8">
                  <h3 class="text-lg font-bold text-white mb-4">Zdroje a súbory</h3>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a
                      v-for="resource in selectedLesson.resources"
                      :key="resource.url"
                      :href="resource.url"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="group flex items-center p-4 bg-dark-800/50 border border-dark-700 rounded-xl hover:border-primary-500/50 hover:bg-dark-800 transition-all duration-200"
                    >
                      <div class="flex-shrink-0 mr-4">
                        <div class="w-10 h-10 bg-dark-700/50 rounded-lg flex items-center justify-center group-hover:bg-primary-500/10 transition-colors">
                          <svg v-if="resource.type === 'pdf'" class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                          </svg>
                          <svg v-else-if="resource.type === 'video'" class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                          </svg>
                          <svg v-else class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate group-hover:text-primary-400 transition-colors">{{ resource.title }}</p>
                        <p v-if="resource.description" class="text-xs text-dark-400 truncate mt-0.5">{{ resource.description }}</p>
                        <p class="text-xs text-primary-500 font-bold uppercase tracking-wider mt-1">{{ getResourceTypeLabel(resource.type) }}</p>
                      </div>
                      <div class="flex-shrink-0 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                      </div>
                    </a>
                  </div>
                </div>

                <!-- Lesson Transcript -->
                <div v-if="selectedLesson.transcript" class="mb-6">
                  <h3 class="text-lg font-bold text-white mb-3">Prepis videa</h3>
                  <div class="bg-dark-800/50 rounded-xl p-6 border border-dark-700/50">
                    <p class="text-sm text-dark-300 whitespace-pre-wrap font-light leading-relaxed">{{ selectedLesson.transcript }}</p>
                  </div>
                </div>

                <!-- Lesson Actions -->
                <div class="flex items-center justify-between pt-8 border-t border-dark-700/50">
                  <button
                    @click="toggleLessonCompletion(selectedLesson)"
                    :class="[
                      'flex items-center px-6 py-3 rounded-xl font-bold transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg',
                      selectedLesson.completed
                        ? 'bg-green-500/10 text-green-500 hover:bg-green-500/20 border border-green-500/20'
                        : 'bg-primary-600 hover:bg-primary-500 text-white hover:shadow-primary-500/25'
                    ]"
                  >
                    <svg v-if="selectedLesson.completed" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ selectedLesson.completed ? 'Označiť ako nedokončené' : 'Označiť ako dokončené' }}
                  </button>

                  <div class="flex items-center space-x-3">
                    <button
                      v-if="selectedLessonIndex > 0"
                      @click="selectLesson(lessons[selectedLessonIndex - 1])"
                      :disabled="lessonSwitching"
                      class="px-5 py-3 border border-dark-600 text-dark-300 rounded-xl hover:bg-dark-800 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                    >
                      <span v-if="lessonSwitching" class="flex items-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-dark-300 mr-2"></div>
                        Načítava...
                      </span>
                      <span v-else>Predchádzajúca</span>
                    </button>
                    <button
                      v-if="selectedLessonIndex < lessons.length - 1"
                      @click="selectLesson(lessons[selectedLessonIndex + 1])"
                      :disabled="lessonSwitching"
                      class="px-6 py-3 bg-dark-800 hover:bg-dark-700 text-white rounded-xl border border-dark-700 hover:border-dark-600 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed font-bold"
                    >
                      <span v-if="lessonSwitching" class="flex items-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        Načítava...
                      </span>
                      <span v-else>Ďalšia lekcia</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Course Overview (when no lesson selected) -->
            <div v-if="!selectedLesson" class="bg-dark-900 border border-dark-800 rounded-3xl p-8 hover:border-dark-700 transition-all duration-300">
              <h2 class="text-2xl font-black text-white mb-6">Prehľad kurzu</h2>
              <p class="text-dark-300 mb-8 leading-relaxed font-light">{{ course.description }}</p>
              
              <!-- What you will learn -->
              <div v-if="course.what_you_will_learn && course.what_you_will_learn.length > 0" class="bg-dark-800/30 rounded-2xl p-6 border border-dark-700/50">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                  <svg class="w-5 h-5 text-primary-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  Čo sa naučíte
                </h3>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <li v-for="item in course.what_you_will_learn" :key="item" class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-dark-300 font-light">{{ item }}</span>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Course Lessons List -->
            <div class="bg-dark-900 border border-dark-800 rounded-3xl p-8 hover:border-dark-700 transition-all duration-300">
              <h2 class="text-2xl font-black text-white mb-6">Obsah kurzu</h2>
              
              <div v-if="lessons && lessons.length > 0" class="space-y-4">
                <div
                  v-for="(lesson, index) in lessons"
                  :key="lesson.id"
                  class="group border rounded-2xl p-5 hover:bg-dark-800 transition-all duration-300 cursor-pointer relative overflow-hidden"
                  @click="selectLesson(lesson)"
                  :class="{
                    'border-primary-500/50 bg-primary-900/10 hover:bg-primary-900/20': selectedLesson?.id === lesson.id,
                    'border-dark-700/50 bg-dark-800/30': selectedLesson?.id !== lesson.id,
                    'opacity-50 pointer-events-none': lessonSwitching
                  }"
                >
                  <!-- Loading overlay when switching -->
                  <div v-if="lessonSwitching && selectedLesson?.id === lesson.id" class="absolute inset-0 bg-dark-900/80 flex items-center justify-center rounded-2xl z-20">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                  </div>
                  
                  <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center">
                      <div 
                        class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center mr-4 transition-colors duration-300"
                        :class="selectedLesson?.id === lesson.id ? 'bg-primary-500 text-white' : 'bg-dark-700 text-dark-300 group-hover:bg-dark-600'"
                      >
                        <span class="text-sm font-bold">{{ index + 1 }}</span>
                      </div>
                      <div>
                        <h3 class="font-bold text-white mb-1 group-hover:text-primary-400 transition-colors">{{ lesson.title }}</h3>
                        <p class="text-sm text-dark-400 font-light">{{ lesson.description || 'Popis lekcie' }}</p>
                      </div>
                    </div>
                    <div class="flex items-center space-x-4">
                      <span class="text-sm text-dark-400 font-medium">{{ formatDuration(lesson.duration_minutes || 15) }}</span>
                      <div class="w-6 h-6 rounded-full border border-dark-600 flex items-center justify-center">
                        <svg v-if="lesson.completed" class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- No lessons fallback -->
              <div v-else class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-dark-800 flex items-center justify-center">
                  <svg class="h-8 w-8 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-white">Lekcie budú čoskoro dostupné</h3>
                <p class="mt-2 text-dark-400">Momentálne tento kurz neobsahuje žiadne lekcie.</p>
              </div>
            </div>
          </div>

          <!-- Sidebar (Right Column) -->
          <div class="lg:col-span-1 space-y-6">
            <!-- Mobile Progress (Visible only on small screens) -->
            <div class="sm:hidden bg-dark-900 border border-dark-800 rounded-3xl p-6">
              <h3 class="text-lg font-bold text-white mb-4">Môj pokrok</h3>
              <div class="flex items-center space-x-4">
                 <div class="relative w-16 h-16 flex-shrink-0">
                  <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                    <path
                      class="text-dark-800"
                      stroke="currentColor"
                      stroke-width="3"
                      fill="none"
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <path
                      class="text-primary-500"
                      stroke="currentColor"
                      stroke-width="3"
                      stroke-linecap="round"
                      fill="none"
                      :stroke-dasharray="`${progressPercentage}, 100`"
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                  </svg>
                  <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm font-bold text-white">{{ Math.round(progressPercentage) }}%</span>
                  </div>
                </div>
                <div>
                  <div class="text-sm text-dark-400">Dokončených lekcií</div>
                  <div class="text-xl font-bold text-white">{{ completedLessons }} <span class="text-dark-500 text-sm font-normal">/ {{ totalLessons }}</span></div>
                </div>
              </div>
            </div>

            <!-- Course Stats -->
            <div class="bg-dark-900 border border-dark-800 rounded-3xl p-6 hover:border-dark-700 transition-all duration-300">
              <h3 class="text-lg font-bold text-white mb-6 flex items-center">
                <svg class="w-5 h-5 text-primary-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Štatistiky kurzu
              </h3>
              <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-dark-800/50 rounded-xl">
                  <span class="text-dark-400 text-sm font-medium">Pokrok</span>
                  <span class="font-bold text-white">{{ Math.round(progressPercentage) }}%</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-dark-800/50 rounded-xl">
                  <span class="text-dark-400 text-sm font-medium">Dokončené lekcie</span>
                  <span class="font-bold text-white">{{ completedLessons }}<span class="text-dark-500">/{{ totalLessons }}</span></span>
                </div>
                <div class="flex justify-between items-center p-3 bg-dark-800/50 rounded-xl">
                  <span class="text-dark-400 text-sm font-medium">Zapísaný</span>
                  <span class="font-bold text-white">{{ formatEnrollmentDate(enrollmentDate) }}</span>
                </div>
              </div>
            </div>

            <!-- Continue Learning Button -->
            <div class="bg-dark-900 border border-dark-800 rounded-3xl p-6 hover:border-dark-700 transition-all duration-300">
              <h3 class="text-lg font-bold text-white mb-4">Akcia</h3>
              <button
                v-if="nextLesson"
                @click="startLesson(nextLesson)"
                class="w-full bg-primary-600 hover:bg-primary-500 text-white font-bold py-4 px-4 rounded-xl transition-all duration-200 shadow-lg hover:shadow-primary-500/25 flex items-center justify-center group"
              >
                <span class="mr-2">{{ progressPercentage === 0 ? 'Začať kurz' : 'Pokračovať v učení' }}</span>
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
              </button>
              <div v-else class="text-center py-4">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-500/10 rounded-full flex items-center justify-center">
                  <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Kurz je dokončený!</h3>
                <p class="text-dark-400 mb-6 text-sm">Gratulujeme k úspešnému absolvovaniu kurzu.</p>
                <button
                  @click="downloadCertificate"
                  class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-green-500/25"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Stiahnuť certifikát
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="flex flex-col items-center justify-center h-96 text-center">
        <div class="w-20 h-20 bg-dark-800 rounded-full flex items-center justify-center mb-6">
          <svg class="w-10 h-10 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Kurz sa nenašiel</h3>
        <p class="text-dark-400 mb-8">Požadovaný kurz neexistuje alebo k nemu nemáte prístup.</p>
        <router-link 
          to="/my-courses"
          class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-primary-600 hover:bg-primary-500 transition-all shadow-lg hover:shadow-primary-500/25"
        >
          Späť na moje kurzy
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'
import { learningService, authService } from '@/services'
import { useEnrollmentStore } from '@/stores/enrollment'

const props = defineProps({
  slug: {
    type: String,
    default: ''
  }
})

const route = useRoute()
const toast = useToast()

const downloadCertificate = async () => {
  if (!course.value?.enrollment?.id) return
  
  try {
    const response = await authService.getCertificate(course.value.enrollment.id)
    const win = window.open('', '_blank')
    if (win) {
      win.document.write(response)
      win.document.close()
    }
  } catch (error) {
    console.error('Error downloading certificate:', error)
    toast.error('Nepodarilo sa stiahnuť certifikát. Skúste to prosím neskôr.')
  }
}

const loading = ref(false)
const course = ref(null)
const lessons = ref([])
const selectedLesson = ref(null)
const lessonContentRef = ref(null)
const lessonSwitching = ref(false)

const progressPercentage = computed(() => {
  // Try to get progress from course enrollment data or calculate from lessons
  const enrollmentProgress = course.value?.progress_percentage || course.value?.enrollment?.progress_percentage
  
  if (enrollmentProgress !== undefined) {
    return enrollmentProgress
  }
  
  // Fallback: calculate from completed lessons
  const completed = completedLessons.value
  const total = totalLessons.value
  return total > 0 ? Math.round((completed / total) * 100) : 0
})

const completedLessons = computed(() => {
  return lessons.value.filter(lesson => lesson.completed).length
})

const totalLessons = computed(() => {
  return lessons.value.length || 1
})

const enrollmentDate = computed(() => {
  return course.value?.enrollment_data?.enrolled_at
})

const nextLesson = computed(() => {
  return lessons.value.find(lesson => !lesson.completed) || lessons.value[0]
})

const selectedLessonIndex = computed(() => {
  return selectedLesson.value ? lessons.value.findIndex(lesson => lesson.id === selectedLesson.value.id) : -1
})


const loadCourseContent = async () => {
  loading.value = true
  try {
    const courseSlug = props.slug || route?.params?.slug
    if (import.meta.env.DEV) console.log('Loading course content for slug:', courseSlug)
    
    if (!courseSlug) {
      throw new Error('Course slug is missing')
    }

    const response = await learningService.getCourseContent(courseSlug)
    if (import.meta.env.DEV) console.log('Course content response:', response)
    
    course.value = response.course || response
    lessons.value = response.lessons || []
    
    // Store enrollment data in course for progress tracking
    if (response.enrollment) {
      course.value.enrollment = response.enrollment
      course.value.progress_percentage = response.enrollment.progress_percentage
    }
    
    // Convert video_duration from seconds to minutes for display and map progress data
    if (lessons.value.length > 0) {
      lessons.value = lessons.value.map(lesson => ({
        ...lesson,
        duration_minutes: lesson.video_duration ? Math.ceil(lesson.video_duration / 60) : 15,
        completed: lesson.user_progress?.completed || false,
        progress_percentage: lesson.user_progress?.progress_percentage || 0,
        watch_time_seconds: lesson.user_progress?.watch_time_seconds || 0
      }))
    }
    

  } catch (error) {
    console.error('Error loading course content:', error)
    course.value = null
  } finally {
    loading.value = false
  }
}

const selectLesson = (lesson) => {
  // Don't change lesson if already switching
  if (lessonSwitching.value) return
  
  lessonSwitching.value = true
  selectedLesson.value = lesson
  if (import.meta.env.DEV) console.log('Selected lesson:', lesson.title)
  
  // Scroll to lesson content after a small delay to ensure DOM update
  nextTick(() => {
    if (lessonContentRef.value) {
      lessonContentRef.value.scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
      })
      
      // Reset switching flag after scroll animation
      setTimeout(() => {
        lessonSwitching.value = false
      }, 1000)
    } else {
      // Fallback if ref not found (e.g. unexpected structure)
      window.scrollTo({ top: 0, behavior: 'smooth' })
      lessonSwitching.value = false
    }
  })
}

const startLesson = (lesson) => {
  if (import.meta.env.DEV) console.log('Starting lesson:', lesson.title)
  selectLesson(lesson)
}

const formatDuration = (minutes) => {
  if (!minutes) return '0min'
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  
  if (hours > 0) {
    return `${hours}h ${mins}min`
  }
  return `${mins}min`
}

const formatDifficulty = (level) => {
  const levels = {
    beginner: 'Začiatočník',
    intermediate: 'Pokročilý',
    advanced: 'Expert'
  }
  return levels[level] || level
}

const getDifficultyBadgeClass = (level) => {
  const classes = {
    beginner: 'px-3 py-1 rounded-full text-xs font-bold bg-green-500/10 text-green-500 border border-green-500/20',
    intermediate: 'px-3 py-1 rounded-full text-xs font-bold bg-yellow-500/10 text-yellow-500 border border-yellow-500/20',
    advanced: 'px-3 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-500 border border-red-500/20'
  }
  return classes[level] || 'px-3 py-1 rounded-full text-xs font-bold bg-dark-700 text-dark-300'
}

const formatEnrollmentDate = (dateString) => {
  if (!dateString) return 'N/A'
  
  const date = new Date(dateString)
  return date.toLocaleDateString('sk-SK', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getEmbedUrl = (url) => {
  if (!url) return ''
  
  // YouTube URL conversion
  if (url.includes('youtube.com/watch?v=')) {
    const videoId = url.split('v=')[1].split('&')[0]
    return `https://www.youtube.com/embed/${videoId}`
  }
  
  if (url.includes('youtu.be/')) {
    const videoId = url.split('youtu.be/')[1].split('?')[0]
    return `https://www.youtube.com/embed/${videoId}`
  }
  
  // Vimeo URL conversion
  if (url.includes('vimeo.com/')) {
    const videoId = url.split('vimeo.com/')[1]
    return `https://player.vimeo.com/video/${videoId}`
  }
  
  // Direct video URLs
  return url
}

const getResourceTypeLabel = (type) => {
  const labels = {
    pdf: 'PDF Dokument',
    link: 'Webový odkaz',
    download: 'Stiahnutie',
    video: 'Video',
    image: 'Obrázok',
    document: 'Dokument',
    external: 'Externý zdroj'
  }
  return labels[type] || type
}

const toggleLessonCompletion = async (lesson) => {
  try {
    const wasCompleted = lesson.completed
    const newCompletionStatus = !wasCompleted
    
    // Optimistically update UI
    lesson.completed = newCompletionStatus
    
    // Call API to update lesson completion status
    const progressData = {
      completed: newCompletionStatus,
      watch_time_seconds: lesson.watch_time_seconds || 0
    }
    
    const response = await learningService.updateProgress(
      course.value.slug,
      lesson.slug,
      progressData
    )

    
    if (import.meta.env.DEV) {
      console.log(`Lesson ${lesson.title} marked as ${newCompletionStatus ? 'completed' : 'incomplete'}`)
      console.log('Progress update response:', response)
    }
    
    // Update lesson with fresh data from server
    if (response.progress) {
      lesson.completed = response.progress.completed
      lesson.watch_time_seconds = response.progress.watch_time_seconds
      lesson.progress_percentage = response.progress.progress_percentage
    }
    
    // Update course progress if enrollment data is returned
    if (response.enrollment) {
      course.value.progress_percentage = response.enrollment.progress_percentage
    }

    // Update global store state
    const enrollmentStore = useEnrollmentStore()
    enrollmentStore.updateCourseProgress({
      id: course.value.id,
      title: course.value.title,
      slug: course.value.slug,
      enrollment_data: {
        progress_percentage: course.value.progress_percentage
      }
    })
    
  } catch (error) {
    console.error('Error updating lesson completion:', error)
    // Revert the change if API call fails
    lesson.completed = !lesson.completed
    
    // Show error message to user
    toast.error('Nastala chyba pri aktualizácii pokroku lekcie. Skúste to znovu.')
  }
}

// Add keyboard navigation support for lesson switching
const handleKeyDown = (event) => {
  // Only handle keys if not typing in input field
  if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') return
  
  switch (event.key) {
    case 'ArrowLeft':
      event.preventDefault()
      if (selectedLessonIndex.value > 0) {
        selectLesson(lessons.value[selectedLessonIndex.value - 1])
      }
      break
    case 'ArrowRight':
      event.preventDefault()
      if (selectedLessonIndex.value < lessons.value.length - 1) {
        selectLesson(lessons.value[selectedLessonIndex.value + 1])
      }
      break
  }
}



onMounted(() => {
  loadCourseContent()
  document.addEventListener('keydown', handleKeyDown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyDown)
})
</script>

<style scoped>
/* Animations */
@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.2; transform: scale(1.1); }
}

.animate-pulse-slow {
  animation: pulse-slow 8s infinite ease-in-out;
}

.delay-500 { animation-delay: 0.5s; }
.delay-1000 { animation-delay: 1s; }

/* Override prose typography for dark theme */
:deep(.prose) {
  color: #d1d5db; /* text-gray-300 */
}

:deep(.prose h1),
:deep(.prose h2),
:deep(.prose h3),
:deep(.prose h4),
:deep(.prose h5),
:deep(.prose h6) {
  color: #ffffff; /* text-white */
  font-weight: 700;
}

:deep(.prose strong) {
  color: #ffffff; /* text-white */
  font-weight: 600;
}

:deep(.prose p),
:deep(.prose li) {
  color: #d1d5db; /* text-gray-300 - keeping this for readability */
  font-weight: 300; /* light font weight */
  line-height: 1.7;
}

:deep(.prose a) {
  color: #ED6F55; /* primary-500 */
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

:deep(.prose a:hover) {
  color: #E85C40; /* primary-600 */
  text-decoration: underline;
}

:deep(.prose code) {
  color: #ED6F55; /* primary-500 */
  background-color: rgba(237, 111, 85, 0.1);
  padding: 0.1em 0.3em;
  border-radius: 0.25rem;
}

:deep(.prose blockquote) {
  border-left-color: #ED6F55;
  color: #9ca3af;
  background-color: rgba(17, 24, 39, 0.5); /* bg-dark-900/50 */
  padding: 1rem;
  border-radius: 0.5rem;
}

:deep(.prose ul > li::marker) {
  color: #6b7280; /* text-gray-500 */
}

:deep(.prose ol > li::marker) {
  color: #d1d5db; /* text-gray-300 */
  font-weight: 500;
}
</style>
