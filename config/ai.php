<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Services Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for various AI services used in the application
    |
    */

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', 'AIzaSyDvV-0l_4wCKDwe73xOOCbmbmGCYiFNlyM'),
        'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        'model' => 'gemini-2.0-flash',
        'timeout' => 30,
        'max_tokens' => 1000,
    ],

    'evaluation' => [
        'categories' => [
            'sales_order' => 'Kinerja Pesanan Penjualan',
            'purchase_order' => 'Efisiensi Pesanan Pembelian',
            'financial_position' => 'Analisis Posisi Keuangan',
            'employee_attendance' => 'Kehadiran & Kinerja Karyawan',
        ],
        
        'prompts' => [
            'sales_order' => 'Analisis data kinerja pesanan penjualan dan berikan wawasan tentang tren penjualan, perilaku pelanggan, dan rekomendasi untuk peningkatan.',
            'purchase_order' => 'Evaluasi data efisiensi pesanan pembelian dan berikan wawasan tentang kinerja pemasok, optimasi biaya, dan rekomendasi pengadaan.',
            'financial_position' => 'Analisis data posisi keuangan dan berikan wawasan tentang kesehatan keuangan, tren, dan rekomendasi untuk manajemen keuangan.',
            'employee_attendance' => 'Evaluasi data kehadiran dan kinerja karyawan dan berikan wawasan tentang produktivitas, pola kehadiran, dan rekomendasi untuk peningkatan.',
        ],
    ],
];
