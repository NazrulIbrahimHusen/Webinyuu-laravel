<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return view('seller.payment-methods.index', ['methods' => PaymentMethod::orderBy('sort_order')->get()]);
    }

    public function store(Request $request)
    {
        PaymentMethod::create($this->validated($request));

        return back()->with('status', 'Metode pembayaran ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $m = PaymentMethod::findOrFail($id);
        $m->update($this->validated($request, $m));

        return back()->with('status', 'Metode pembayaran diperbarui.');
    }

    public function destroy(string $id)
    {
        $m = PaymentMethod::findOrFail($id);
        if ($m->qris_image_path) {
            Storage::disk('public')->delete($m->qris_image_path);
        }
        $m->delete();

        return back()->with('status', 'Metode pembayaran dihapus.');
    }

    private function validated(Request $request, ?PaymentMethod $existing = null): array
    {
        $data = $request->validate([
            'type' => 'required|in:bank,ewallet,qris',
            'label' => 'required|string|max:80',
            'account_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50|required_if:type,bank,ewallet',
            'qris_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'instructions' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        if ($request->hasFile('qris_image')) {
            if ($existing?->qris_image_path) {
                Storage::disk('public')->delete($existing->qris_image_path);
            }
            $data['qris_image_path'] = $request->file('qris_image')->store('qris', 'public');
        }
        unset($data['qris_image']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
