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
        $sidebar = $this->app->cacheService->getCache('misc')->get('BluesealSidebar:' . $this->app->getUser()->id);
        if ($sidebar == false) {

            $sections = \Monkey::app()->repoFactory->create('SidebarSection')->em()->findBySql("Select id 
                                                                                  from SidebarSection 
                                                                                    ORDER BY `order` asc");
            $sidebar = [];
            $i=0;
            foreach ($sections as $section) {

                $sidebarGroups = [];
                $sidebar[$i]['title'] = $section->sidebarSectionTranslation->getFirst()->title;
                $sidebar[$i]['icon'] = $section->icon;
                $groups = \Monkey::app()->repoFactory->create('SidebarGroup')->em()->findBySql("Select id
                                                                                    from SidebarGroup where sidebarSectionId=" . $section->id . "
                                                                               ORDER BY `order` asc");

                foreach ($groups as $group) {

                    $sidebarPages = [];
                    foreach ($group->page as $page) {
                        if ($this->app->getUser()->hasPermissions($page->permission)) {
                            $sidebarPages[$page->slug] = [
                                'title' => $page->pageTranslation->getFirst()->title,
                                'url' => $page->url,
                                'icon' => $page->icon,
                                'permission' => $page->permission,
                                'postId' => $page->postId
                            ];
                        }
                        if (count($sidebarPages) > 0) {

                            $sidebarGroups[$group->slug]['title'] = $group->sidebarGroupTranslation->getFirst()->title;
                            $sidebarGroups[$group->slug]['icon'] = $group->icon;
                            $sidebarGroups[$group->slug]['pages'] = $sidebarPages;

                        }


                    }


                }


                $sidebar[$i]['sidebarGroups'] = $sidebarGroups;

        $i++;
            }

            $this->app->cacheService->getCache('misc')->add('BluesealSidebar:' . $this->app->getUser()->id,$sidebar,10000);
        }
        return $sidebar;
    }
}