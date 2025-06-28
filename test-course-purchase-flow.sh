#!/bin/bash

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
