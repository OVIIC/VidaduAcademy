#!/bin/bash

echo "=== VidaduAcademy Admin Panel Access Test ==="
echo

# Test 1: Check if admin panel is accessible
echo "1. Testing admin panel accessibility..."
admin_status=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/admin)
if [ "$admin_status" = "302" ]; then
    echo "✅ Admin panel is accessible (redirecting to login)"
else
    echo "❌ Admin panel returned status: $admin_status"
fi
echo

# Test 2: Verify admin user credentials via API
echo "2. Testing admin user authentication via API..."
api_response=$(curl -s -X POST http://localhost:8000/api/auth/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@example.com","password":"password123"}')

if echo "$api_response" | grep -q "access_token"; then
    echo "✅ Admin user can authenticate via API"
    echo "   API Response: $(echo "$api_response" | jq -r '.message // .user.email // "Success"' 2>/dev/null || echo "Authentication successful")"
else
    echo "❌ Admin user authentication failed"
    echo "   API Response: $api_response"
fi
echo

# Test 3: Check user roles and permissions
echo "3. Checking admin user roles and permissions..."
cd /Users/hermes/Documents/GitHub/VidaduAcademy/backend
role_check=$(php artisan tinker --execute="
\$user = App\Models\User::where('email', 'test@example.com')->first();
if (\$user) {
    echo 'Email: ' . \$user->email . PHP_EOL;
    echo 'Roles: ' . \$user->getRoleNames()->implode(', ') . PHP_EOL;
    echo 'Admin Role: ' . (\$user->hasRole('admin') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Admin Panel Access: ' . (\$user->can('access admin panel') ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo 'User not found' . PHP_EOL;
}
" 2>/dev/null)

echo "$role_check"
echo

echo "=== Admin Panel Login Instructions ==="
echo "1. Open: http://localhost:8000/admin"
echo "2. Use these credentials:"
echo "   Email: test@example.com"
echo "   Password: password123"
echo
echo "If you're still having issues, please check:"
echo "- Browser cookies are enabled"
echo "- No browser extensions blocking the login"
echo "- Try incognito/private browsing mode"
echo

echo "=== Alternative Admin User (if needed) ==="
echo "You can also create a dedicated admin user with:"
echo "cd backend && php artisan tinker"
echo "Then run:"
echo "\$admin = App\Models\User::create(['name' => 'Admin User', 'email' => 'admin@vidaduacademy.com', 'password' => bcrypt('admin123')]);"
echo "\$admin->assignRole('admin');"
echo
