#!/bin/bash

# 🛒 KOMPLETNÝ TESTOVACÍ POSTUP NÁKUPU KURZU
# Pre VidaduAcademy

echo "🛒 KOMPLETNÝ TEST NÁKUPNÉHO PROCESU"
echo "=================================="

echo ""
echo "📋 KROK ZA KROKOM TESTOVANIE:"
echo "=============================="
echo ""

echo "1️⃣ PRIHLÁSENIE (POVINNÉ!):"
echo "  → Choďte na: http://localhost:3000/login"
echo "  → Prihláste sa s existujúcim účtom"
echo "  → Alebo sa zaregistrujte na: http://localhost:3000/register"
echo "  → Po prihlásení sa dostanete na dashboard"
echo ""

echo "2️⃣ PREHLIADANIE KURZOV:"
echo "  → Choďte na: http://localhost:3000/courses"
echo "  → Vyberte si kurz (napr. 'test kurz')"
echo "  → Kliknite na kurz pre detail"
echo ""

echo "3️⃣ DETAIL KURZU:"
echo "  → URL: http://localhost:3000/course/testkurz"
echo "  → Skontrolujte detail kurzu"
echo "  → Kliknite na 'Kúpiť kurz' tlačidlo"
echo "  → POZOR: Ak nie ste prihlásený, zobrazí sa auth modal!"
echo ""

echo "4️⃣ NÁKUPNÝ PROCES:"
echo "  → Ak ste prihlásený, otvorí sa Stripe checkout"
echo "  → Ak zlyhá, fallback na simulátor: /checkout"
echo "  → Dokončite 'platbu' (v development mode)"
echo ""

echo "5️⃣ PO NÁKUPE:"
echo "  → Presmerovanie na: /payment/success"
echo "  → Kliknite 'Start Learning Now'"
echo "  → Dostanete sa na learning interface: /study/[slug]"
echo ""

echo "🚨 MOŽNÉ CHYBY A RIEŠENIA:"
echo "=========================="
echo ""
echo "❌ '404 Not Found' po kliknutí 'Kúpiť kurz':"
echo "  → Nie ste prihlásený!"
echo "  → Riešenie: Prihláste sa najprv"
echo ""
echo "❌ 'Auth modal sa neobjaví':"
echo "  → JavaScript chyba v komponente"
echo "  → Skontrolujte Console v DevTools"
echo ""
echo "❌ 'Checkout stránka sa nenačíta':"
echo "  → Vue template compilation error"
echo "  → Skontrolujte Vue komponent errors"
echo ""
echo "❌ 'API 401 Unauthorized':"
echo "  → Token expired alebo invalid"
echo "  → Riešenie: Odhlás + prihlás sa znovu"
echo ""

echo "🔧 DEBUGGING KROKY:"
echo "=================="
echo ""
echo "1. Otvorte DevTools (F12)"
echo "2. Console tab - hľadajte JavaScript errors"
echo "3. Network tab - sledujte API volania"
echo "4. Application tab - skontrolujte localStorage auth token"
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

# Check auth status
AUTH_STATUS=$(curl -s http://localhost:3000/api/user 2>/dev/null | head -1)
if echo "$AUTH_STATUS" | grep -q "email\|name"; then
    echo "✅ User je prihlásený"
else
    echo "❌ User nie je prihlásený - NAJPRV SA PRIHLÁSTE!"
fi

echo ""
echo "🎯 ZAČNITE TESTOVANIE:"
echo "==================="
echo "1. http://localhost:3000/login (ak nie ste prihlásený)"
echo "2. http://localhost:3000/courses (prehliadanie kurzov)"
echo "3. http://localhost:3000/course/testkurz (detail kurzu)"
echo "4. Kliknite 'Kúpiť kurz' a sledujte proces"
echo ""
