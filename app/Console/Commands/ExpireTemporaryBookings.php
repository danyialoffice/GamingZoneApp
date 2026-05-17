<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ExpireTemporaryBookings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:expire-temporary';

    /**
     * The console command description.
     */
    protected $description = 'Expire temporary bookings that have passed their expiration time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredBookings = Booking::where('status', 'temporary')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        $this->info("Expired {$expiredBookings} temporary booking(s).");

        return Command::SUCCESS;
    }
}
