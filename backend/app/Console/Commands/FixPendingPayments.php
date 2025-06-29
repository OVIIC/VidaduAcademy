<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\Enrollment;
use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class FixPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:fix-pending 
                           {--days=7 : Check payments from last N days}
                           {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix pending payments that were actually completed in Stripe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        
        $this->info("Checking pending payments from last {$days} days...");
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
        
        // Find pending purchases from last N days
        $pendingPurchases = Purchase::where('status', 'pending')
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('stripe_session_id')
            ->with(['user', 'course'])
            ->get();
            
        $this->info("Found {$pendingPurchases->count()} pending purchases");
        
        $fixed = 0;
        $failed = 0;
        
        foreach ($pendingPurchases as $purchase) {
            try {
                $this->line("Checking purchase #{$purchase->id} for {$purchase->user->name}...");
                
                // Check Stripe session
                $session = Session::retrieve($purchase->stripe_session_id);
                
                if ($session->payment_status === 'paid') {
                    $this->info("  ✅ Payment is paid in Stripe");
                    
                    if (!$dryRun) {
                        // Update purchase
                        $purchase->update([
                            'status' => 'completed',
                            'stripe_payment_intent_id' => $session->payment_intent,
                            'purchased_at' => now(),
                        ]);
                        
                        // Create enrollment if doesn't exist
                        $enrollment = Enrollment::where('user_id', $purchase->user_id)
                            ->where('course_id', $purchase->course_id)
                            ->first();
                            
                        if (!$enrollment) {
                            Enrollment::create([
                                'user_id' => $purchase->user_id,
                                'course_id' => $purchase->course_id,
                                'status' => 'active',
                                'enrolled_at' => now(),
                            ]);
                            $this->info("  📚 Enrollment created");
                        } else {
                            $this->info("  📚 Enrollment already exists");
                        }
                        
                        $this->info("  🔄 Purchase updated to completed");
                    } else {
                        $this->info("  🔄 Would update purchase to completed");
                        $enrollment = Enrollment::where('user_id', $purchase->user_id)
                            ->where('course_id', $purchase->course_id)
                            ->first();
                        if (!$enrollment) {
                            $this->info("  📚 Would create enrollment");
                        }
                    }
                    
                    $fixed++;
                } else {
                    $this->warn("  ❌ Payment not completed in Stripe (status: {$session->payment_status})");
                }
                
            } catch (\Exception $e) {
                $this->error("  ❌ Error checking purchase #{$purchase->id}: {$e->getMessage()}");
                $failed++;
            }
        }
        
        $this->newLine();
        $this->info("Summary:");
        $this->info("- Fixed: {$fixed}");
        $this->info("- Failed: {$failed}");
        $this->info("- Total checked: {$pendingPurchases->count()}");
        
        if ($dryRun) {
            $this->warn('No changes were made (dry run mode)');
            $this->info('Run without --dry-run to apply fixes');
        }
        
        return Command::SUCCESS;
    }
}
