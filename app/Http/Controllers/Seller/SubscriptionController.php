<?php

namespace App\Http\Controllers\Seller;

use App\Enums\PlanCode;
use App\Enums\SubscriptionPeriod;
use App\Http\Controllers\Controller;
use App\Models\PlatformPaymentAccount;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use App\Services\PlanService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $subs, private PlanService $plans) {}

    private function own(Request $request, string $id): SubscriptionPayment
    {
        return SubscriptionPayment::with('plan')->where('user_id', $request->user()->id)->findOrFail($id);
    }

    public function index(Request $request)
    {
        $uid = $request->user()->id;

        return view('seller.subscription.index', [
            'current' => $this->plans->currentSubscription($uid),
            'effective' => $request->attributes->get('store')->effectivePlan(),
            'plans' => Plan::whereIn('code', [PlanCode::Starter->value, PlanCode::Pro->value])->orderBy('price_monthly')->get(),
            'invoices' => SubscriptionPayment::with('plan')->where('user_id', $uid)->latest()->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'plan_code' => ['required', Rule::in([PlanCode::Starter->value, PlanCode::Pro->value])],
            'period' => ['required', Rule::enum(SubscriptionPeriod::class)],
        ]);
        $invoice = $this->subs->createInvoice(
            $request->user(), Plan::where('code', $d['plan_code'])->firstOrFail(), SubscriptionPeriod::from($d['period'])
        );

        return redirect()->route('seller.subscription.show', $invoice->id);
    }

    public function show(Request $request, string $id)
    {
        return view('seller.subscription.invoice', [
            'invoice' => $this->own($request, $id),
            'accounts' => PlatformPaymentAccount::where('is_active', true)->get(),
        ]);
    }

    public function uploadProof(Request $request, string $id)
    {
        $invoice = $this->own($request, $id);
        $request->validate(['proof' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:4096']);
        $this->subs->uploadProof($invoice, $request->file('proof'));

        return back()->with('status', 'Bukti pembayaran terkirim. Admin akan memverifikasi.');
    }

    public function cancel(Request $request, string $id)
    {
        $this->subs->cancelInvoice($this->own($request, $id));

        return redirect()->route('seller.subscription.index')->with('status', 'Invoice dibatalkan.');
    }

    public function proof(Request $request, string $id)
    {
        $invoice = $this->own($request, $id);
        abort_unless($invoice->proof_path && Storage::disk('local')->exists($invoice->proof_path), 404);

        return Storage::disk('local')->response($invoice->proof_path);
    }
}
