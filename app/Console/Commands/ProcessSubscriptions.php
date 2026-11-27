<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionPaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Console\Command;

class ProcessSubscriptions extends Command
{
    protected $signature = 'subscriptions:process';
    protected $description = 'Ubah status langganan, kirim pengingat, kedaluwarsakan invoice.';

    private function graceDays(): int { return (int) config('webinyuu.grace_days', 3); }

    public function handle(): int
    {
        // 1. active -> grace
        Subscription::where('status', SubscriptionStatus::Active->value)
            ->whereNotNull('ends_at')->where('ends_at', '<', now())
            ->each(fn ($s) => $s->update([
                'status' => SubscriptionStatus::Grace,
                'grace_ends_at' => $s->ends_at->copy()->addDays($this->graceDays()),
            ]));

        // 2. grace -> expired
        Subscription::where('status', SubscriptionStatus::Grace->value)
            ->where('grace_ends_at', '<', now())
            ->update(['status' => SubscriptionStatus::Expired->value]);

        // 3. Pengingat H-7, H-3, H-1, H-0
        foreach ([7, 3, 1, 0] as $day) {
            Subscription::where('status', SubscriptionStatus::Active->value)
                ->whereDate('ends_at', now()->addDays($day)->toDateString())
                ->where(fn ($q) => $q->whereNull('last_reminder_day')->orWhere('last_reminder_day', '>', $day))
                ->each(function ($s) use ($day) {
                    // TODO: kirim notifikasi ke $s->user
                    $s->update(['last_reminder_day' => $day]);
                });
        }

        // 4. Invoice pending lewat batas
        SubscriptionPayment::where('status', SubscriptionPaymentStatus::Pending->value)
            ->where('due_at', '<', now())
            ->update(['status' => SubscriptionPaymentStatus::Expired->value]);

        return self::SUCCESS;
    }
}
