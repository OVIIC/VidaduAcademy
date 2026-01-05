#!/bin/bash

echo "=== VidaduAcademy Deployment Status Check ==="
echo "Checking backend and frontend deployment status..."
echo

echo "1. Backend Health Check:"
echo "URL: https://vidaduacademy-backend.onrender.com/api/health"
curl -s -I https://vidaduacademy-backend.onrender.com/api/health | head -5
echo

echo "2. Backend Root Check:"
echo "URL: https://vidaduacademy-backend.onrender.com/"
curl -s -I https://vidaduacademy-backend.onrender.com/ | head -5
echo

echo "3. Frontend Check (if deployed):"
echo "URL: https://vidaduacademy-frontend.onrender.com/"
curl -s -I https://vidaduacademy-frontend.onrender.com/ | head -5
echo

echo "4. DNS Resolution Check:"
nslookup vidaduacademy-backend.onrender.com
echo

echo "=== Status Check Complete ==="
