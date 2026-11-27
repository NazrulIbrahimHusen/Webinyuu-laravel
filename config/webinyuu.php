<?php

return [
    // Lama masa tenggang setelah langganan lewat jatuh tempo.
    'grace_days' => 3,

    // Batas waktu pembayaran invoice langganan.
    'invoice_due_hours' => 24,

    // Landing page: section yang boleh dipakai dan batas isi per paket (asumsi, PRD v1.0 belum mengatur).
    'landing' => [
        'sections' => [
            'starter' => ['hero', 'about', 'featured', 'contact'],
            'pro' => ['hero', 'about', 'featured', 'testimonial', 'faq', 'contact', 'gallery', 'promo'],
        ],
        'testimonials' => ['starter' => 0, 'pro' => 10],
        'faqs' => ['starter' => 0, 'pro' => 10],
        'gallery_max' => 6,
    ],

    // Akun admin awal (dipakai AdminSeeder). Ganti password setelah login pertama.
    'admin_email' => env('ADMIN_EMAIL', 'admin@webinyuu.test'),
    'admin_password' => env('ADMIN_PASSWORD', 'ganti-password-ini'),
];
