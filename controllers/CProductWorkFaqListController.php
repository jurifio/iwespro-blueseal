<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CFaqArgument;
use bamboo\domain\entities\CFaqType;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductWorkFaqListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/11/2018
 * @since 1.0
 */
class CProductWorkFaqListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_work_faq_list";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/product_work_faq_list.php');

        /** @var CObjectCollection $faqs */
        $faqs = \Monkey::app()->repoFactory->create('Faq')->findBy(['faqTypeId'=> CFaqType::FASON]);

        /** @var CObjectCollection $faqArguments */
        $faqArguments = \Monkey::app()->repoFactory->create('FaqArgument')->findBy(['faqTypeId' => 1]);

        $allShops = \Monkey::app()->getUser()->hasPermission('allShops');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'faqs' => $faqs,
            'faqArguments' => $faqArguments,
            'allShops' => $allShops
        ]);
    }
}