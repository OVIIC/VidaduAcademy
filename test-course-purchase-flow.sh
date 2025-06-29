#!/bin/bash

# 🛒 TESTOVANIE NÁKUPNÉHO PROCESU KURZOV
# Pre VidaduAcademy Course Purchase Flow

echo "🛒 TESTOVANIE NÁKUPNÉHO PROCESU - VidaduAcademy"
echo "=============================================="

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
    echo "   Spustite: cd frontend && npm run dev"
    exit 1
fi

if [ -z "$BACKEND_URL" ]; then
    echo "❌ Backend server nebeží"
    echo "   Spustite: cd backend && ./vendor/bin/sail up -d"
    exit 1
fi

echo ""
echo "🔍 TESTOVANIE API ENDPOINTOV:"
echo "-----------------------------"

# Test courses endpoint
echo "📚 Testovanie zoznamu kurzov..."
if curl -s "$BACKEND_URL/api/courses" | grep -q "title\|name"; then
    echo "  ✅ API endpoint /api/courses funguje"
else
    echo "  ❌ Problém s API endpoint /api/courses"
fi

# Test learning endpoint
echo "🎓 Testovanie learning endpointov..."
if curl -s "$BACKEND_URL/api/learning" | grep -q "Unauthenticated\|enrollment\|course"; then
    echo "  ✅ API endpoint /api/learning je dostupný"
else
    echo "  ❌ Problém s API endpoint /api/learning"
fi

echo ""
echo "🛒 NÁKUPNÝ PROCES - MANUÁLNE TESTOVANIE:"
echo "======================================="
echo ""
echo "1️⃣ REGISTRÁCIA/PRIHLÁSENIE:"
echo "   • Choďte na: $FRONTEND_URL/register"
echo "   • Zaregistrujte sa alebo sa prihláste"
echo "   • Skontrolujte, že ste prihlásený"
echo ""
echo "2️⃣ PREHLIADANIE KURZOV:"
echo "   • Choďte na: $FRONTEND_URL/courses"
echo "   • Skontrolujte, že sa kurzy načítajú"
echo "   • Kliknite na kurz pre detail"
echo ""
echo "3️⃣ DETAIL KURZU:"
echo "   • URL: $FRONTEND_URL/course/[slug]"
echo "   • Skontrolujte zobrazenie kurzu"
echo "   • Kliknite na 'Kúpiť kurz' tlačidlo"
echo ""
echo "4️⃣ CHECKOUT PROCES:"
echo "   • URL: $FRONTEND_URL/checkout"
echo "   • Vyplňte platobné údaje (test mode)"
echo "   • Dokončite platbu"
echo ""
echo "5️⃣ ÚSPEŠNÁ PLATBA:"
echo "   • URL: $FRONTEND_URL/payment/success"
echo "   • Skontrolujte success správu"
echo "   • Kliknite na 'Start Learning Now'"
echo ""
echo "6️⃣ UČENIE KURZU:"
echo "   • URL: $FRONTEND_URL/study/[slug]"
echo "   • Skontrolujte, že sa kurz načítal"
echo "   • Testujte navigáciu medzi lekciami"
echo "   • Označte lekciu ako hotovú"
echo ""
echo "7️⃣ MOJE KURZY:"
echo "   • URL: $FRONTEND_URL/my-courses"
echo "   • Skontrolujte, že sa kurz zobrazuje"
echo "   • Kliknite na 'Continue Learning'"
echo ""

echo "🔧 DEBUGGING TIPY:"
echo "=================="
echo "• Otvorte Browser DevTools (F12)"
echo "• Sledujte Console tab pre chyby"
echo "• Sledujte Network tab pre API volania"
echo "• Skontrolujte Response na API calls"
echo ""
echo "🚨 ČASTÉ PROBLÉMY:"
echo "=================="
echo "• 404 na /learning/course/undefined - kurz nemá slug"
echo "• 401 Unauthorized - nie ste prihlásený"
echo "• 404 na course detail - nesprávny slug"
echo "• CORS chyby - backend nie je dostupný"
echo ""

echo "✅ OČAKÁVANÉ SPRÁVANIE:"
echo "======================="
echo "• Po registrácii: presmerovanie na dashboard"
echo "• Po prihlásení: presmerovanie na pôvodnú stránku"
echo "• Po nákupe: presmerovanie na payment success"
echo "• Po kliknutí 'Start Learning': učenie kurzu sa načíta"
echo "• V 'Moje kurzy': kurz je označený ako zakúpený"
echo "• Progress tracking: dokončené lekcie sa uložia"

echo ""
echo "🎯 KĽÚČOVÉ SÚBORY NA KONTROLU:"
echo "=============================="
echo "Frontend Routes:"
echo "  • frontend/src/router/index.js"
echo "  • frontend/src/views/CourseDetailView.vue"
echo "  • frontend/src/views/CourseStudyView.vue" 
echo "  • frontend/src/views/PaymentSuccessView.vue"
echo "  • frontend/src/views/MyCoursesView.vue"
echo ""
echo "Backend APIs:"
echo "  • backend/routes/api.php"
echo "  • backend/app/Http/Controllers/Api/LearningController.php"
echo "  • backend/app/Http/Controllers/Api/CourseController.php"

echo ""
echo "🚀 ZAČNITE TESTOVANIE!"
echo "Frontend: $FRONTEND_URL"
echo "Backend: $BACKEND_URL"
echo "==========================================="

echo "=== Test Course Purchase and Learning Flow ==="

# Test 1: Check if all route links use correct parameters
echo "1. Checking route links..."

# Check CourseDetailView.vue
echo "   - CourseDetailView.vue:"
if grep -q "name: 'CourseStudy'" frontend/src/views/CourseDetailView.vue; then
    echo "     ✅ Uses CourseStudy route"
else
    echo "     ❌ Does not use CourseStudy route"
fi

# Check PaymentSuccessView.vue  
echo "   - PaymentSuccessView.vue:"
if grep -q "name: 'CourseStudy'" frontend/src/views/PaymentSuccessView.vue; then
    echo "     ✅ Uses CourseStudy route"
else
    echo "     ❌ Does not use CourseStudy route"
fi

# Test 2: Check LearnView.vue API endpoints
echo "2. Checking LearnView.vue API endpoints..."
if grep -q "/learning/course/\${route.params.courseSlug}" frontend/src/views/LearnView.vue; then
    echo "   ✅ Uses correct API endpoint with courseSlug"
else
    echo "   ❌ API endpoint issue"
fi

# Test 3: Check learningService endpoints
echo "3. Checking learningService..."
if grep -q "getCourseContent(courseSlug)" frontend/src/services/index.js; then
    echo "   ✅ learningService has correct getCourseContent method"
else
    echo "   ❌ learningService issue"
fi

# Test 4: Test API connectivity
echo "4. Testing API connectivity..."
if curl -s http://localhost:80/api/health > /dev/null 2>&1; then
    echo "   ✅ Backend API is running"
else
    echo "   ❌ Backend API is not accessible"
fi

# Test 5: Test frontend connectivity
echo "5. Testing frontend connectivity..."
if curl -s http://localhost:5173 > /dev/null 2>&1; then
    echo "   ✅ Frontend is running"
else
    echo "   ❌ Frontend is not accessible"
fi

echo ""
echo "=== Test Complete ==="
echo "If all tests pass, try purchasing a course and accessing it through:"
echo "1. CourseDetailView -> Continue Learning button"  
echo "2. PaymentSuccessView -> Start Learning Now button"
echo "3. MyCoursesView -> Course card button"
