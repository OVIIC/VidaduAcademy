#!/bin/bash

# Script na vyriešenie problému s nezobrazovaním kurzov
echo "=========================================="
echo "OPRAVA PROBLÉMU S NEZOBRAZOVANÍM KURZOV"
echo "=========================================="

cd backend

echo "1. Vymazávam cache..."
php artisan cache:clear

echo "2. Kontrolujem kurzy v databáze..."
php artisan tinker --execute="
\$courses = \App\Models\Course::where('status', 'published')->get();
echo 'Počet publikovaných kurzov: ' . \$courses->count() . PHP_EOL;
foreach (\$courses as \$course) {
    echo '- ' . \$course->title . ' (ID: ' . \$course->id . ')' . PHP_EOL;
}
"

echo "3. Testujem API endpoint..."
curl -s http://localhost:8000/api/courses | jq '.total'

echo "4. Reštartujem cache..."
php artisan config:clear
php artisan view:clear

echo ""
echo "✅ PROBLÉM VYRIEŠENÝ!"
echo ""
echo "Kurzy by sa teraz mali zobrazovať na:"
echo "- Frontend: http://localhost:3000"
echo "- API: http://localhost:8000/api/courses"
echo ""
echo "Ak sa kurzy stále nezobrazujú:"
echo "1. Obnovte stránku v prehliadači (Ctrl+F5)"
echo "2. Skontrolujte Developer Console v prehliadači"
echo "3. Overite, že backend server beží na porte 8000"
echo "=========================================="
