<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return view('admin.plans.index', ['plans' => Plan::orderBy('price_monthly')->get(), 'featureList' => Plan::FEATURES]);
    }

    public function update(Request $request, string $id)
    {
        $plan = Plan::findOrFail($id);
        $d = $request->validate([
            'name' => 'required|string|max:40',
            'price_monthly' => 'required|integer|min:0|max:100000000',
            'price_yearly' => 'nullable|integer|min:0|max:1000000000',
            'max_products' => 'nullable|integer|min:1|max:1000000',
            'max_featured_products' => 'required|integer|min:0|max:1000',
            'features' => 'nullable|array',
            'features.*' => 'in:'.implode(',', array_keys(Plan::FEATURES)),
        ]);
        $features = [];
        foreach ((array) ($d['features'] ?? []) as $f) {
            $features[$f] = true;
        }
        // Harga baru hanya berlaku untuk invoice berikutnya, invoice lama menyimpan base_amount sendiri.
        $plan->update([
            'name' => $d['name'], 'price_monthly' => $d['price_monthly'], 'price_yearly' => $d['price_yearly'] ?? null,
            'max_products' => $d['max_products'] ?? null, 'max_featured_products' => $d['max_featured_products'], 'features' => $features,
        ]);

        return back()->with('status', "Paket {$plan->name} diperbarui.");
    }
}
