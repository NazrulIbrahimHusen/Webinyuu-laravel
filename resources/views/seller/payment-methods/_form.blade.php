@php $m = $m ?? null; @endphp
<div class="grid md:grid-cols-3 gap-2">
    <select name="type" class="border rounded px-2 py-1.5 text-sm">
        @foreach (['bank' => 'Transfer bank', 'ewallet' => 'E-wallet', 'qris' => 'QRIS'] as $v => $l)<option value="{{ $v }}" @selected(($m?->type) === $v)>{{ $l }}</option>@endforeach
    </select>
    <input name="label" value="{{ $m?->label }}" required placeholder="Label (mis. BCA)" class="border rounded px-2 py-1.5 text-sm">
    <input name="account_name" value="{{ $m?->account_name }}" placeholder="Atas nama" class="border rounded px-2 py-1.5 text-sm">
    <input name="account_number" value="{{ $m?->account_number }}" placeholder="Nomor rekening / HP" class="border rounded px-2 py-1.5 text-sm">
    <input name="sort_order" type="number" min="0" value="{{ $m?->sort_order ?? 0 }}" placeholder="Urutan" class="border rounded px-2 py-1.5 text-sm">
    <input type="file" name="qris_image" accept="image/*" class="text-xs">
</div>
<textarea name="instructions" rows="2" placeholder="Instruksi pembayaran (opsional)" class="mt-2 w-full border rounded px-2 py-1.5 text-sm">{{ $m?->instructions }}</textarea>
<label class="flex items-center gap-2 text-sm mt-2"><input type="checkbox" name="is_active" value="1" @checked($m ? $m->is_active : true)> Aktif</label>
