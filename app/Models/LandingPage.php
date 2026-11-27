<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use BelongsToStore;

    protected $guarded = [];
    protected $casts = ['theme' => 'array'];

    public function sections() { return $this->hasMany(LandingSection::class)->orderBy('sort_order'); }
    public function featured() { return $this->hasMany(LandingFeaturedProduct::class)->orderBy('sort_order'); }
    public function testimonials() { return $this->hasMany(LandingTestimonial::class)->orderBy('sort_order'); }
    public function faqs() { return $this->hasMany(LandingFaq::class)->orderBy('sort_order'); }
}
