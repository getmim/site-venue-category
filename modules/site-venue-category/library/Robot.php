<?php
/**
 * Robot
 * @package site-venue-category
 * @version 0.0.1
 */

namespace SiteVenueCategory\Library;

use VenueCategory\Model\VenueCategory as VCategory;

class Robot
{
    static private function getPages(): ?array{
        $cond = [
            'updated' => ['__op', '>', date('Y-m-d H:i:s', strtotime('-2 days'))]
        ];
        $pages = VCategory::get($cond);
        if(!$pages)
            return null;

        return $pages;
    }

    static function feed(): array {
        $mim = &\Mim::$app;

        $pages = self::getPages();
        if(!$pages)
            return [];

        $result = [];
        foreach($pages as $page){
            $route = $mim->router->to('siteVenueCategorySingle', (array)$page);
            $meta  = json_decode($page->meta);
            $title = $meta->title ?? $page->name;
            $desc  = $meta->description ?? substr($page->content, 0, 100);

            $result[] = (object)[
                'description'   => $desc,
                'page'          => $route,
                'published'     => $page->created,
                'updated'       => $page->updated,
                'title'         => $title,
                'guid'          => $route
            ];
        }

        return $result;
    }

    static function sitemap(): array {
        $mim = &\Mim::$app;

        $pages = self::getPages();
        if(!$pages)
            return [];

        $result = [];
        foreach($pages as $page){
            $route  = $mim->router->to('siteVenueCategorySingle', (array)$page);
            $result[] = (object)[
                'page'          => $route,
                'updated'       => $page->updated,
                'priority'      => '0.4',
                'changefreq'    => 'daily'
            ];
        }

        return $result;
    }
}