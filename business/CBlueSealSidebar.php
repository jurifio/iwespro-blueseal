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
        $this->sidebar = $this->app->repoFactory->create('Sidebar')->findAll(null," ORDER BY groupOrder,pageOrder ASC ");
    }

    public function build()
    {
        $sidebar = [];

        while ($this->sidebar->valid()) {
            $group = $this->app->repoFactory->create('SidebarGroup')->findOne([$this->sidebar->current()->sidebarGroup->getFirst()->id]);
            $page = $this->app->repoFactory->create('Page')->findOne([$this->sidebar->current()->page->getFirst()->id]);
            $sidebar[$group->slug]['title'] = $group->sidebarGroupTranslation->getFirst()->title;
            $sidebar[$group->slug]['icon'] = $group->icon;
            $sidebar[$group->slug]['pages'][$page->slug] = [
                'title' => $page->pageTranslation->getFirst()->title,
                'url' => $page->url,
                'icon' => $page->icon,
                'permission' => $page->permission
             ];
            $this->sidebar->next();
        }

        return $sidebar;
    }
}