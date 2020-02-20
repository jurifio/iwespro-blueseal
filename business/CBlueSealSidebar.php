<?php

namespace bamboo\blueseal\business;

use bamboo\core\application\AApplication;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\db\pandaorm\repositories\IRepo;

/**
 * Class CBlueSealSidebar
 * @package bamboo\blueseal\business
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CBlueSealSidebar
{
    protected $sidebar;
    protected $repo;

    /**
     * CBlueSealSidebar constructor.
     * @param AApplication $app
     */
    public function __construct(AApplication $app)
    {
        $this->app = $app;
    }

    public function build()
    {
        $sidebar = $this->app->cacheService->getCache('misc')->get('BluesealSidebar:'.$this->app->getUser()->id);
        if($sidebar == false) {
            $groups = \Monkey::app()->repoFactory->create('SidebarGroup')->em()->findBySql("Select id 
                                                                                    from SidebarGroup 
                                                                                    ORDER BY `order` asc");

            $sidebar = [];
            foreach ($groups as $group) {
                $sidebarPages = [];
                foreach ($group->page as $page) {
                    if($this->app->getUser()->hasPermissions($page->permission)) {
                        $sidebarPages[$page->slug] = [
                            'title' => $page->pageTranslation->getFirst()->title,
                            'url' => $page->url,
                            'icon' => $page->icon,
                            'permission' => $page->permission,
                            'postId' => $page->postId
                        ];
                    }
                }
                if(count($sidebarPages) > 0) {
                    $sidebar[$group->slug]['title'] = $group->sidebarGroupTranslation->getFirst()->title;
                    $sidebar[$group->slug]['icon'] = $group->icon;
                    $sidebar[$group->slug]['pages'] = $sidebarPages;
                }
            }
            $this->app->cacheService->getCache('misc')->add('BluesealSidebar:'.$this->app->getUser()->id,$sidebar,10000);
        }
        return $sidebar;
    }
}