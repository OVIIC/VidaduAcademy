<template>
  <section class="py-24 relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-20">
        <h2 class="text-3xl sm:text-4xl font-black text-white mb-6">
          Prečo práve VidaduAcademy?
        </h2>
        <p class="text-xl text-dark-300 font-extralight">
          Vidadu tím má za sebou 15 rokov práce s top tvorcami. Naše skúsenosti sme pretavili do praktických kurzov pre každého, kto to s tvorbou myslí vážne.
          Ponúkame ti kompletný ekosystém pre tvoj úspech.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
        <!-- Feature 1 -->
        <div class="group p-8 rounded-3xl bg-dark-900 border border-dark-800 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/50">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500/20 to-purple-500/20 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
            <TrophyIcon class="w-8 h-8 text-primary-400" />
          </div>
          <h3 class="text-2xl font-bold text-white mb-4">Viac ako 10 rokov v prvej línii</h3>
          <p class="text-dark-300 leading-relaxed font-extralight">
            Naše know-how nie je vyčítané z kníh, ale overené prácou s top SK/CZ tvorcami.
          </p>
        </div>

        <!-- Feature 2 -->
        <div class="group p-8 rounded-3xl bg-dark-900 border border-dark-800 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/50">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-cyan-500/20 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
            <RocketLaunchIcon class="w-8 h-8 text-blue-400" />
          </div>
          <h3 class="text-2xl font-bold text-white mb-4">Prax nad teóriu</h3>
          <p class="text-dark-300 leading-relaxed font-extralight">
            Žiadna vata. Dostaneš len funkčné stratégie, ktoré môžeš okamžite použiť.
          </p>
        </div>

        <!-- Feature 3 -->
        <div class="group p-8 rounded-3xl bg-dark-900 border border-dark-800 hover:border-primary-500/30 transition-all duration-300 hover:bg-dark-800/50">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
            <CubeTransparentIcon class="w-8 h-8 text-emerald-400" />
          </div>
          <h3 class="text-2xl font-bold text-white mb-4">Kompletný ekosystém</h3>
          <p class="text-dark-300 leading-relaxed font-extralight">
            Viac než len kurzy a osobné konzultácie. Získaš zázemie, ktoré potrebuješ, ak to s tvorbou myslíš naozaj vážne.
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { TrophyIcon, RocketLaunchIcon, CubeTransparentIcon } from '@heroicons/vue/24/outline'
</script>
