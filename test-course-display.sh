#!/bin/bash

# 🔍 TESTOVANIE ZOBRAZENIA KURZOV
# Pre VidaduAcademy Course Display Debug

echo "🔍 TESTOVANIE ZOBRAZENIA KURZOV - VidaduAcademy"
echo "==============================================="

# Check if servers are running
FRONTEND_PORTS=(3000 3001 3002 3003 3004 3005)
BACKEND_PORTS=(8000 8080 8888)
FRONTEND_URL=""
BACKEND_URL=""

# Check frontend
for port in "${FRONTEND_PORTS[@]}"; do
    if curl -s "http://localhost:$port" > /dev/null; then
        FRONTEND_URL="http://localhost:$port"
        echo "✅ Frontend server beží na $FRONTEND_URL"
        break
    fi
done

# Check backend
for port in "${BACKEND_PORTS[@]}"; do
    if curl -s "http://localhost:$port/api/health" > /dev/null 2>&1 || curl -s "http://localhost:$port" > /dev/null 2>&1; then
        BACKEND_URL="http://localhost:$port"
        echo "✅ Backend server beží na $BACKEND_URL"
        break
    fi
done

if [ -z "$FRONTEND_URL" ]; then
    echo "❌ Frontend server nebeží"
    exit 1
fi

if [ -z "$BACKEND_URL" ]; then
    echo "❌ Backend server nebeží"
    exit 1
fi

echo ""
echo "🔍 TESTOVANIE API ENDPOINTOV:"
echo "-----------------------------"

# Test courses list
echo "📚 Testovanie /api/courses..."
COURSES_RESPONSE=$(curl -s "$BACKEND_URL/api/courses")
if echo "$COURSES_RESPONSE" | jq . > /dev/null 2>&1; then
    echo "  ✅ JSON response je validný"
    COURSE_COUNT=$(echo "$COURSES_RESPONSE" | jq '.data | length // 0')
    echo "  📊 Počet kurzov: $COURSE_COUNT"
    
    if [ "$COURSE_COUNT" -gt 0 ]; then
        echo "  📋 Prvý kurz:"
        FIRST_COURSE=$(echo "$COURSES_RESPONSE" | jq '.data[0]')
        COURSE_ID=$(echo "$FIRST_COURSE" | jq -r '.id // "N/A"')
        COURSE_SLUG=$(echo "$FIRST_COURSE" | jq -r '.slug // "N/A"')
        COURSE_TITLE=$(echo "$FIRST_COURSE" | jq -r '.title // "N/A"')
        COURSE_PRICE=$(echo "$FIRST_COURSE" | jq -r '.price // "N/A"')
        
        echo "    - ID: $COURSE_ID"
        echo "    - Slug: $COURSE_SLUG"
        echo "    - Title: $COURSE_TITLE"
        echo "    - Price: $COURSE_PRICE"
        
        # Test course detail by slug
        if [ "$COURSE_SLUG" != "N/A" ] && [ "$COURSE_SLUG" != "null" ]; then
            echo ""
            echo "🎯 Testovanie detailu kurzu /api/courses/$COURSE_SLUG..."
            COURSE_DETAIL_RESPONSE=$(curl -s "$BACKEND_URL/api/courses/$COURSE_SLUG")
            if echo "$COURSE_DETAIL_RESPONSE" | jq . > /dev/null 2>&1; then
                echo "  ✅ Course detail API funguje"
                echo "  📋 Detail kurzu:"
                echo "$COURSE_DETAIL_RESPONSE" | jq '{id, slug, title, price, description: (.description // "N/A" | if length > 100 then .[:100] + "..." else . end)}'
            else
                echo "  ❌ Course detail API nefunguje"
                echo "  📝 Response: $COURSE_DETAIL_RESPONSE"
            fi
        else
            echo "  ❌ Kurz nemá slug"
        fi
    else
        echo "  ❌ Žiadne kurzy nenájdené"
    fi
else
    echo "  ❌ Invalid JSON response"
    echo "  📝 Response: $COURSES_RESPONSE"
fi

echo ""
echo "🌐 TESTOVANIE FRONTEND ROUTES:"
echo "------------------------------"

# Test courses page
echo "📋 Testovanie stránky kurzov..."
COURSES_PAGE=$(curl -s "$FRONTEND_URL/courses")
if echo "$COURSES_PAGE" | grep -q "VidaduAcademy\|kurz\|course"; then
    echo "  ✅ Courses page sa načítala"
else
    echo "  ❌ Courses page sa nenačítala správne"
fi

# Test course detail page (if we have a slug)
if [ "$COURSE_SLUG" != "N/A" ] && [ "$COURSE_SLUG" != "null" ]; then
    echo "🎯 Testovanie course detail page..."
    COURSE_DETAIL_PAGE=$(curl -s "$FRONTEND_URL/course/$COURSE_SLUG")
    if echo "$COURSE_DETAIL_PAGE" | grep -q "VidaduAcademy\|kurz\|course"; then
        echo "  ✅ Course detail page sa načítala"
    else
        echo "  ❌ Course detail page sa nenačítala správne"
    fi
fi

echo ""
echo "🔧 DEBUGGING INFO:"
echo "------------------"
echo "Frontend URL: $FRONTEND_URL"
echo "Backend URL: $BACKEND_URL"
echo "Test Course Slug: $COURSE_SLUG"

echo ""
echo "📝 MANUAL TESTING STEPS:"
echo "========================"
echo "1️⃣ Otvorte prehliadač na: $FRONTEND_URL"
echo "2️⃣ Choďte na Kurzy: $FRONTEND_URL/courses"
echo "3️⃣ Skontrolujte DevTools Console (F12) pre chyby"
echo "4️⃣ Skontrolujte Network tab pre API volania"
echo "5️⃣ Ak sa kurzy nezobrazujú, skontrolujte:"
echo "   - API volanie na /api/courses"
echo "   - Response obsahuje kurzy"
echo "   - Frontend správne parsuje dáta"

if [ "$COURSE_SLUG" != "N/A" ] && [ "$COURSE_SLUG" != "null" ]; then
    echo "6️⃣ Testujte konkrétny kurz: $FRONTEND_URL/course/$COURSE_SLUG"
    echo "7️⃣ Skontrolujte, či sa zobrazuje:"
    echo "   - Názov kurzu: $COURSE_TITLE"
    echo "   - Cena: $COURSE_PRICE"
    echo "   - Popis kurzu"
    echo "   - Tlačidlo 'Kúpiť kurz'"
fi

echo ""
echo "🚨 MOŽNÉ PROBLÉMY:"
echo "=================="
echo "• CORS chyby - backend nie je prístupný z frontendu"
echo "• API endpoint neexistuje alebo vracia chybu"
echo "• Frontend komponenty majú chyby v načítavaní dát"
echo "• Kurzy nemajú správny slug"
echo "• Database je prázdna"

echo ""
echo "🔍 QUICK API TEST:"
echo "=================="
echo "Skúste tieto URL v prehliadači:"
echo "• Backend API: $BACKEND_URL/api/courses"
echo "• Course detail: $BACKEND_URL/api/courses/$COURSE_SLUG"
echo "• Frontend courses: $FRONTEND_URL/courses"
echo "• Frontend course detail: $FRONTEND_URL/course/$COURSE_SLUG"

echo ""
echo "=== TESTOVANIE DOKONČENÉ ==="
