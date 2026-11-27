<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\LandingTestimonial;
use App\Models\Product;
use App\Services\LandingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LandingController extends Controller
{
    private const FIELDS = [
        'hero' => ['headline', 'subheadline', 'button_text'],
        'about' => ['title', 'text'],
        'featured' => ['title'],
        'testimonial' => ['title'],
        'faq' => ['title'],
        'contact' => ['title', 'text'],
        'gallery' => ['title'],
        'promo' => ['title', 'text', 'button_text', 'button_link'],
    ];

    public function __construct(private LandingService $landing) {}

    public function edit(Request $request)
    {
        $store = $request->attributes->get('store');
        $page = $this->landing->ensurePage($store);
        $allowed = $this->landing->allowedSections($store);

        return view('seller.landing.edit', [
            'store' => $store, 'page' => $page,
            'sections' => $page->sections()->whereIn('type', $allowed)->get(),
            'locked' => array_values(array_diff(LandingService::TYPES, $allowed)),
            'limits' => $this->landing->limits($store),
            'products' => Product::where('status', 'published')->orderBy('name')->get(['id', 'name']),
            'featuredIds' => $page->featured()->pluck('product_id')->all(),
            'testimonials' => $page->testimonials()->get(),
            'faqs' => $page->faqs()->get(),
            'labels' => LandingService::LABELS,
        ]);
    }

    public function update(Request $request)
    {
        $store = $request->attributes->get('store');
        $page = $this->landing->ensurePage($store);
        $allowed = $this->landing->allowedSections($store);
        $limits = $this->landing->limits($store);

        $d = $request->validate([
            'status' => ['required', Rule::in(['draft', 'published'])],
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'sections' => 'nullable|array',
            'sections.*.enabled' => 'nullable|boolean',
            'sections.*.sort_order' => 'nullable|integer|min:0|max:99',
            'sections.*.title' => 'nullable|string|max:100',
            'sections.*.headline' => 'nullable|string|max:120',
            'sections.*.subheadline' => 'nullable|string|max:250',
            'sections.*.text' => 'nullable|string|max:2000',
            'sections.*.button_text' => 'nullable|string|max:40',
            'sections.*.button_link' => 'nullable|url:http,https|max:255',
            'image_hero' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_about' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images' => 'nullable|array|max:6',
            'gallery_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_remove' => 'nullable|array',
            'gallery_remove.*' => 'integer',
            'featured_ids' => 'nullable|array',
            'featured_ids.*' => 'integer',
        ]);

        $enabledCount = collect($allowed)->filter(fn ($t) => ! empty($d['sections'][$t]['enabled']))->count();
        if ($d['status'] === 'published' && $enabledCount === 0) {
            throw ValidationException::withMessages(['status' => 'Aktifkan minimal satu section sebelum dipublikasikan.']);
        }

        $featuredIds = collect($d['featured_ids'] ?? [])->unique()->values();
        if ($request->has('featured_present')) {
            if ($featuredIds->count() > $limits['featured']) {
                throw ValidationException::withMessages(['featured_ids' => "Produk unggulan maksimal {$limits['featured']} di paket Anda."]);
            }
            $featuredIds = Product::where('status', 'published')->whereIn('id', $featuredIds)->pluck('id')
                ->sortBy(fn ($id) => $featuredIds->search($id))->values();
        }

        DB::transaction(function () use ($request, $page, $d, $allowed, $limits, $featuredIds) {
            $page->update(['status' => $d['status'], 'meta_title' => $d['meta_title'] ?? null, 'meta_description' => $d['meta_description'] ?? null]);

            foreach ($allowed as $type) {
                $section = $page->sections()->where('type', $type)->first();
                if (! $section) {
                    continue;
                }
                $in = $d['sections'][$type] ?? [];
                $content = $section->content ?? [];
                foreach (self::FIELDS[$type] as $f) {
                    if (array_key_exists($f, $in)) {
                        $content[$f] = $in[$f];
                    }
                }

                if (in_array($type, ['hero', 'about'], true) && $request->hasFile("image_$type")) {
                    $this->deleteFile($content['image'] ?? null);
                    $content['image'] = $request->file("image_$type")->store('landing', 'public');
                }
                if ($type === 'gallery') {
                    $images = $content['images'] ?? [];
                    foreach ((array) ($d['gallery_remove'] ?? []) as $idx) {
                        if (isset($images[$idx])) {
                            $this->deleteFile($images[$idx]);
                            unset($images[$idx]);
                        }
                    }
                    $images = array_values($images);
                    foreach ((array) $request->file('gallery_images', []) as $file) {
                        if (count($images) < $limits['gallery']) {
                            $images[] = $file->store('landing', 'public');
                        }
                    }
                    $content['images'] = $images;
                }

                $section->update([
                    'content' => $content,
                    'is_enabled' => ! empty($in['enabled']),
                    'sort_order' => (int) ($in['sort_order'] ?? $section->sort_order),
                ]);
            }

            if ($request->has('featured_present') && in_array('featured', $allowed, true)) {
                $page->featured()->delete();
                foreach ($featuredIds as $i => $pid) {
                    $page->featured()->create(['product_id' => $pid, 'sort_order' => $i]);
                }
            }
        });

        return back()->with('status', $d['status'] === 'published' ? 'Landing page disimpan dan dipublikasikan.' : 'Landing page disimpan sebagai draf.');
    }

    public function preview(Request $request)
    {
        $store = $request->attributes->get('store');
        $page = $this->landing->ensurePage($store);

        return view('store.landing', $this->landing->data($store, $page) + ['store' => $store, 'cartCount' => 0, 'preview' => true]);
    }

    public function storeTestimonial(Request $request)
    {
        $store = $request->attributes->get('store');
        $page = $this->landing->ensurePage($store);
        abort_unless(in_array('testimonial', $this->landing->allowedSections($store), true), 403);

        $d = $request->validate(['name' => 'required|string|max:80', 'content' => 'required|string|max:500', 'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024']);
        if ($page->testimonials()->count() >= $this->landing->limits($store)['testimonials']) {
            return back()->withErrors(['name' => 'Batas testimoni paket Anda sudah tercapai.']);
        }
        $page->testimonials()->create([
            'name' => $d['name'], 'content' => $d['content'],
            'photo_path' => $request->hasFile('photo') ? $request->file('photo')->store('landing', 'public') : null,
            'sort_order' => (int) $page->testimonials()->max('sort_order') + 1,
        ]);

        return back()->with('status', 'Testimoni ditambahkan.');
    }

    public function destroyTestimonial(Request $request, string $id)
    {
        $page = $this->landing->ensurePage($request->attributes->get('store'));
        $t = $page->testimonials()->findOrFail($id);
        $this->deleteFile($t->photo_path);
        $t->delete();

        return back()->with('status', 'Testimoni dihapus.');
    }

    public function storeFaq(Request $request)
    {
        $store = $request->attributes->get('store');
        $page = $this->landing->ensurePage($store);
        abort_unless(in_array('faq', $this->landing->allowedSections($store), true), 403);

        $d = $request->validate(['question' => 'required|string|max:200', 'answer' => 'required|string|max:1000']);
        if ($page->faqs()->count() >= $this->landing->limits($store)['faqs']) {
            return back()->withErrors(['question' => 'Batas FAQ paket Anda sudah tercapai.']);
        }
        $page->faqs()->create($d + ['sort_order' => (int) $page->faqs()->max('sort_order') + 1]);

        return back()->with('status', 'FAQ ditambahkan.');
    }

    public function destroyFaq(Request $request, string $id)
    {
        $page = $this->landing->ensurePage($request->attributes->get('store'));
        $page->faqs()->findOrFail($id)->delete();

        return back()->with('status', 'FAQ dihapus.');
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
