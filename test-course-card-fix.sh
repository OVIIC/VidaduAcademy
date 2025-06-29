#!/bin/bash

# 🛒 TEST TLAČIDLA "KÚPIŤ KURZ" NA KARTE KURZU
# Pre VidaduAcademy Course Card Purchase Button

echo "🛒 TEST TLAČIDLA 'KÚPIŤ KURZ' NA KARTE KURZU"
echo "============================================"

echo ""
echo "🔍 IDENTIFIKOVANÝ PROBLÉM:"
echo "========================="
echo "❌ Tlačidlo 'Kúpiť kurz' na karte kurzu malo nesprávne URL:"
echo "   Pôvodne: /courses/{slug} (404 chyba)"
echo "   Opravené: /course/{slug}  (správne)"
echo ""

echo "🔧 VYKONANÉ OPRAVY:"
echo "=================="
echo "✅ CourseCardMobile.vue - opravená URL pre course detail"
echo "✅ CourseCardMobile.vue - opravená URL pre course study"
echo ""

echo "📊 QUICK STATUS CHECK:"
echo "====================="

# Check if servers are running
if curl -s http://localhost:3000 > /dev/null; then
    echo "✅ Frontend server beží"
else
    echo "❌ Frontend server nebeží - spustite: cd frontend && npm run dev"
fi

if curl -s http://localhost:8000 > /dev/null; then
    echo "✅ Backend server beží"
else
    echo "❌ Backend server nebeží - spustite: cd backend && ./vendor/bin/sail up -d"
fi

echo ""
echo "🎯 TESTOVANIE OPRAVENÉHO TLAČIDLA:"
echo "=================================="
echo ""
echo "1️⃣ OTVORTE KURZY:"
echo "   → URL: http://localhost:3000/courses"
echo "   → Uvidíte karty kurzov"
echo ""
echo "2️⃣ TESTUJTE TLAČIDLO 'KÚPIŤ KURZ':"
echo "   → Na karte kurzu kliknite 'Kúpiť kurz'"
echo "   → Ak nie ste prihlásený: presmeruje na /login"
echo "   → Ak ste prihlásený: presmeruje na /course/{slug}"
echo ""
echo "3️⃣ OČAKÁVANÉ SPRÁVANIE:"
echo "   ✅ Neprihlásený user → /login"
echo "   ✅ Prihlásený user → /course/testkurz (course detail)"
echo "   ✅ Enrolled user → /study/testkurz (course study)"
echo "   ❌ Už nie 404 chyba!"
echo ""

echo "🧪 MANUÁLNY TEST:"
echo "================"
echo ""

# Test course card routes
echo "📋 Testovanie routes..."
if curl -s "http://localhost:3000/course/testkurz" | grep -q "VidaduAcademy\|kurz\|course"; then
    echo "✅ Route /course/testkurz funguje"
else
    echo "❌ Route /course/testkurz nefunguje"
fi

if curl -s "http://localhost:3000/study/testkurz" | grep -q "VidaduAcademy\|kurz\|course"; then
    echo "✅ Route /study/testkurz funguje"
else
    echo "❌ Route /study/testkurz nefunguje"
fi

# Check auth status
AUTH_STATUS=$(curl -s http://localhost:3000/api/user 2>/dev/null | head -1)
if echo "$AUTH_STATUS" | grep -q "email\|name"; then
    echo "✅ User je prihlásený"
    echo "   → Tlačidlo 'Kúpiť kurz' vás presmeruje na course detail"
else
    echo "❌ User nie je prihlásený"
    echo "   → Tlačidlo 'Kúpiť kurz' vás presmeruje na login"
fi

echo ""
echo "🚀 ZAČNITE TESTOVANIE:"
echo "===================="
echo "1. Otvorte: http://localhost:3000/courses"
echo "2. Nájdite kartu kurzu 'TEstu kurz 2'"
echo "3. Kliknite na oranžové tlačidlo 'Kúpiť kurz'"
echo "4. Skontrolujte, že sa NEDOSTANETE na 404!"
echo ""
echo "💡 TIPY PRE TESTOVANIE:"
echo "======================"
echo "• Ak nie ste prihlásený → mal by vás poslať na /login"
echo "• Ak ste prihlásený → mal by vás poslať na /course/testkurz"
echo "• Na course detail page môžete dokončiť nákup"
echo ""

echo "🎉 PROBLÉM BY MAL BYŤ VYRIEŠENÝ!"
echo "================================"
