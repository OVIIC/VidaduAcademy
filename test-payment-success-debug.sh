#!/bin/bash

echo "=== VidaduAcademy Payment Success Debug Test ==="
echo "Testing payment success page functionality..."

# Check if services are running
echo "1. Checking services..."
echo "Frontend URL: http://localhost:3002"
echo "Backend URL: http://localhost:8000"

# Test basic page access
echo -e "\n2. Testing basic page access..."
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" "http://localhost:3002/payment/success"

# Test with session ID parameter
echo -e "\n3. Testing with session_id parameter..."
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" "http://localhost:3002/payment/success?session_id=test123"

# Test API verification endpoint directly
echo -e "\n4. Testing API verification endpoint..."
curl -s -X POST -H "Content-Type: application/json" \
  -d '{"session_id":"test123"}' \
  "http://localhost:8000/api/payments/verify" | jq '.' 2>/dev/null || echo "Invalid JSON response or jq not available"

# Check if frontend is accessible
echo -e "\n5. Testing frontend main page..."
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" "http://localhost:3002/"

# Test course API
echo -e "\n6. Testing courses API..."
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" "http://localhost:8000/api/courses"

echo -e "\n=== Test Complete ==="
echo "If you see HTTP Status: 200 for most tests, the basic connectivity is working."
echo "If payment success page shows ERR_FAILED, it might be a frontend JavaScript error."
echo "Check the browser console for detailed error messages."
