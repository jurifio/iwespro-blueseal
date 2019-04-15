<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CFixedPage;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;


/**
 * Class CManageFixedPageDetailListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/02/2019
 * @since 1.0
 */
class CManageFixedPageDetailListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "fixed_page_detail";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/fixed_page_detail.php');

        $id = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');
        $langId = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('langId');
        $fixedPageTypeId = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('fixedPageTypeId');

        $langs = \Monkey::app()->repoFactory->create('Lang')->findAll();

        /** @var CFixedPage $fixedPage */
        $fixedPage = \Monkey::app()->repoFactory->create('FixedPage')->findOneBy(['id'=>$id, 'langId'=>$langId, 'fixedPageTypeId'=>$fixedPageTypeId]);
        $fixedPageTypes = \Monkey::app()->repoFactory->create('FixedPageType')->findAll();

        $fixedPageHasPopup = false;
        if((!is_null($fixedPage) && $fixedPage->havePopup())){
            $fixedPageHasPopup = true;
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'fixedPage' => $fixedPage,
            'fixedPageTypes' => $fixedPageTypes,
            'langs' => $langs,
            'fixedPageHasPopup' => $fixedPageHasPopup
        ]);
    }
}