<?php

namespace App\Http\Controllers\Seller;

use App\Enums\BusinessType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StoreProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('seller.store', ['store' => $request->attributes->get('store'), 'types' => BusinessType::cases()]);
    }

    public function update(Request $request)
    {
        $store = $request->attributes->get('store');

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'business_type' => ['required', Rule::enum(BusinessType::class)],
            'description' => 'nullable|string|max:2000',
            'address' => 'nullable|string|max:500',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'bg_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        // Kustomisasi warna hanya untuk paket yang mendukung.
        if (! $store->hasFeature('bg_color')) {
            unset($data['bg_color']);
        }

        if ($request->hasFile('logo')) {
            if ($store->logo_path) {
                Storage::disk('public')->delete($store->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }
        unset($data['logo']);
        $data['is_active'] = $request->boolean('is_active');

        $store->update($data);

        return back()->with('status', 'Profil katalog disimpan.');
    }
}
