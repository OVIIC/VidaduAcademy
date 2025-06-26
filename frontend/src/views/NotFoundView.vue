<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
      <!-- 404 Icon -->
      <div class="mx-auto h-32 w-32 text-gray-400">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
      </div>

      <!-- Error Message -->
      <div class="space-y-4">
        <h1 class="text-6xl font-bold text-gray-900">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700">Page Not Found</h2>
        <p class="text-gray-600 max-w-sm mx-auto">
          Sorry, we couldn't find the page you're looking for. The page might have been moved, deleted, or you might have typed the wrong URL.
        </p>
      </div>

      <!-- Suggestions -->
      <div class="bg-white rounded-lg shadow-sm p-6 text-left">
        <h3 class="text-lg font-medium text-gray-900 mb-4">What can you do?</h3>
        <ul class="space-y-3 text-sm text-gray-600">
          <li class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Check the URL for typos and try again
          </li>
          <li class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Go back to the previous page using your browser's back button
          </li>
          <li class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Visit our homepage and navigate from there
          </li>
          <li class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Browse our available courses
          </li>
        </ul>
      </div>

      <!-- Navigation Links -->
      <div class="space-y-3">
        <router-link
          to="/"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
        >
          Go to Homepage
        </router-link>
        
        <router-link
          to="/courses"
          class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
        >
          Browse Courses
        </router-link>

        <div class="flex space-x-3">
          <router-link
            to="/dashboard"
            class="flex-1 flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
          >
            Dashboard
          </router-link>
          
          <router-link
            to="/my-courses"
            class="flex-1 flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
          >
            My Courses
          </router-link>
        </div>
      </div>

      <!-- Contact Support -->
      <div class="bg-gray-100 rounded-lg p-4">
        <div class="flex items-center justify-center">
          <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
          <div class="text-center">
            <p class="text-sm font-medium text-gray-900">Still need help?</p>
            <p class="text-xs text-gray-600 mt-1">
              Contact our support team if you believe this is an error.
            </p>
          </div>
        </div>
      </div>

      <!-- Fun fact -->
      <div class="text-center">
        <p class="text-xs text-gray-500">
          Fun fact: This 404 page is visited by {{ Math.floor(Math.random() * 100) + 1 }}% fewer people than our course pages! 😄
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFoundView'
}
</script>
