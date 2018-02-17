<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\email\CEmail;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CNewsletterRepo;
use bamboo\ecommerce\views\VBase;

/**
 * Class CNewsletterSingleRedemptionListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/02/2018
 * @since 1.0
 */
class CNewsletterSingleRedemptionListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "single_redemption";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */

    public function get()
    {

        $newsletterId = \Monkey::app()->router->request()->getRequestData('newsletterId');

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/single_redemption.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'newsletterId'=>$newsletterId,
            'sidebar' => $this->sidebar->build()
        ]);

    }
}