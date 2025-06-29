# Payment Troubleshooting Guide

## Automatic Payment Recovery System

Tento systém zabezpečuje, že sa platby nikdy nestratia a používatelia dostanú svoje kurzy aj v prípade chýb.

### 1. Preventívne opatrenia

#### Retry mechanizmus vo frontend
- PaymentSuccessView má retry mechanizmus s exponenciálnym backoff
- Pokúša sa 3x overiť platbu s Stripe
- Aj v prípade zlyhania API zobrazí success správu

#### Rozšírené logovanie
- Všetky platby sa logujú v `storage/logs/laravel.log`
- Možno sledovať celý payment flow

#### Stripe webhook
- Webhook endpoint: `/api/stripe/webhook`
- Automaticky spracováva `checkout.session.completed` a `payment_intent.succeeded`
- Backup mechanizmus pre prípad zlyhania frontend API volania

### 2. Automatické opravy

#### Scheduled Jobs
- **Každú hodinu**: Kontrola posledných 24 hodín
- **Každú nedeľu**: Kontrola posledných 7 dní
- Command: `payments:fix-pending`

#### Manuálna oprava
```bash
# Kontrola posledných 7 dní (dry run)
php artisan payments:fix-pending --dry-run

# Skutočná oprava
php artisan payments:fix-pending

# Kontrola posledných 30 dní
php artisan payments:fix-pending --days=30
```

### 3. Ako debugovať problém

#### Krok 1: Nájdi používateľa a purchase
```php
// V Laravel Tinker
$user = User::where('email', 'email@example.com')->first();
$purchases = Purchase::where('user_id', $user->id)->get();
```

#### Krok 2: Skontroluj Stripe session
```php
\Stripe\Stripe::setApiKey(config('services.stripe.secret'));
$session = \Stripe\Checkout\Session::retrieve('session_id');
echo $session->payment_status; // should be 'paid'
```

#### Krok 3: Oprav manuálne ak potrebné
```php
// Ak je payment_status 'paid' ale purchase je 'pending'
$purchase->update(['status' => 'completed']);

// Vytvor enrollment ak neexistuje
Enrollment::create([
    'user_id' => $user->id,
    'course_id' => $course->id,
    'status' => 'active',
    'enrolled_at' => now(),
]);
```

### 4. Monitoring

#### Logy na sledovanie
```bash
# Všetky payment logy
tail -f storage/logs/laravel.log | grep -i payment

# Stripe webhook logy
tail -f storage/logs/laravel.log | grep -i stripe

# Verification logy
tail -f storage/logs/laravel.log | grep -i verification
```

#### Databáza monitoring
```sql
-- Pending purchases starší ako 1 hodina
SELECT * FROM purchases 
WHERE status = 'pending' 
AND created_at < NOW() - INTERVAL 1 HOUR;

-- Používatelia s purchase ale bez enrollment
SELECT p.*, u.name, u.email, c.title 
FROM purchases p
JOIN users u ON p.user_id = u.id
JOIN courses c ON p.course_id = c.id
LEFT JOIN enrollments e ON (e.user_id = p.user_id AND e.course_id = p.course_id)
WHERE p.status = 'completed' AND e.id IS NULL;
```

### 5. Riešenie častých problémov

#### Problém: Payment success stránka sa nezobrazuje
- Skontroluj service worker cache
- Skontroluj CORS nastavenia
- Skontroluj autentifikáciu

#### Problém: API volanie zlyháva
- Skontroluj auth token v localStorage
- Skontroluj CSRF token
- Skontroluj network errors v devtools

#### Problém: Stripe webhook nechodí
- Skontroluj webhook endpoint URL v Stripe dashboard
- Skontroluj webhook secret v `.env`
- Skontroluj logy pre webhook errors

### 6. Štartovanie scheduled jobs

V produkcii pridaj do crontab:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

V development můžeš spustiť:
```bash
php artisan schedule:work
```

### 7. Monitoring dashboard

Môžeš vytvoriť jednoduchý monitoring endpoint:
```php
// V web.php alebo api.php
Route::get('/admin/payment-status', function() {
    $pendingCount = Purchase::where('status', 'pending')
        ->where('created_at', '>', now()->subHours(1))
        ->count();
    
    return response()->json([
        'pending_payments_last_hour' => $pendingCount,
        'status' => $pendingCount > 0 ? 'warning' : 'ok'
    ]);
});
```

### 8. Testovacie scenáre

#### Test 1: Štandardný flow
1. Vytvor purchase
2. Spusti Stripe checkout
3. Dokončíš platbu
4. Skontroluj payment success stránku
5. Verifikuj enrollment

#### Test 2: Retry mechanizmus
1. Dočasne vypni backend
2. Dokončíš Stripe platbu
3. Zapni backend
4. Skontroluj, či retry funguje

#### Test 3: Webhook backup
1. Dočasne vypni frontend
2. Dokončíš Stripe platbu
3. Skontroluj, či webhook vytvoril enrollment

#### Test 4: Manuálna oprava
1. Vytvor "stuck" purchase
2. Spusti `payments:fix-pending`
3. Verifikuj opravu
