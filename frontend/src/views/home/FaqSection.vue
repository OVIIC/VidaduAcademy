<template>
  <section class="py-24 bg-dark-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl sm:text-4xl font-black text-white text-center mb-16">
        Často kladené otázky
      </h2>
      
      <div class="space-y-6">
        <div v-for="(faq, index) in faqs" :key="index" class="bg-dark-900 rounded-2xl overflow-hidden border border-dark-800">
          <button @click="toggleFaq(index)" class="w-full px-8 py-6 flex items-center justify-between text-left hover:bg-dark-800/50 transition-colors">
            <span class="text-lg font-medium text-white">{{ faq.question }}</span>
            <ChevronDownIcon class="w-6 h-6 text-dark-400 transition-transform duration-300" :class="{ 'rotate-180': faq.isOpen }" />
          </button>
          <div v-show="faq.isOpen" class="px-8 pb-8 text-dark-300">
            <p class="pt-4 border-t border-dark-800">{{ faq.answer }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'

const faqs = ref([
  {
    question: 'Je VidaduAcademy vhodná pre úplných začiatočníkov?',
    answer: 'Absolútne! Máme špeciálne kurzy navrhnuté pre ľudí, ktorí ešte nikdy nenahrali video. Prevedieme ťa od založenia kanála až po prvé úspešné video.',
    isOpen: false
  },
  {
    question: 'Koľko času musím venovať štúdiu?',
    answer: 'Všetky naše kurzy sú "self-paced", čo znamená, že študuješ vlastným tempom. Máme študentov, ktorí venujú štúdiu 15 minút denne, aj takých, ktorí to berú ako plný úväzok.',
    isOpen: false
  },
  {
    question: 'Získam po dokončení kurzu certifikát?',
    answer: 'Áno, po úspešnom dokončení každého kurzu získaš oficiálny certifikát od VidaduAcademy, ktorý si môžeš pridať na LinkedIn alebo do životopisu.',
    isOpen: false
  },
  {
    question: 'Aká je politika vrátenia peňazí?',
    answer: 'Veríme v kvalitu našich kurzov. Ak nebudeš spokojný, ponúkame 14-dňovú garanciu vrátenia peňazí bez zbytočných otázok.',
    isOpen: false
  }
])

const toggleFaq = (index) => {
  faqs.value[index].isOpen = !faqs.value[index].isOpen
}
</script>
