<template>
  <div class="min-h-screen bg-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
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
        <h2 class="text-2xl font-semibold text-gray-700">Stránka sa nenašla</h2>
        <p class="text-gray-600 max-w-sm mx-auto">
          Prepáčte, nemohli sme nájsť stránku, ktorú hľadáte. Stránka možno bola presunutá, vymazaná, alebo ste zadali nesprávnu URL adresu.
        </p>
      </div>

      <!-- Suggestions -->
      <div class="bg-white rounded-lg shadow-sm p-6 text-left">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Čo môžete urobiť?</h3>
        <ul class="space-y-3 text-sm text-gray-600">
          <li class="flex items-start">
            <svg class="w-5 h-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Skontrolujte URL adresu a skúste znova
          </li>
          <li class="flex items-start">
            <svg class="w-5 h-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Vráťte sa na predchádzajúcu stránku pomocou tlačidla späť
          </li>
          <li class="flex items-start">
            <svg class="w-5 h-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Navštívte našu domovskú stránku a navigujte odtiaľ
          </li>
          <li class="flex items-start">
            <svg class="w-5 h-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Preskúmajte naše dostupné kurzy
          </li>
        </ul>
      </div>

      <!-- Navigation Links -->
      <div class="space-y-3">
        <router-link
          to="/"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-200"
        >
          Ísť na domovskú stránku
        </router-link>
        
        <router-link
          to="/courses"
          class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-200"
        >
          Prehliadať kurzy
        </router-link>

        <div class="flex space-x-3">
          <router-link
            to="/dashboard"
            class="flex-1 flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-200"
          >
            Nástenka
          </router-link>
          
          <router-link
            to="/my-courses"
            class="flex-1 flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-200"
          >
            Moje kurzy
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
            <p class="text-sm font-medium text-gray-900">Stále potrebujete pomoc?</p>
            <p class="text-xs text-gray-600 mt-1">
              Kontaktujte náš tím podpory, ak si myslíte, že ide o chybu.
            </p>
          </div>
        </div>
      </div>

      <!-- Fun fact -->
      <div class="text-center">
        <p class="text-xs text-gray-500">
          Zaujímavosť: Túto 404 stránku navštívi o {{ Math.floor(Math.random() * 100) + 1 }}% menej ľudí ako naše kurzové stránky! 😄
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
