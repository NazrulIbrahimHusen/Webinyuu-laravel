<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index(Request $request)
    {
        $store = $request->attributes->get('store');

        return view('seller.shipping.index', [
            'setting' => $store->shippingSetting,
            'zones' => ShippingZone::orderBy('sort_order')->get(),
        ]);
    }

    public function saveSettings(Request $request)
    {
        $store = $request->attributes->get('store');
        $data = $request->validate([
            'sender_name' => 'nullable|string|max:100',
            'sender_phone' => 'nullable|string|max:20',
            'origin_address' => 'nullable|string|max:500',
            'postal_code' => 'nullable|string|max:10',
        ]);
        // Mode otomatis (RajaOngkir) dibuka di fase berikutnya, sementara selalu manual.
        $store->shippingSetting()->updateOrCreate([], $data + ['mode' => 'manual']);

        return back()->with('status', 'Pengaturan pengiriman disimpan.');
    }

    public function storeZone(Request $request)
    {
        ShippingZone::create($this->zone($request));

        return back()->with('status', 'Wilayah ditambahkan.');
    }

    public function updateZone(Request $request, string $id)
    {
        ShippingZone::findOrFail($id)->update($this->zone($request));

        return back()->with('status', 'Wilayah diperbarui.');
    }

    public function destroyZone(string $id)
    {
        ShippingZone::findOrFail($id)->delete();

        return back()->with('status', 'Wilayah dihapus.');
    }

    private function zone(Request $request): array
    {
        $d = $request->validate(['name' => 'required|string|max:80', 'fee' => 'required|integer|min:0|max:10000000', 'sort_order' => 'nullable|integer|min:0']);
        $d['sort_order'] = $d['sort_order'] ?? 0;

        return $d;
    }
}
