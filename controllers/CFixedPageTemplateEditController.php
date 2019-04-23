<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CFixedPageTemplate;
use bamboo\ecommerce\views\VBase;

/**
 * Class CFixedPageTemplateEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/04/2019
 * @since 1.0
 */
class CFixedPageTemplateEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "fixed_page_template_edit";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/fixed_page_template_edit.php');

        $fixedPageTemplateId = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');

        /** @var CFixedPageTemplate $fixedPageTemplate */
        $fixedPageTemplate = \Monkey::app()->repoFactory->create('FixedPageTemplate')->findOneBy(['id'=>$fixedPageTemplateId]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build(),
            'fixedPageTemplate' => $fixedPageTemplate
        ]);

    }
}