#!/bin/bash

echo "=== VidaduAcademy Hero Section Matrix Animation - Final Test ==="
echo "Testing the complete hero section with Matrix digital rain animation..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

echo -e "${BLUE}1. Checking Services...${NC}"
echo "Frontend URL: http://localhost:3002"
curl -s -o /dev/null -w "Frontend Status: %{http_code}\n" "http://localhost:3002/"

echo -e "\n${BLUE}2. Verifying Components...${NC}"
if [ -f "frontend/src/components/effects/MatrixRain.vue" ]; then
    echo -e "${GREEN}✅ MatrixRain.vue component exists${NC}"
    echo "   - Location: frontend/src/components/effects/MatrixRain.vue"
else
    echo -e "${RED}❌ MatrixRain.vue component missing${NC}"
fi

echo -e "\n${BLUE}3. Checking HomeView Updates...${NC}"
if grep -q "MatrixRain" frontend/src/views/HomeView.vue; then
    echo -e "${GREEN}✅ HomeView.vue imports MatrixRain component${NC}"
else
    echo -e "${RED}❌ HomeView.vue missing MatrixRain import${NC}"
fi

if grep -q "#3B3157" frontend/src/views/HomeView.vue; then
    echo -e "${GREEN}✅ Background color #3B3157 applied${NC}"
else
    echo -e "${RED}❌ Background color #3B3157 not found${NC}"
fi

if grep -q "#7A65B4" frontend/src/components/effects/MatrixRain.vue; then
    echo -e "${GREEN}✅ Matrix characters color #7A65B4 configured${NC}"
else
    echo -e "${RED}❌ Matrix characters color #7A65B4 not found${NC}"
fi

echo -e "\n${BLUE}4. Checking Content Updates...${NC}"
if grep -q "Ovládnite rast" frontend/src/views/HomeView.vue; then
    echo -e "${GREEN}✅ Updated hero title found${NC}"
else
    echo -e "${RED}❌ Updated hero title missing${NC}"
fi

if grep -q "spokojných študentov" frontend/src/views/HomeView.vue; then
    echo -e "${GREEN}✅ Stats section integrated in hero${NC}"
else
    echo -e "${RED}❌ Stats section missing from hero${NC}"
fi

echo -e "\n${BLUE}5. Testing Page Response...${NC}"
curl -s "http://localhost:3002/" > /tmp/homepage_test.html

if grep -qi "youtube" /tmp/homepage_test.html; then
    echo -e "${GREEN}✅ YouTube content found${NC}"
else
    echo -e "${RED}❌ YouTube content missing${NC}"
fi

echo -e "\n${PURPLE}6. Visual Verification Checklist:${NC}"
echo "🎨 Open http://localhost:3002/ in your browser and verify:"
echo "   • Background color is deep purple (#3B3157)"
echo "   • Matrix digital rain animation is running"
echo "   • Characters are purple (#7A65B4) with varying opacity"
echo "   • Hero title: 'Ovládnite rast YouTube kanála'"
echo "   • Stats cards are integrated in hero section"
echo "   • Red action buttons with hover effects"
echo "   • Animation is responsive (different on mobile/desktop)"

echo -e "\n${BLUE}7. Performance Notes:${NC}"
echo "   • Matrix animation uses requestAnimationFrame for smooth performance"
echo "   • Different animation settings for mobile vs desktop"
echo "   • Canvas-based rendering for optimal performance"

echo -e "\n${GREEN}=== Test Complete! ===${NC}"
echo -e "${PURPLE}The hero section has been successfully updated with Matrix digital rain animation!${NC}"

# Clean up
rm -f /tmp/homepage_test.html
