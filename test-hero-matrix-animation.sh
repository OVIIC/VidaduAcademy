#!/bin/bash

echo "=== VidaduAcademy Hero Section Matrix Animation Test ==="
echo "Testing new hero section with Matrix digital rain..."

# Check if frontend is running
echo "1. Checking if frontend is accessible..."
curl -s -o /dev/null -w "Frontend Status: %{http_code}\n" "http://localhost:3002/"

# Check if the Matrix animation component exists
echo -e "\n2. Checking Matrix component files..."
if [ -f "frontend/src/components/effects/MatrixRain.vue" ]; then
    echo "✅ MatrixRain.vue component exists"
else
    echo "❌ MatrixRain.vue component missing"
fi

# Check if HomeView has been updated
echo -e "\n3. Checking HomeView.vue updates..."
if grep -q "MatrixRain" frontend/src/views/HomeView.vue; then
    echo "✅ HomeView.vue contains MatrixRain import"
else
    echo "❌ HomeView.vue missing MatrixRain import"
fi

if grep -q "#3B3157" frontend/src/views/HomeView.vue; then
    echo "✅ New background color #3B3157 applied"
else
    echo "❌ Background color not updated"
fi

# Test page content
echo -e "\n4. Testing page content..."
curl -s "http://localhost:3002/" > /tmp/homepage_test.html

if grep -q "Ovládnite rast" /tmp/homepage_test.html; then
    echo "✅ Hero title found"
else
    echo "❌ Hero title missing"
fi

if grep -q "spokojných študentov" /tmp/homepage_test.html; then
    echo "✅ Stats section in hero found"
else
    echo "❌ Stats section missing from hero"
fi

# Check for JavaScript errors in the console (basic check)
echo -e "\n5. Basic validation..."
if curl -s "http://localhost:3002/" | grep -i "error" > /dev/null; then
    echo "⚠️  Possible errors found in HTML"
else
    echo "✅ No obvious errors in HTML output"
fi

echo -e "\n6. Visual Test Instructions:"
echo "📱 Open http://localhost:3002/ in your browser"
echo "🎨 You should see:"
echo "   - Purple background (#3B3157)"
echo "   - Matrix digital rain animation with purple characters (#7A65B4)"
echo "   - Updated hero title 'Ovládnite rast YouTube kanála'"
echo "   - Stats cards integrated in hero section"
echo "   - Red action buttons with hover effects"

echo -e "\n=== Test Complete ==="

# Clean up
rm -f /tmp/homepage_test.html
