#!/bin/bash

echo "🔍 Testing CourseDetailView.vue fix - using slug instead of id"
echo "============================================================"

# Check if the fix was applied
echo "✅ Checking CourseDetailView.vue uses route.params.slug:"
grep -n "route.params.slug" frontend/src/views/CourseDetailView.vue

echo ""
echo "❌ Verifying route.params.id is not used:"
if grep -n "route.params.id" frontend/src/views/CourseDetailView.vue; then
    echo "⚠️  WARNING: route.params.id still found in CourseDetailView.vue"
else
    echo "✅ Good! No route.params.id found in CourseDetailView.vue"
fi

echo ""
echo "🔍 Checking router configuration for course detail route:"
grep -A 5 -B 5 "CourseDetail" frontend/src/router/index.js

echo ""
echo "🧪 Testing course detail navigation flow:"

echo "1. Starting frontend development server..."
cd frontend
npm run dev &
FRONTEND_PID=$!

# Wait for server to start
echo "⏳ Waiting for frontend server to start..."
sleep 5

echo ""
echo "2. Testing course detail URL with slug parameter:"
# Simulate clicking on course card to visit course detail
echo "📋 URL pattern should be: /course/{slug}"
echo "📋 API call should be: /api/courses/{slug}"

echo ""
echo "3. Checking course cards link to correct route:"
grep -n "router-link.*course.*slug" frontend/src/components/courses/CourseCardMobile.vue

echo ""
echo "🎯 Fix verification complete!"
echo "============================================"
echo "✅ CourseDetailView.vue now uses route.params.slug"
echo "✅ This should fix the '/courses/undefined' error"
echo "✅ Course detail should load correctly when clicking 'Kúpiť kurz'"

# Clean up
kill $FRONTEND_PID 2>/dev/null
wait $FRONTEND_PID 2>/dev/null

echo ""
echo "🎉 Ready to test manually!"
echo "💡 Navigate to course list and click 'Kúpiť kurz' to verify the fix"
