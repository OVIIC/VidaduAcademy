# Automatic Lesson Scrolling - Implementation

## Úvod
Implementované automatické scrollovanie na začiatok lekcie pri kliku na ľubovoľnú lekciu v kurze pre lepší používateľský zážitok.

## Implementované funkcie

### 1. Automatic Smooth Scrolling
- **Čo**: Stránka sa automaticky a plynulo posúva na začiatok lekcie
- **Kedy**: Pri kliku na ľubovoľnú lekciu (sidebar, navigačné tlačidlá)
- **Ako**: Použitie `window.scrollTo()` s `behavior: 'smooth'`

### 2. Optimal Offset
- **Offset**: 80px od vrcholu stránky
- **Dôvod**: Lepšia viditeľnosť obsahu lekcie bez zakrytia headerom

### 3. Visual Feedback
- **Loading State**: Zobrazenie loading indikátora počas prechodu
- **Disabled States**: Tlačidlá sú dočasne zakázané počas animácie
- **Duration**: 1 sekunda na dokončenie animácie

### 4. Universal Implementation
Scrollovanie funguje pre všetky spôsoby navigácie:
- ✅ Klik na lekciu v sidebar-i
- ✅ "Predchádzajúca" tlačidlo
- ✅ "Ďalšia lekcia" tlačidlo  
- ✅ Šípky v header-i lekcie

## Technické detaily

### Komponenty
**Súbor**: `/frontend/src/views/CourseStudyView.vue`

### Kľúčové funkcie
```javascript
const selectLesson = (lesson) => {
  // Prevent multiple rapid clicks
  if (lessonSwitching.value) return
  
  lessonSwitching.value = true
  selectedLesson.value = lesson
  
  // Smooth scroll after DOM update
  nextTick(() => {
    if (lessonContentRef.value) {
      const rect = lessonContentRef.value.getBoundingClientRect()
      const offset = 80
      
      window.scrollTo({
        top: window.pageYOffset + rect.top - offset,
        behavior: 'smooth'
      })
      
      // Reset loading state
      setTimeout(() => {
        lessonSwitching.value = false
      }, 1000)
    }
  })
}
```

### Nové reactive variables
- `lessonContentRef` - Referencia na DOM element s obsahom lekcie
- `lessonSwitching` - Boolean flag pre loading state

### UI Improvements
- **Loading overlay** na sidebar lekciách
- **Spinning indikátory** na navigačných tlačidlách
- **Disabled states** počas animácie
- **Opacity effects** pre vizuálny feedback

## UX Benefits

### Pred implementáciou
- Používateľ musel manuálne scrollovať na lekciu
- Neprehľadné prepínanie medzi lekciami
- Zlá orientácia na stránke

### Po implementácii  
- ✅ Automatické scrollovanie na lekciu
- ✅ Plynulá animácia
- ✅ Jasný vizuálny feedback
- ✅ Lepšia orientácia na stránke
- ✅ Intuitívnejší UX

## Testovanie

### Manuálne testovanie
1. Spustiť `./test-lesson-scroll.sh`
2. Otvoriť http://localhost:3003
3. Prihlásiť sa a navigovať na kurz s lekciami
4. Testovať všetky spôsoby navigácie

### Očakávané správanie
- Plynulé scrollovanie (1s animácia)
- Offset 80px od vrcholu
- Loading states počas prechodu
- Funkčnosť na všetkých zariadeniach

## Performance Impact
- **Minimálny**: Len JavaScript animácia
- **Bez API calls**: Čisto frontend funkcionalita
- **Optimalizované**: Debouncing proti viacnásobným klikom

## Browser Support
- ✅ Chrome/Edge (Chromium)
- ✅ Firefox
- ✅ Safari
- ✅ Mobile browsers

## Budúce vylepšenia
- [ ] Konfigurovateľný offset
- [ ] Rôzne animácie (fade, slide)
- [ ] Keyboard navigation support
- [ ] Touch gestures na mobile

---

**Dátum implementácie**: 27. jún 2025  
**Status**: ✅ Kompletná implementácia  
**Testované**: ✅ Funkčné
