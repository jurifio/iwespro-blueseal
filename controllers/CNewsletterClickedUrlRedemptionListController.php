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
class CNewsletterClickedUrlRedemptionListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "clicked_url";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */

    public function get()
    {

        $emailId = \Monkey::app()->router->request()->getRequestData('emailId');
        $emailAddressId = \Monkey::app()->router->request()->getRequestData('emailAddressId');

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/clicked_url.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'emailId'=>$emailId,
            'emailAddressId'=>$emailAddressId,
            'sidebar' => $this->sidebar->build()
        ]);

    }
}