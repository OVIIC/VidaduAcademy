<template>
  <div class="bg-gray-50 min-h-screen py-16 sm:py-24">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
      <div class="bg-white px-6 py-12 shadow-xl sm:rounded-2xl sm:px-12">
        <div class="prose prose-primary mx-auto max-w-none text-gray-700">
          <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl mb-8">
            Ochrana osobných údajov a Cookies
          </h1>
          
          <p class="lead text-xl text-gray-500 mb-8">
            Vaše súkromie je pre nás dôležité. Tieto pravidlá vysvetľujú, aké údaje zhromažďujeme, prečo ich zhromažďujeme a ako s nimi nakladáme v súlade s nariadením GDPR.
          </p>

          <h2 class="text-2xl font-semibold text-gray-900 mt-12 mb-4">1. Kto sme (Prevádzkovateľ)</h2>
          <p>
            Prevádzkovateľom vašich osobných údajov je spoločnosť <strong>Vidadu Extra, s. r. o.</strong>, so sídlom Račianska 22/A, 831 02 Bratislava, IČO: 50437101.<br>
            V prípade akýchkoľvek otázok ohľadom spracovania vašich údajov nás môžete kontaktovať na e-mailovej adrese: <strong>vidadutv@vidadu.com</strong>.
          </p>

          <h2 class="text-2xl font-semibold text-gray-900 mt-12 mb-4">2. Aké údaje zhromažďujeme a prečo</h2>
          <ul class="list-disc pl-5 space-y-2 mb-6">
            <li><strong>Údaje potrebné na vybavenie objednávky:</strong> Meno, priezvisko, e-mailová adresa, fakturačná adresa. Právnym základom je plnenie zmluvy.</li>
            <li><strong>Účtovníctvo:</strong> Fakturačné údaje ukladáme na základe zákonnej povinnosti (zákony o účtovníctve).</li>
            <li><strong>Marketing (s vaším súhlasom):</strong> E-mailová adresa pre zasielanie noviniek alebo štatistické údaje z cookies na zlepšovanie nášho webu.</li>
          </ul>

          <h2 class="text-2xl font-semibold text-gray-900 mt-12 mb-4">3. Kto má prístup k vašim údajom</h2>
          <p>
            Vaše údaje nepredávame. Zdieľame ich len s partnermi, ktorí sú nevyhnutní pre našu činnosť:
          </p>
          <ul class="list-disc pl-5 space-y-2 mb-6">
            <li>Poskytovatelia platobných brán (napr. Stripe) pre spracovanie platieb.</li>
            <li>Účtovná firma pre spracovanie daňových povinností.</li>
            <li>Poskytovatelia analytických softvérov (napr. Google Analytics), len pokiaľ ste udelili súhlas s analytickými cookies.</li>
          </ul>

          <h2 class="text-2xl font-semibold text-gray-900 mt-12 mb-4">4. Vaše práva podľa GDPR</h2>
          <p>Máte právo:</p>
          <ul class="list-disc pl-5 space-y-2 mb-6">
            <li>Požiadať o prístup k svojim osobným údajom.</li>
            <li>Na opravu nesprávnych údajov alebo na výmaz údajov ("právo na zabudnutie").</li>
            <li>Obmedziť spracúvanie alebo namietať proti spracúvaniu.</li>
            <li>Na prenosnosť údajov.</li>
            <li>Kedykoľvek odvolať svoj udelený súhlas.</li>
          </ul>

          <h2 class="text-2xl font-semibold text-gray-900 mt-12 mb-4">5. Zásady používania súborov Cookie</h2>
          <p>
            Tento web používa súbory cookie na zlepšenie vášho zážitku. Súbory cookie rozdeľujeme do troch kategórií:
          </p>
          <div class="bg-gray-50 p-6 rounded-lg my-6 border border-gray-100">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nevyhnutné cookies (Vždy zapnuté)</h3>
            <p class="text-sm text-gray-600 mb-4">Ide o cookies nevyhnutné pre fungovanie nákupného košíka, prihlasovania a zabezpečenia stránky. Tieto cookies nemôžu byť vypnuté.</p>
            
            <h3 class="text-lg font-medium text-gray-900 mb-2">Analytické cookies</h3>
            <p class="text-sm text-gray-600 mb-4">Pomáhajú nám pochopiť, aké stránky sú najpopulárnejšie a ako sa návštevníci na webe správajú. Sú aktívne len s vaším súhlasom.</p>

            <h3 class="text-lg font-medium text-gray-900 mb-2">Marketingové cookies</h3>
            <p class="text-sm text-gray-600">Slúžia na zobrazenie relevantnej reklamy pre vás na iných weboch a sociálnych sieťach. Sú aktívne len s vaším súhlasom.</p>
          </div>

          <p class="font-medium text-gray-900">Správa vašich súhlasov s Cookies (Vaše právo kedykoľvek zmeniť názor)</p>
          <p class="mb-6">
            V súlade s GDPR máte právo kedykoľvek prehodnotiť nastavenie vašich súborov cookie a rovnako jednoducho, ako ste súhlas udelili, ho aj odvolať.
          </p>

          <button 
            @click="openCookiePreferences" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
          >
            Spravovať nastavenia Cookies
          </button>
          
          <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-500">
              Tieto pravidlá sú platné od 1. januára 2024. Posledná aktualizácia: {{ new Date().toLocaleDateString('sk-SK') }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCookieConsent } from '@/composables/useCookieConsent'
import { onMounted } from 'vue'

const { openPreferences } = useCookieConsent()

const openCookiePreferences = () => {
  openPreferences()
}

onMounted(() => {
  window.scrollTo(0, 0)
})
</script>
