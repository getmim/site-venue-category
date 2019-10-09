<?php
/**
 * RobotController
 * @package site-venue-category
 * @version 0.0.1
 */

namespace SiteVenueCategory\Controller;

use SiteVenueCategory\Library\Robot;
use Venue\Model\Venue;
use VenueCategory\Model\VenueCategory as VCategory;
use VenueCategory\Model\VenueCategoryChain as VCChain;
use LibRobot\Library\Feed;

class RobotController extends \Site\Controller
{
    public function feedAction(){
        $category = VCategory::getOne(['slug'=>$this->req->param->slug]);
        if(!$category)
            return $this->show404();

        $meta  = json_decode($category->meta);
        $title = $meta->title ?? $category->name;
        $desc  = $meta->description ?? substr($category->content, 0, 100);
        $link  = $this->router->to('siteVenueCategoryFeed', (array)$category);

        $feed_opts = (object)[
            'self_url'          => $link,
            'copyright_year'    => date('Y'),
            'copyright_name'    => \Mim::$app->config->name,
            'description'       => $desc,
            'language'          => 'id-ID',
            'host'              => $link,
            'title'             => $title
        ];

        $date_start = date('Y-m-d H:i:s', strtotime('-2 days'));
        $cond = [
            'venue_category' => $category->id,
            'venue.updated'  => ['__op', '>', $date_start]
        ];

        $links = [];

        $vcchain = VCChain::get($cond);
        if($vcchain){
            $venue_ids = array_column($vcchain, 'venue');
            $venues = Venue::get(['id'=>$venue_ids]);
            if($venues){
                foreach($venues as $venue){
                    $route = \Mim::$app->router->to('siteVenueSingle', (array)$venue);
                    $meta  = json_decode($venue->meta);
                    $title = $meta->title ?? $venue->title;
                    $desc  = $meta->description ?? substr($venue->content, 0, 100);

                    $links[] = (object)[
                        'description'   => $desc,
                        'page'          => $route,
                        'published'     => $venue->created,
                        'updated'       => $venue->updated,
                        'title'         => $title,
                        'guid'          => $route
                    ];
                }
            }
        }

        Feed::render($links, $feed_opts);
        $this->res->setCache(3600);
        $this->res->send();
    }
}