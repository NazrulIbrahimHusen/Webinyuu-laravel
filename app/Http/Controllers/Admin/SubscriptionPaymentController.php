<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionPaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubscriptionPaymentController extends Controller
{
    public function __construct(private SubscriptionService $subs) {}

    public function index(Request $request)
    {
        $status = $request->query('status', SubscriptionPaymentStatus::WaitingVerification->value);
        $q = SubscriptionPayment::with(['user.store', 'plan'])->latest();
        if ($status !== 'all') {
            $q->where('status', $status);
        }
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('invoice_number', 'like', "%{$s}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")));
        }

        return view('admin.payments.index', [
            'payments' => $q->paginate(20)->withQueryString(),
            'status' => $status, 'statuses' => SubscriptionPaymentStatus::cases(),
        ]);
    }

    public function show(string $id)
    {
        $p = SubscriptionPayment::with(['user.store', 'plan', 'verifier', 'subscription'])->findOrFail($id);

        return view('admin.payments.show', ['p' => $p, 'isPdf' => str_ends_with((string) $p->proof_path, '.pdf')]);
    }

    public function proof(string $id)
    {
        $p = SubscriptionPayment::findOrFail($id);
        abort_unless($p->proof_path && Storage::disk('local')->exists($p->proof_path), 404);

        return Storage::disk('local')->response($p->proof_path);
    }

    public function verify(Request $request, string $id)
    {
        $this->subs->verify(SubscriptionPayment::findOrFail($id), $request->user());

        return redirect()->route('admin.payments.show', $id)->with('status', 'Pembayaran diverifikasi, langganan aktif.');
    }

    public function reject(Request $request, string $id)
    {
        $d = $request->validate(['reject_reason' => 'required|string|max:500']);
        $this->subs->reject(SubscriptionPayment::findOrFail($id), $request->user(), $d['reject_reason']);

        return redirect()->route('admin.payments.show', $id)->with('status', 'Pembayaran ditolak. Seller dapat mengunggah ulang.');
    }
}
