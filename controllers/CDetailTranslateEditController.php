<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CShop;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;


/**
 * Class CDetailTranslateEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CDetailTranslateEditController extends CDetailTranslateManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "detail_translate_edit";

    /**
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/detail_translate_edit.php');

        $productDetailId = $this->app->router->request()->getRequestData();

        $detailEdit = \Monkey::app()->repoFactory->create('ProductDetail')->findOne($productDetailId);
        $productDetailEdit = \Monkey::app()->repoFactory->create('ProductDetailTranslation')->findBy(['productDetailId' => $productDetailId]);

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findBy(["isActive"=>1]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'detailEdit' => $detailEdit,
            'productDetailEdit' => $productDetailEdit,
            'langs' => $langs,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}