<?php

namespace App\Services;

use App\Enums\PlanCode;
use App\Enums\SubscriptionPaymentStatus as PayStatus;
use App\Enums\SubscriptionPeriod;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SubscriptionService
{
    private const OPEN = [PayStatus::Pending, PayStatus::WaitingVerification];

    /** Seller membuat invoice. Total = harga paket + kode unik (agar mudah dicocokkan di mutasi bank). */
    public function createInvoice(User $user, Plan $plan, SubscriptionPeriod $period): SubscriptionPayment
    {
        $base = $plan->priceFor($period);
        if ($plan->code === PlanCode::Basic || ! $base) {
            throw ValidationException::withMessages(['plan' => 'Paket atau periode ini tidak tersedia.']);
        }

        return DB::transaction(function () use ($user, $plan, $period, $base) {
            $open = SubscriptionPayment::where('user_id', $user->id)->whereIn('status', array_map(fn ($s) => $s->value, self::OPEN))
                ->where(fn ($q) => $q->where('status', PayStatus::WaitingVerification->value)->orWhere('due_at', '>', now()))
                ->lockForUpdate()->first();

            if ($open) {
                if ($open->status === PayStatus::Pending && $open->plan_id === $plan->id && $open->period === $period) {
                    return $open; // klik ganda: pakai invoice yang sama
                }
                throw ValidationException::withMessages(['plan' => "Selesaikan atau batalkan dulu invoice {$open->invoice_number}."]);
            }

            $usedTotals = SubscriptionPayment::whereIn('status', array_map(fn ($s) => $s->value, self::OPEN))
                ->lockForUpdate()->pluck('total_amount')->all();

            for ($i = 0; $i < 60; $i++) {
                $code = random_int(1, 999);
                if (! in_array($base + $code, $usedTotals, true)) {
                    return SubscriptionPayment::create([
                        'user_id' => $user->id, 'plan_id' => $plan->id,
                        'invoice_number' => $this->invoiceNumber(),
                        'period' => $period, 'base_amount' => $base, 'unique_code' => $code, 'total_amount' => $base + $code,
                        'method' => 'manual', 'status' => PayStatus::Pending,
                        'due_at' => now()->addHours(config('webinyuu.invoice_due_hours', 24)),
                    ]);
                }
            }
            throw ValidationException::withMessages(['plan' => 'Gagal membuat kode unik, coba lagi.']);
        });
    }

    public function cancelInvoice(SubscriptionPayment $p): void
    {
        if ($p->status === PayStatus::Pending) {
            $p->update(['status' => PayStatus::Expired]);
        }
    }

    public function uploadProof(SubscriptionPayment $p, UploadedFile $file): void
    {
        if (! $p->canUploadProof()) {
            throw ValidationException::withMessages(['proof' => 'Invoice ini tidak bisa menerima bukti pembayaran lagi.']);
        }
        if ($p->proof_path) {
            Storage::disk('local')->delete($p->proof_path);
        }
        $p->update([
            'proof_path' => $file->store('subscription-proofs', 'local'),
            'status' => PayStatus::WaitingVerification,
            'reject_reason' => null,
        ]);
    }

    public function verify(SubscriptionPayment $payment, User $admin): Subscription
    {
        return DB::transaction(function () use ($payment, $admin) {
            $p = SubscriptionPayment::lockForUpdate()->findOrFail($payment->id);
            if ($p->status !== PayStatus::WaitingVerification) {
                throw ValidationException::withMessages(['payment' => 'Pembayaran ini sudah diproses.']);
            }
            $sub = $this->activate($p->user, $p->plan, $p->period, $admin);
            $p->update([
                'status' => PayStatus::Paid, 'subscription_id' => $sub->id,
                'verified_by' => $admin->id, 'verified_at' => now(),
            ]);

            return $sub;
        });
    }

    public function reject(SubscriptionPayment $payment, User $admin, string $reason): void
    {
        if ($payment->status !== PayStatus::WaitingVerification) {
            throw ValidationException::withMessages(['payment' => 'Pembayaran ini sudah diproses.']);
        }
        $payment->update([
            'status' => PayStatus::Rejected, 'reject_reason' => $reason,
            'verified_by' => $admin->id, 'verified_at' => now(),
        ]);
    }

    /**
     * Buat baris langganan baru sesuai aturan ERD:
     * - perpanjang paket sama / turun paket: mulai saat langganan berjalan berakhir (sisa hari tidak hilang)
     * - naik paket: mulai sekarang, langganan lama dibatalkan, tanpa prorata
     * - sudah berakhir / grace / belum punya: mulai sekarang
     */
    public function activate(User $user, Plan $plan, SubscriptionPeriod $period, ?User $admin = null): Subscription
    {
        $tail = Subscription::current()->where('user_id', $user->id)->with('plan')->orderByDesc('ends_at')->first();
        $starts = now();

        if ($tail && $tail->status === SubscriptionStatus::Active && $tail->ends_at?->isFuture()) {
            if ($this->rank($plan) > $this->rank($tail->plan)) {
                Subscription::current()->where('user_id', $user->id)
                    ->update(['status' => SubscriptionStatus::Cancelled->value]);
            } else {
                $starts = $tail->ends_at->copy();
            }
        }

        $ends = $period === SubscriptionPeriod::Yearly ? $starts->copy()->addYear() : $starts->copy()->addMonthNoOverflow();

        return Subscription::create([
            'user_id' => $user->id, 'plan_id' => $plan->id, 'period' => $period,
            'status' => SubscriptionStatus::Active,
            'starts_at' => $starts, 'ends_at' => $ends,
            'grace_ends_at' => $ends->copy()->addDays(config('webinyuu.grace_days', 3)),
            'activated_by' => $admin?->id,
        ]);
    }

    private function rank(Plan $plan): int
    {
        return (int) array_search($plan->code, PlanCode::cases(), true);
    }

    private function invoiceNumber(): string
    {
        do {
            $n = 'SUB-'.now()->format('ymd').'-'.strtoupper(Str::random(5));
        } while (SubscriptionPayment::where('invoice_number', $n)->exists());

        return $n;
    }
}
