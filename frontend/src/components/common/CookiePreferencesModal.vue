<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-[110]">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="w-full max-w-xl transform overflow-hidden rounded-3xl bg-white p-8 text-left align-middle shadow-2xl transition-all">
              <DialogTitle as="h3" class="text-[26px] font-bold tracking-tight text-[#111827] mb-8 font-sans">
                Nastavenia súborov cookie
              </DialogTitle>
              
              <div class="mt-2 space-y-8">
                <!-- Essential (Always On) -->
                <div class="flex items-start justify-between gap-6">
                  <div class="pr-2">
                    <h4 class="text-[17px] font-bold text-[#111827] mb-1">Nevyhnutné cookies</h4>
                    <p class="text-[15px] leading-relaxed font-medium text-slate-500">Tieto súbory sú potrebné pre fungovanie webu a nie je možné ich vypnúť. Bez nich by stránka nefungovala správne.</p>
                  </div>
                  <div class="flex-shrink-0 pt-1">
                    <Switch
                      :modelValue="true"
                      disabled
                      class="bg-[#e99a89] relative inline-flex h-8 w-14 shrink-0 cursor-not-allowed rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                    >
                      <span class="sr-only">Nevyhnutné cookies</span>
                      <span class="translate-x-6 pointer-events-none inline-block h-7 w-7 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" />
                    </Switch>
                  </div>
                </div>

                <!-- Analytics -->
                <div class="flex items-start justify-between gap-6">
                  <div class="pr-2">
                    <h4 class="text-[17px] font-bold text-[#111827] mb-1">Analytické cookies</h4>
                    <p class="text-[15px] leading-relaxed font-medium text-slate-500">Pomáhajú nám pochopiť, ako návštevníci používajú náš web, aby sme ho mohli neustále zlepšovať.</p>
                  </div>
                  <div class="flex-shrink-0 pt-1">
                    <Switch
                      v-model="localPreferences.analytics"
                      :class="[
                        localPreferences.analytics ? 'bg-primary-500 border-transparent' : 'bg-white border-primary-500',
                        'relative inline-flex h-8 w-14 shrink-0 cursor-pointer rounded-full border-2 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2'
                      ]"
                    >
                      <span class="sr-only">Analytické cookies</span>
                      <span
                        aria-hidden="true"
                        :class="[
                          localPreferences.analytics ? 'translate-x-6 bg-white' : 'translate-x-0 bg-gray-100',
                          'pointer-events-none inline-block h-7 w-7 transform rounded-full shadow ring-0 transition duration-200 ease-in-out'
                        ]"
                      />
                    </Switch>
                  </div>
                </div>

                <!-- Marketing -->
                <div class="flex items-start justify-between gap-6">
                  <div class="pr-2">
                    <h4 class="text-[17px] font-bold text-[#111827] mb-1">Marketingové cookies</h4>
                    <p class="text-[15px] leading-relaxed font-medium text-slate-500">Používajú sa na sledovanie návštevníkov naprieč webstránkami. Zámerom je zobrazovať reklamy, ktoré sú relevantné a pútavé pre jednotlivého užívateľa.</p>
                  </div>
                  <div class="flex-shrink-0 pt-1">
                    <Switch
                      v-model="localPreferences.marketing"
                      :class="[
                        localPreferences.marketing ? 'bg-primary-500' : 'bg-[#E5E7EB]',
                        'relative inline-flex h-8 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2'
                      ]"
                    >
                      <span class="sr-only">Marketingové cookies</span>
                      <span
                        aria-hidden="true"
                        :class="[
                          localPreferences.marketing ? 'translate-x-6' : 'translate-x-0',
                          'pointer-events-none inline-block h-7 w-7 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                        ]"
                      />
                    </Switch>
                  </div>
                </div>
              </div>

              <div class="mt-10 flex justify-end space-x-4">
                <button
                  type="button"
                  class="inline-flex justify-center rounded-xl bg-[#F3F4F6] px-6 py-3 text-[16px] font-bold text-[#111827] hover:bg-gray-200 focus:outline-none transition-colors"
                  @click="closeModal"
                >
                  Zrušiť
                </button>
                <button
                  type="button"
                  class="inline-flex justify-center rounded-xl bg-[#E25C3E] px-6 py-3 text-[16px] font-bold text-white hover:bg-[#c94d30] focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-colors shadow-sm"
                  @click="savePreferences"
                >
                  Uložiť nastavenia
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref, watch } from 'vue'
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
  Switch,
} from '@headlessui/vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  initialPreferences: {
    type: Object,
    default: () => ({ analytics: false, marketing: false })
  }
})

const emit = defineEmits(['close', 'save'])

const localPreferences = ref({ ...props.initialPreferences })

// Sync when modal opens
watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    localPreferences.value = { ...props.initialPreferences }
  }
})

const closeModal = () => {
  emit('close')
}

const savePreferences = () => {
  emit('save', localPreferences.value)
}
</script>
