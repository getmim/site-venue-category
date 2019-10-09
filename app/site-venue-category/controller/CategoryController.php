<?php
/**
 * CategoryController
 * @package site-venue-category
 * @version 0.0.1
 */

namespace SiteVenueCategory\Controller;

use LibFormatter\Library\Formatter;
use Venue\Model\Venue as Venue;
use VenueCategory\Model\VenueCategory as VCategory;
use VenueCategory\Model\VenueCategoryChain as VCChain;
use SiteVenueCategory\Meta\VenueCategory as Meta;
use LibPagination\Library\Paginator;

class CategoryController extends \Site\Controller
{

    public function singleAction() {
        $slug = $this->req->param->slug;

        // category
        $category = VCategory::getOne(['slug'=>$slug]);
        if(!$category)
            return $this->show404();

        // venues
        list($page, $rpp) = $this->req->getPager();
        $cond = [
            'venue_category' => $category->id
        ];

        $venues = VCChain::get($cond, $rpp, $page, ['id' => 'DESC']);
        if($venues){
            $venues = array_column($venues, 'venue');
            $venues = Venue::get(['id'=>$venues]);

            $formats = ['user','category'];
            if(module_exists('venue-facility'))
                $formats[] = 'facility';
            if(module_exists('venue-food'))
                $formats[] = 'food';

            $venues = !$venues ? [] : Formatter::formatMany('venue', $venues, $formats);
            foreach($venues as &$pg)
                unset($pg->content, $pg->meta);
            unset($pg);
        }

        // pagination
        $pager = null;
        $total = VCChain::count($cond);
        if($total > $rpp){
            $home = $this->router->to('siteHome');
            $pager = new Paginator($home, $total, $page, $rpp);
        }

        // result
        $category = Formatter::format('venue-category', $category);

        $params = [
            'category'  => $category,
            'meta'      => Meta::single($category, $venues, $total, $page, $rpp),
            'venues'    => $venues,
            'pager'     => $pager
        ];
        
        $this->res->render('venue/category/single', $params);
        $this->res->setCache(86400);
        $this->res->send();
    }
}