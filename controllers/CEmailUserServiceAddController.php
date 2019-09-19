<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
 * Class CEmailUserServiceAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/09/2019
 * @since 1.0
 */
class CEmailUserServiceAddController extends ARestrictedAccessRootController
{
protected $fallBack = "blueseal";
protected $pageSlug = "emailuserservice_add";

/**
* @return string
* @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
*/

    public function get()
    {
     $view = new VBase(array());
     $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/emailuserservice_add.php');

            return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
            ]);

    }
}