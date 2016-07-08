<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CShop;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;


/**
 * Class CNameTranslateEditController
 * @package redpanda\blueseal\controllers
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
class CNameTranslateEditController extends CNameTranslateManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "name_translate_edit";

    /**
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/name_translate_edit.php');

        $name = $this->app->router->request()->getRequestData('name');

        $langs = $this->app->repoFactory->create('Lang')->findAll();
        $productsEdit = [];
             $product= $this->app->repoFactory->create('ProductNameTranslation')->findOneBy(['name' => $name, 'langId' => 1]);
        foreach($langs as $lang) {
            $productsEdit[$lang->id] = $this->app->repoFactory->create('ProductNameTranslation')->findOneBy(
                [
                    'productId' => $product->productId,
                    'productVariantId' => $product->productVariantId,
                    'langId' => $lang->id
                ]
            );
        }

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll("limit 99999", "");

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'name' => $name,
            'productEdit' => $productsEdit,
            'langs' => $langs,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}