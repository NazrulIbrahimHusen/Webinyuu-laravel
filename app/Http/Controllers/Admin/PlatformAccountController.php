<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformPaymentAccount;
use Illuminate\Http\Request;

class PlatformAccountController extends Controller
{
    public function index()
    {
        return view('admin.accounts.index', ['accounts' => PlatformPaymentAccount::orderBy('id')->get()]);
    }

    public function store(Request $request)
    {
        PlatformPaymentAccount::create($this->data($request));

        return back()->with('status', 'Rekening ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        PlatformPaymentAccount::findOrFail($id)->update($this->data($request));

        return back()->with('status', 'Rekening diperbarui.');
    }

    public function destroy(string $id)
    {
        PlatformPaymentAccount::findOrFail($id)->delete();

        return back()->with('status', 'Rekening dihapus.');
    }

    private function data(Request $request): array
    {
        $d = $request->validate([
            'bank_name' => 'required|string|max:60', 'account_name' => 'required|string|max:100', 'account_number' => 'required|string|max:50',
        ]);
        $d['is_active'] = $request->boolean('is_active');

        return $d;
    }
}
