#!/bin/bash

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

function show_help {
    echo -e "${BLUE}VidaduAcademy Script Manager${NC}"
    echo "Usage: ./manager.sh [category] [command]"
    echo ""
    echo -e "${GREEN}Categories:${NC}"
    echo "  dev       - Development setup and checks"
    echo "  deploy    - Deployment to Render/Railway"
    echo "  fix       - Emergency fixes and repairs"
    echo "  monitor   - System monitoring"
    echo "  test      - Testing tools"
    echo ""
    echo -e "${GREEN}Examples:${NC}"
    echo "  ./manager.sh dev setup          (Runs scripts/dev/setup.sh)"
    echo "  ./manager.sh fix 502            (Runs scripts/fix/error-502-fix.sh)"
    echo "  ./manager.sh deploy render      (Runs scripts/deploy/render-setup.sh)"
    echo ""
    exit 0
}

if [ -z "$1" ]; then
    show_help
fi

CATEGORY=$1
COMMAND=$2

# Validate Category
if [ ! -d "scripts/$CATEGORY" ]; then
    echo -e "${RED}Error: Category '$CATEGORY' not found.${NC}"
    show_help
fi

# List commands if only category provided
if [ -z "$COMMAND" ]; then
    echo -e "${BLUE}Available commands in '$CATEGORY':${NC}"
    ls "scripts/$CATEGORY" | sed 's/\.sh//'
    exit 0
fi

# Find the script (fuzzy match)
SCRIPT=$(find "scripts/$CATEGORY" -name "*$COMMAND*.sh" | head -n 1)

if [ -z "$SCRIPT" ]; then
    echo -e "${RED}Error: Command '$COMMAND' not found in '$CATEGORY'.${NC}"
    echo "Available scripts:"
    ls "scripts/$CATEGORY"
    exit 1
fi

echo -e "${GREEN}Running $SCRIPT...${NC}"
chmod +x "$SCRIPT"
./"$SCRIPT"
