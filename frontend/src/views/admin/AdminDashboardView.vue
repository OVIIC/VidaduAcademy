<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-white mb-6">Admin Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Stat Card 1 -->
      <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
        <h3 class="text-gray-400 text-sm font-medium uppercase mb-2">Total Users</h3>
        <p class="text-3xl font-bold text-white">1,234</p>
        <p class="text-green-500 text-sm mt-2 flex items-center">
          <span class="mr-1">↑</span> 12% from last month
        </p>
      </div>
      
      <!-- Stat Card 2 -->
      <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
        <h3 class="text-gray-400 text-sm font-medium uppercase mb-2">Active Courses</h3>
        <p class="text-3xl font-bold text-white">42</p>
        <p class="text-green-500 text-sm mt-2 flex items-center">
          <span class="mr-1">↑</span> 3 new this week
        </p>
      </div>
      
      <!-- Stat Card 3 -->
      <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
        <h3 class="text-gray-400 text-sm font-medium uppercase mb-2">Revenue</h3>
        <p class="text-3xl font-bold text-white">€12,450</p>
        <p class="text-green-500 text-sm mt-2 flex items-center">
          <span class="mr-1">↑</span> 8% from last month
        </p>
      </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
      <h2 class="text-xl font-bold text-white mb-4">Recent Activity</h2>
      <p class="text-gray-400">No recent activity logs found.</p>
    </div>
  </div>
</template>

<script setup>
// Placeholder logic
</script>
