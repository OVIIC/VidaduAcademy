#!/bin/bash

# Render deployment fixes
echo "🔧 Committing Dockerfile fixes for Render..."

git add backend/Dockerfile frontend/Dockerfile
git commit -m "Fix: Dockerfile issues for Render deployment

- Fix npm timeout config (use timeout instead of network-timeout)
- Fix composer dump-autoload by setting permissions first
- Add error handling for autoload dump
- Optimize build order for better reliability"

git push origin main

echo "✅ Fixes pushed to GitHub"
echo "🎨 Ready for Render deployment retry!"
