<?php
/**
 * VenueCategory
 * @package site-venue-category
 * @version 0.0.1
 */

namespace SiteVenueCategory\Meta;

class VenueCategory{
    static function single(object $category, array $venues, int $total, int $page, int $rpp){
        $result = [
            'head' => [],
            'foot' => []
        ];

        $home_url = \Mim::$app->router->to('siteHome');

        // reset meta
        if(!is_object($category->meta))
            $category->meta = (object)[];

        $def_meta = [
            'title'         => $category->name,
            'description'   => $category->content->chars(160),
            'schema'        => 'ItemList',
            'keyword'       => ''
        ];

        foreach($def_meta as $key => $value){
            if(!isset($category->meta->$key) || !$category->meta->$key)
                $category->meta->$key = $value;
        }

        $result['head'] = [
            'description'       => $category->meta->description,
            'published_time'    => $category->created,
            'schema.org'        => [],
            'type'              => 'website',
            'title'             => $category->meta->title,
            'updated_time'      => $category->updated,
            'url'               => $category->page,
            'metas'             => []
        ];

        // schema breadcrumbList
        $result['head']['schema.org'][] = [
            '@context'  => 'http://schema.org',
            '@type'     => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'item' => [
                        '@id' => $home_url,
                        'name' => \Mim::$app->config->name
                    ]
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'item' => [
                        '@id' => $home_url . '#venue',
                        'name' => 'Venue'
                    ]
                ]
            ]
        ];

        // schema page
        $title_items = [];
        $position_start = ( $page - 1 ) * $rpp;
        foreach($venues as $venue){
            $venue_schema = [
                '@type' => 'ListItem',
                'position' => ++$position_start,
                'item' => [
                    '@type' => 'LocalBusiness',
                    'name' => $venue->title,
                    'url'  => $venue->page
                ]
            ];
            if($venue->logo->target){
                $venue_schema['item']['image'] = [
                    '@context'   => 'http://schema.org',
                    '@type'      => 'ImageObject',
                    'contentUrl' => $venue->logo,
                    'url'        => $venue->logo
                ];
            }

            $price = $venue->prices;
            $venue_schema['item']['priceRange'] = sprintf('%s %d - %s %d', $price->currency, $price->min, $price->currency, $price->max);

            $contact = $venue->contact;
            if(isset($contact->phone) && $contact->phone)
                $venue_schema['item']['telephone'] = $contact->phone;
            if(isset($contact->address) && $contact->address)
                $venue_schema['item']['address'] = $contact->address;

            $title_items[] = $venue_schema;
        }
        
        $result['head']['schema.org'][] = [
            '@context'      => 'http://schema.org',
            '@type'         => $category->meta->schema,
            'name'          => $category->meta->title,
            'itemListOrder' => 'http://schema.org/ItemListOrderDescending',
            'description'   => $category->meta->description,
            'url'           => $category->page,
            'itemListElement' => $title_items,
            'numberOfItems' => $total
        ];

        return $result;
    }
}