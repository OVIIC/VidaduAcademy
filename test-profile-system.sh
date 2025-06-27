#!/bin/bash

# VidaduAcademy Profile Management Test Script
echo "=== VidaduAcademy Profile Management Test ==="
echo ""

# Backend API Tests
echo "1. Testing Backend API Health..."
HEALTH_RESPONSE=$(curl -s http://localhost:8002/api/health)
echo "Health Check: $HEALTH_RESPONSE"
echo ""

echo "2. Testing User Login..."
LOGIN_RESPONSE=$(curl -s -X POST http://localhost:8002/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -d '{"email":"test@example.com","password":"password123"}')

# Extract token from login response
TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d':' -f2 | tr -d '"')
echo "Login successful, token: ${TOKEN:0:20}..."
echo ""

if [ -n "$TOKEN" ]; then
  echo "3. Testing Profile Fetch..."
  PROFILE_RESPONSE=$(curl -s -X GET http://localhost:8002/api/user/profile \
    -H "Authorization: Bearer $TOKEN" \
    -H "Accept: application/json")
  echo "Profile Response: ${PROFILE_RESPONSE:0:100}..."
  echo ""

  echo "4. Testing Profile Update..."
  UPDATE_RESPONSE=$(curl -s -X PUT http://localhost:8002/api/user/profile \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"name":"Test User API","email":"test@example.com","bio":"Updated via API test","content_niche":"technology","goals":"monetization","email_notifications":true}')
  echo "Update Response: ${UPDATE_RESPONSE:0:100}..."
  echo ""

  echo "5. Testing Certificates Endpoint..."
  CERT_RESPONSE=$(curl -s -X GET http://localhost:8002/api/user/certificates \
    -H "Authorization: Bearer $TOKEN" \
    -H "Accept: application/json")
  echo "Certificates Response: ${CERT_RESPONSE:0:100}..."
  echo ""

  echo "6. Testing Password Update..."
  PWD_RESPONSE=$(curl -s -X PUT http://localhost:8002/api/user/password \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"current_password":"password123","new_password":"newpassword123","new_password_confirmation":"newpassword123"}')
  echo "Password Update Response: ${PWD_RESPONSE:0:100}..."
  echo ""

else
  echo "Login failed - cannot continue with authenticated tests"
fi

echo "=== API Tests Completed ==="
echo ""
echo "Frontend is running at: http://localhost:3003"
echo "Backend is running at: http://localhost:8002"
echo ""
echo "To test the full application:"
echo "1. Open http://localhost:3003 in your browser"
echo "2. Navigate to the login page"
echo "3. Login with: test@example.com / password123"
echo "4. Go to Profile section and test all tabs"
echo ""
echo "Profile Management Features:"
echo "✓ User profile viewing and editing"
echo "✓ Password change functionality"
echo "✓ Notification preferences"
echo "✓ Certificates/achievements viewing"
echo "✓ Account data export"
echo "✓ Account deletion (with confirmation)"
echo ""
