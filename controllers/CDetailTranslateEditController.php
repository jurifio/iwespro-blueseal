<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CShop;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;


/**
 * Class CDetailTranslateEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
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
        $detailEdit = $this->app->repoFactory->create('ProductDetail')->findOneBy(['id' => $productDetailId]);
        $productDetailEdit = $this->app->repoFactory->create('ProductDetailTranslation')->findBy(['productDetailId' => $productDetailId]);

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll("limit 99999", "");

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'detailEdit' => $detailEdit,
            'productDetailEdit' => $productDetailEdit,
            'langs' => $langs,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}