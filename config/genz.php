<?php

return [
    'name' => 'GenZ Consultants',
    'tagline' => 'Global Education and Career Development',
    'public_prices' => false,
    'demo_seed_enabled' => env('GENZ_DEMO_SEEDING', false),
    'demo_database' => env('GENZ_DEMO_DATABASE', 'genz_crm_lms_demo'),
    'contact' => [
        'is_demo' => true,
        'phone' => '+92 300 000 0000',
        'whatsapp' => '+92 300 000 0000',
        'email' => 'hello@genzconsultants.example',
        'address' => 'Demo office, Lahore, Pakistan — replace before launch',
        'hours' => 'Monday–Saturday, 10:00 AM–6:00 PM PKT',
        'socials' => [
            'facebook' => null,
            'instagram' => null,
            'linkedin' => null,
        ],
    ],
    'registration' => [
        'branches' => ['Lahore', 'Karachi', 'Islamabad', 'Online'],
        'currencies' => ['PKR' => 'Pakistani Rupee (PKR)', 'USD' => 'US Dollar (USD)', 'EUR' => 'Euro (EUR)'],
        // Illustrative demo amounts from the supplied registration-form reference.
        // These are not approved/live consultancy prices.
        'fee_schedules' => [
            'standard' => ['label' => 'Standard Study Programs', 'fees' => [
                ['destination' => 'UK', 'amount' => 40000, 'currency' => 'PKR'],
                ['destination' => 'Turkey', 'amount' => 50000, 'currency' => 'PKR'],
                ['destination' => 'Malaysia', 'amount' => 30000, 'currency' => 'PKR'],
                ['destination' => 'Europe', 'amount' => 150000, 'currency' => 'PKR'],
                ['destination' => 'USA', 'amount' => 150000, 'currency' => 'PKR'],
            ]],
            'scholarship' => ['label' => 'Fully Funded Scholarship Programs', 'fees' => [
                ['destination' => 'Europe', 'amount' => 1500, 'currency' => 'EUR'],
                ['destination' => 'USA', 'amount' => 2000, 'currency' => 'USD'],
                ['destination' => 'China', 'amount' => 2000, 'currency' => 'USD'],
                ['destination' => 'Turkey', 'amount' => 2000, 'currency' => 'USD'],
            ]],
        ],
    ],
    'colors' => [
        'primary' => '#073579',
        'secondary' => '#16BCF3',
        'background' => '#F4F9FF',
        'surface' => '#FFFFFF',
        'text' => '#142945',
        'muted' => '#64748B',
        'border' => '#E3EAF3',
    ],
    'features' => [
        'crm' => true,
        'lms' => true,
        'admissions' => true,
        'consultations' => true,
        'certificates' => true,
    ],
    'roles' => [
        'instructor' => 6,
        'finance' => 7,
        'cms_editor' => 8,
    ],
    'authorization' => [
        'staff' => ['Manage Students', 'Manage Service', 'Manage Consultations', 'Manage Free Consultations', 'Manage Leads', 'Manage Admissions'],
        'consultant' => ['Manage Leads', 'Manage Student Documents', 'Manage Admissions', 'Manage Consultations'],
        'instructor' => ['Manage Courses'],
        'finance' => ['Manage Transaction'],
        'cms_editor' => ['Manage Cms Settings', 'Manage Events'],
    ],
];
