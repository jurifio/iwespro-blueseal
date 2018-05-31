<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CUser;
use bamboo\ecommerce\views\VBase;

/**
 * Class CMessageListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/05/2018
 * @since 1.0
 */
class CMessageListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "message_list";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {

        $isAdmin = \Monkey::app()->getUser()->hasPermission('allShops');

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/message_list.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build(),
            'perm' => $isAdmin
        ]);
    }
}