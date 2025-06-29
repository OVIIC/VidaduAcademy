#!/bin/bash

# 🔍 LIVE DEBUGGING - UI TEST
# Real-time debugging pre course display

echo "🔍 LIVE UI DEBUGGING - VidaduAcademy"
echo "===================================="

echo "📱 Otvorte prehliadač na: http://localhost:3000"
echo "🔧 Otvorte DevTools (F12) a sledujte Console"
echo ""

echo "🎯 TESTUJTE TIETO KROKY:"
echo "========================"
echo ""
echo "1️⃣ DOMOVSKÁ STRÁNKA"
echo "   URL: http://localhost:3000"
echo "   ✅ Stránka sa načíta"
echo "   ✅ Navigácia funguje"
echo "   ✅ Žiadne chyby v Console"
echo ""

echo "2️⃣ STRÁNKA KURZOV"
echo "   URL: http://localhost:3000/courses"
echo "   ✅ Stránka sa načíta"
echo "   🔍 Sledujte Network tab:"
echo "      - API volanie: GET /api/courses"
echo "      - Response: Status 200"
echo "      - Data obsahuje kurzy"
echo "   ✅ Kurzy sa zobrazia na stránke"
echo "   ✅ Žiadne chyby v Console"
echo ""

echo "3️⃣ DETAIL KURZU"
echo "   URL: http://localhost:3000/course/testkurz"
echo "   ✅ Stránka sa načíta"
echo "   🔍 Sledujte Network tab:"
echo "      - API volanie: GET /api/courses/testkurz"
echo "      - Response: Status 200"
echo "      - Data obsahuje detail kurzu"
echo "   ✅ Detail kurzu sa zobrazí:"
echo "      - Názov: 'test kurz'"
echo "      - Cena: '$100.00'"
echo "      - Popis kurzu"
echo "      - Tlačidlo 'Kúpiť kurz'"
echo "   ✅ Žiadne chyby v Console"
echo ""

echo "🚨 AK SÚ PROBLÉMY, SKONTROLUJTE:"
echo "================================"
echo ""
echo "📋 STRÁNKA KURZOV PROBLÉMY:"
echo "• Kurzy sa nezobrazujú"
echo "  → Skontrolujte Network tab"
echo "  → API volanie na /api/courses"
echo "  → Chyba v parsovaní response"
echo "  → Problem s component rendering"
echo ""
echo "• Loading spinner sa nezastaví"
echo "  → API volanie zlyhalo"
echo "  → Chyba v try/catch bloku"
echo "  → Network connectivity problem"
echo ""
echo "• Console errors"
echo "  → Component errors"
echo "  → API errors"
echo "  → JavaScript errors"
echo ""

echo "🎯 DETAIL KURZU PROBLÉMY:"
echo "========================"
echo "• 404 Not Found error"
echo "  → Nesprávny slug v URL"
echo "  → Kurz neexistuje v databáze"
echo "  → Backend routing problem"
echo ""
echo "• Course detail sa nezobrazuje"
echo "  → API volanie zlyhalo"
echo "  → Chyba v CourseDetailView.vue"
echo "  → Data parsing problem"
echo ""
echo "• Tlačidlo 'Kúpiť kurz' nefunguje"
echo "  → Chyba v click handler"
echo "  → Authentication problem"
echo "  → Route problem"
echo ""

echo "🔧 DEBUGGING COMMANDS:"
echo "====================="
echo ""
echo "# Skontrolujte API response manuálne:"
echo "curl -s http://localhost:8000/api/courses | jq '.'"
echo ""
echo "# Skontrolujte konkrétny kurz:"
echo "curl -s http://localhost:8000/api/courses/testkurz | jq '.'"
echo ""
echo "# Skontrolujte frontend build:"
echo "cd frontend && npm run build"
echo ""
echo "# Reštart development servera:"
echo "cd frontend && npm run dev"
echo ""

echo "🎯 QUICK TESTS:"
echo "==============="
echo ""

# Test API directly
echo "📡 API TEST:"
if curl -s http://localhost:8000/api/courses | jq '.data | length' > /dev/null 2>&1; then
    COURSE_COUNT=$(curl -s http://localhost:8000/api/courses | jq '.data | length')
    echo "✅ API funguje - $COURSE_COUNT kurzov"
else
    echo "❌ API nefunguje"
fi

# Test specific course
echo "🎯 COURSE TEST:"
if curl -s http://localhost:8000/api/courses/testkurz | jq '.title' > /dev/null 2>&1; then
    COURSE_TITLE=$(curl -s http://localhost:8000/api/courses/testkurz | jq -r '.title')
    echo "✅ Course detail funguje - '$COURSE_TITLE'"
else
    echo "❌ Course detail nefunguje"
fi

echo ""
echo "🚀 ZAČNITE TESTOVANIE V PREHLIADAČI!"
echo "===================================="
echo "1. Otvorte http://localhost:3000"
echo "2. DevTools (F12) → Console tab"
echo "3. DevTools → Network tab"
echo "4. Testujte kroky vyššie"
echo "5. Hľadajte chyby v Console/Network"
echo ""
