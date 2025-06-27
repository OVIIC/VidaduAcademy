#!/bin/bash

# Test script for lesson progress functionality
BASE_URL="http://localhost:8000/api"

# 1. Register a test user
echo "1. Registering test user..."
register_response=$(curl -s -X POST "$BASE_URL/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }')

echo "Register response: $register_response"

# Extract token from response
TOKEN=$(echo $register_response | grep -o '"token":"[^"]*' | cut -d'"' -f4)
echo "Token: $TOKEN"

if [ -z "$TOKEN" ]; then
  echo "Failed to get token, trying login..."
  
  # Try login instead
  login_response=$(curl -s -X POST "$BASE_URL/login" \
    -H "Content-Type: application/json" \
    -d '{
      "email": "test@example.com",
      "password": "password123"
    }')
  
  echo "Login response: $login_response"
  TOKEN=$(echo $login_response | grep -o '"token":"[^"]*' | cut -d'"' -f4)
  echo "Token from login: $TOKEN"
fi

if [ -z "$TOKEN" ]; then
  echo "Failed to get authentication token"
  exit 1
fi

# 2. Enroll in a course
echo -e "\n2. Enrolling in course..."
enroll_response=$(curl -s -X POST "$BASE_URL/enroll-me" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "course_id": 1
  }')

echo "Enroll response: $enroll_response"

# 3. Get course content with lessons
echo -e "\n3. Getting course content..."
course_content=$(curl -s -X GET "$BASE_URL/learning/course/youtube-channel-growth-masterclass" \
  -H "Authorization: Bearer $TOKEN")

echo "Course content: $course_content"

# 4. Update lesson progress
echo -e "\n4. Updating lesson progress..."
progress_response=$(curl -s -X POST "$BASE_URL/learning/course/youtube-channel-growth-masterclass/lesson/introduction-to-youtube-growth/progress" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "completed": true,
    "watch_time_seconds": 900
  }')

echo "Progress update response: $progress_response"

# 5. Get updated course content to verify progress
echo -e "\n5. Getting updated course content..."
updated_content=$(curl -s -X GET "$BASE_URL/learning/course/youtube-channel-growth-masterclass" \
  -H "Authorization: Bearer $TOKEN")

echo "Updated course content: $updated_content"

echo -e "\nTest completed!"
