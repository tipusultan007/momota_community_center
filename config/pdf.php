<?php

return [
    'mode'                  => 'utf-8',
    'format'                => 'A4',
    'author'                => '',
    'subject'               => '',
    'keywords'              => '',
    'creator'               => 'Laravel Pdf',
    'display_mode'          => 'fullpage',
    'tempDir'               => storage_path('app/temp'),
    'pdf_a'                 => false,
    'pdf_a_auto'            => false,
    'icc_profile_path'      => '',
    'custom_font_dir' => base_path('public/fonts/'),
    'custom_font_data' => [
        'hind_siliguri' => [
            'R'  => 'HindSiliguri-Regular.ttf',
            'B'  => 'HindSiliguri-Bold.ttf',
            'I'  => 'HindSiliguri-Regular.ttf',
            'BI' => 'HindSiliguri-Bold.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ]
    ]
];
