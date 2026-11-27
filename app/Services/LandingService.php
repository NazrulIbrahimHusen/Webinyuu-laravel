<?php

namespace App\Services;

use App\Models\LandingPage;
use App\Models\Store;

class LandingService
{
    public const TYPES = ['hero', 'about', 'featured', 'testimonial', 'faq', 'contact', 'gallery', 'promo'];

    public const LABELS = [
        'hero' => 'Hero (judul utama)', 'about' => 'Tentang kami', 'featured' => 'Produk unggulan',
        'testimonial' => 'Testimoni', 'faq' => 'FAQ', 'contact' => 'Kontak', 'gallery' => 'Galeri', 'promo' => 'Banner promo',
    ];

    public function __construct(private PlanService $plans) {}

    /** Section yang boleh dipakai paket efektif saat ini. */
    public function allowedSections(Store $store): array
    {
        $plan = $store->effectivePlan();
        if (! $plan->hasFeature('landing_page')) {
            return [];
        }

        return config('webinyuu.landing.sections.'.$plan->code->value, config('webinyuu.landing.sections.starter'));
    }

    public function limits(Store $store): array
    {
        $plan = $store->effectivePlan();
        $code = $plan->code->value;

        return [
            'featured' => (int) $plan->max_featured_products,
            'testimonials' => (int) config("webinyuu.landing.testimonials.$code", 0),
            'faqs' => (int) config("webinyuu.landing.faqs.$code", 0),
            'gallery' => (int) config('webinyuu.landing.gallery_max', 6),
        ];
    }

    /** Buat landing page dan section default bila belum ada (juga saat paket naik ke Pro). */
    public function ensurePage(Store $store): LandingPage
    {
        $page = LandingPage::withoutGlobalScopes()->firstOrCreate(
            ['store_id' => $store->id],
            ['status' => 'draft', 'meta_title' => $store->name, 'meta_description' => mb_substr((string) $store->description, 0, 160)]
        );

        $allowed = $this->allowedSections($store);
        $existing = $page->sections()->pluck('type')->all();
        foreach (self::TYPES as $i => $type) {
            if (in_array($type, $allowed, true) && ! in_array($type, $existing, true)) {
                $page->sections()->create([
                    'type' => $type, 'content' => $this->defaults($type, $store),
                    'sort_order' => $i * 10, 'is_enabled' => in_array($type, ['hero', 'about', 'featured', 'contact'], true),
                ]);
            }
        }

        return $page;
    }

    private function defaults(string $type, Store $store): array
    {
        return match ($type) {
            'hero' => ['headline' => $store->name, 'subheadline' => mb_substr((string) $store->description, 0, 160), 'button_text' => 'Lihat Katalog'],
            'about' => ['title' => 'Tentang Kami', 'text' => (string) $store->description],
            'featured' => ['title' => 'Produk Unggulan'],
            'testimonial' => ['title' => 'Kata Pelanggan'],
            'faq' => ['title' => 'Pertanyaan Umum'],
            'contact' => ['title' => 'Hubungi Kami', 'text' => ''],
            'gallery' => ['title' => 'Galeri', 'images' => []],
            'promo' => ['title' => 'Promo Spesial', 'text' => '', 'button_text' => 'Pesan Sekarang', 'button_link' => ''],
        };
    }

    /**
     * Data tampilan publik. Saat paket turun, data tidak dihapus, hanya dibatasi di sini.
     */
    public function data(Store $store, LandingPage $page): array
    {
        $allowed = $this->allowedSections($store);
        $limits = $this->limits($store);

        $sections = $page->sections()->where('is_enabled', true)->whereIn('type', $allowed)->get();

        $ids = $page->featured()->limit($limits['featured'])->pluck('product_id');
        $featured = $this->plans->visibleProductsQuery($store)->with('images')->whereIn('id', $ids)->get()
            ->sortBy(fn ($p) => $ids->search($p->id))->values();

        return [
            'page' => $page,
            'sections' => $sections,
            'featured' => $featured,
            'testimonials' => $page->testimonials()->limit($limits['testimonials'])->get(),
            'faqs' => $page->faqs()->limit($limits['faqs'])->get(),
        ];
    }
}
