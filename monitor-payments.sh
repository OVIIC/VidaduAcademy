#!/bin/bash

echo "=== MONITORING WEBHOOKS & PAYMENTS ==="
echo "Press Ctrl+C to stop"
echo ""

while true; do
    echo "$(date): Checking payment status..."
    
    # Count purchases by status
    COMPLETED=$(php -r "
        require 'backend/vendor/autoload.php';
        \$app = require 'backend/bootstrap/app.php';
        \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        echo App\Models\Purchase::where('status', 'completed')->count();
    ")
    
    PENDING=$(php -r "
        require 'backend/vendor/autoload.php';
        \$app = require 'backend/bootstrap/app.php';
        \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        echo App\Models\Purchase::where('status', 'pending')->count();
    ")
    
    ENROLLMENTS=$(php -r "
        require 'backend/vendor/autoload.php';
        \$app = require 'backend/bootstrap/app.php';
        \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        echo App\Models\Enrollment::count();
    ")
    
    echo "  Completed: $COMPLETED | Pending: $PENDING | Enrollments: $ENROLLMENTS"
    
    # Show recent webhook logs
    RECENT_WEBHOOKS=$(tail -10 backend/storage/logs/laravel.log | grep -c "webhook\|Stripe")
    if [ $RECENT_WEBHOOKS -gt 0 ]; then
        echo "  🔄 Recent webhook activity detected!"
        tail -5 backend/storage/logs/laravel.log | grep "webhook\|Stripe" | tail -1
    fi
    
    sleep 5
    echo ""
done
