<?php
$menuBilling = [
        'settings-subscription' => [
            'title' => 'Subscription',
            'route' => 'settings-subscription',
            'icon' => 'fe fe-file-text'
        ],
    'settings-account' => [
            'title' => 'Personal',
            'route' => 'settings-personal',
            'icon' => 'fe fe-user',
        ],
    'settings-security' => [
            'title' => 'Security',
            'route' => 'settings-security',
            'icon' => 'fe fe-lock',
        ],
    'settings-business' => [
            'title' => 'Business',
            'route' => 'settings-business',
            'icon' => 'fe fe-briefcase',
        ],
    'settings-customization' => [
            'title' => 'Customization',
            'route' => 'settings-customization',
            'icon' => 'fe fe-layout',
        ],
    'settings-banking' => [
            'title' => 'Banking',
            'route' => 'settings-banking',
            'icon' => 'fe fe-home',
        ],
    'settings-access-grants' => [
            'title' => 'Permissions',
            'route' => 'settings-access-grants',
            'icon' => 'fe fe-unlock',
        ],
    'welcome-setup-overview' => [
            'title' => 'Setup &amp; Features',
            'route' => 'welcome-setup',
            'icon' => 'fe fe-power',
        ]

];

if (config('dorcas.edition','business') != "business" && config('dorcas.edition','business') != "community") {
    $menuBilling['settings-billing'] = [
        'title' => 'Billing',
        'route' => 'settings-billing',
        'icon' => 'fe fe-credit-card',
    ];
}


return $menuBilling;