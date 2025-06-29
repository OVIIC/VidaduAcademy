#!/bin/bash
echo "=== Hero Matrix Animation Test ==="
echo "Testing new hero section..."

# Check frontend
curl -s -o /dev/null -w "Frontend: %{http_code}\n" "http://localhost:3002/"

# Check files
if [ -f "frontend/src/components/effects/MatrixRain.vue" ]; then
    echo "✅ MatrixRain component exists"
else
    echo "❌ MatrixRain component missing"
fi

if grep -q "MatrixRain" frontend/src/views/HomeView.vue; then
    echo "✅ HomeView updated with MatrixRain"
else
    echo "❌ HomeView missing MatrixRain"
fi

if grep -q "#3B3157" frontend/src/views/HomeView.vue; then
    echo "✅ Background color updated"
else
    echo "❌ Background color not found"
fi

echo "🎨 Open http://localhost:3002/ to see the Matrix animation!"
echo "=== Test Complete ==="
