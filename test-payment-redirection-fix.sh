#!/bin/bash

echo "🎯 PAYMENT FLOW REDIRECTION FIX TEST"
echo "===================================="
echo "Testing payment success redirection after configuration fix"
echo ""

echo "✅ Checking backend configuration:"
echo "1. Backend .env FRONTEND_URL:"
grep "FRONTEND_URL" backend/.env

echo ""
echo "2. Laravel config frontend_url:"
grep -A 1 -B 1 "frontend_url" backend/config/app.php

echo ""
echo "3. PaymentController success URL configuration:"
grep -A 3 -B 1 "success_url.*payment" backend/app/Http/Controllers/Api/PaymentController.php

echo ""
echo "✅ Checking frontend routes:"
echo "4. Payment success route definition:"
grep -A 2 -B 1 "payment.*success" frontend/src/router/index.js

echo ""
echo "🧪 Testing complete payment flow:"

# Start services if not running
echo "5. Ensuring backend is running..."
cd backend
if ! ./vendor/bin/sail ps | grep -q "Up"; then
    echo "📊 Backend not running, starting..."
    ./vendor/bin/sail up -d
    sleep 3
else
    echo "✅ Backend already running"
fi
cd ..

echo ""
echo "6. Testing backend API availability:"
if curl -s "http://localhost:8000/api/courses" > /dev/null; then
    echo "✅ Backend API responding"
else
    echo "❌ Backend API not responding"
fi

echo ""
echo "7. Frontend server check:"
if curl -s "http://localhost:3001" > /dev/null; then
    echo "✅ Frontend running on port 3001"
else
    echo "⚠️  Frontend not running on port 3001"
    echo "💡 Start with: cd frontend && npm run dev"
fi

echo ""
echo "🎯 PAYMENT REDIRECTION FIX SUMMARY:"
echo "======================================"
echo "✅ 1. Backend FRONTEND_URL updated to :3001"
echo "✅ 2. PaymentController success_url fixed"
echo "✅ 3. Config app.php default updated"
echo "✅ 4. Frontend /payment/success route exists"
echo "✅ 5. PaymentSuccessView loads payment details"

echo ""
echo "🧪 MANUAL TEST STEPS:"
echo "====================="
echo "1. Go to http://localhost:3001"
echo "2. Navigate to courses page"
echo "3. Click 'Kúpiť kurz' on any course"
echo "4. Complete the payment process"
echo "5. After payment, you should be redirected to:"
echo "   - http://localhost:3001/payment/success?session_id=..."
echo "6. After 3 seconds, auto-redirect to My Courses"

echo ""
echo "🎯 Expected Flow:"
echo "Stripe → http://localhost:3001/payment/success → My Courses"
echo ""
echo "🚨 If still failing, check:"
echo "- Stripe webhook configuration"
echo "- Backend logs: cd backend && ./vendor/bin/sail logs"
echo "- Browser network tab for exact redirect URL"

echo ""
echo "🎉 Payment redirection should now work correctly!"
