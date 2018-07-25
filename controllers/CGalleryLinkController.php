<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
 * Class CAdministrationLinkController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/07/2018
 * @since 1.0
 */
class CGalleryLinkController extends ARestrictedAccessRootController
{
protected $fallBack = "blueseal";
protected $pageSlug = "gallery_link";

/**
* @return string
* @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
*/

    public function get()
    {
     $view = new VBase(array());
     $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/gallery_link.php');

            return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
            ]);

    }
}