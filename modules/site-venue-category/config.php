<?php

return [
    '__name' => 'site-venue-category',
    '__version' => '0.1.0',
    '__git' => 'git@github.com:getmim/site-venue-category.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/site-venue-category' => ['install','update','remove'],
        'app/site-venue-category' => ['install','update','remove'],
        'theme/site/venue/category' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'venue-category' => NULL
            ],
            [
                'site' => NULL
            ],
            [
                'site-meta' => NULL
            ],
            [
                'venue' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'SiteVenueCategory\\Controller' => [
                'type' => 'file',
                'base' => ['app/site-venue-category/controller','modules/site-venue-category/controller'],
                'children' => 'modules/site-venue-category/controller'
            ],
            'SiteVenueCategory\\Meta' => [
                'type' => 'file',
                'base' => 'modules/site-venue-category/meta'
            ],
            'SiteVenueCategory\\Library' => [
                'type' => 'file',
                'base' => 'modules/site-venue-category/library'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'site' => [
            'siteVenueCategorySingle' => [
                'path' => [
                    'value' => '/venue/category/(:slug)',
                    'params' => [
                        'slug' => 'slug'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'SiteVenueCategory\\Controller\\Category::single'
            ],
            'siteVenueCategoryFeed' => [
                'path' => [
                    'value' => '/venue/category/(:slug)/feed.xml'
                ],
                'method' => 'GET',
                'handler' => 'SiteVenueCategory\\Controller\\Robot::feed'
            ]
        ]
    ],
    'libFormatter' => [
        'formats' => [
            'venue-category' => [
                'page' => [
                    'type' => 'router',
                    'router' => [
                        'name' => 'siteVenueCategorySingle',
                        'params' => [
                            'slug' => '$slug'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'site' => [
        'robot' => [
            'feed' => [
                'SiteVenueCategory\\Library\\Robot::feed' => TRUE
            ],
            'sitemap' => [
                'SiteVenueCategory\\Library\\Robot::sitemap' => TRUE
            ]
        ]
    ]
];