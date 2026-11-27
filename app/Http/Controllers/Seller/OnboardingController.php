<?php

namespace App\Http\Controllers\Seller;

use App\Enums\BusinessType;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Rules\ReservedSlug;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function create(Request $request)
    {
        if ($request->user()->store) {
            return redirect()->route('dashboard');
        }

        return view('seller.onboarding', ['types' => BusinessType::cases()]);
    }

    public function store(Request $request)
    {
        abort_if($request->user()->store, 403);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => ['required', 'string', 'min:3', 'max:40', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:stores,slug', new ReservedSlug],
            'business_type' => ['required', Rule::enum(BusinessType::class)],
            'whatsapp' => 'nullable|string|max:20',
        ], ['slug.regex' => 'URL hanya boleh huruf kecil, angka, dan tanda hubung.']);

        Store::create($data + ['user_id' => $request->user()->id, 'email' => $request->user()->email]);

        return redirect()->route('dashboard')->with('status', 'Katalog berhasil dibuat.');
    }
}
