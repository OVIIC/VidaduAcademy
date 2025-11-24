<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-white mb-6">Instructor Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Stat Card 1 -->
      <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
        <h3 class="text-gray-400 text-sm font-medium uppercase mb-2">My Students</h3>
        <p class="text-3xl font-bold text-white">156</p>
        <p class="text-purple-500 text-sm mt-2 flex items-center">
          <span class="mr-1">↑</span> 5 new this week
        </p>
      </div>
      
      <!-- Stat Card 2 -->
      <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
        <h3 class="text-gray-400 text-sm font-medium uppercase mb-2">Course Rating</h3>
        <p class="text-3xl font-bold text-white">4.8</p>
        <div class="flex text-yellow-500 mt-2 text-sm">
          ★★★★★
        </div>
      </div>
      
      <!-- Stat Card 3 -->
      <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
        <h3 class="text-gray-400 text-sm font-medium uppercase mb-2">Total Earnings</h3>
        <p class="text-3xl font-bold text-white">€3,240</p>
        <p class="text-purple-500 text-sm mt-2 flex items-center">
          <span class="mr-1">↑</span> Pending payout: €450
        </p>
      </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
      <h2 class="text-xl font-bold text-white mb-4">My Courses</h2>
      <p class="text-gray-400">You haven't created any courses yet.</p>
      <button class="mt-4 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors">
        Create New Course
      </button>
    </div>
  </div>
</template>

<script setup>
// Placeholder logic
</script>
