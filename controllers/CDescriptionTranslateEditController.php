<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\application\AApplication;

/**
 * Class CDescriptionTranslateEditController
 * @package redpanda\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDescriptionTranslateEditController extends CDescriptionTranslateManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "description_translate_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/description_translate_edit.php');

        $productId = $this->app->router->request()->getRequestData('productId');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');

        $descriptionEdit = $this->app->repoFactory->create('ProductDescriptionTranslation')->findBy(['productId'=>$productId,'productVariantId'=>$productVariantId,'marketplaceId'=>1]);

        $productNameTranslation = [];

        foreach ($descriptionEdit as $des) {

            $productsName = $this->app->repoFactory->create('ProductNameTranslation')->findOneBy(['productId' => $des->productId, 'productVariantId' => $des->productVariantId, 'langId' => $des->langId ]);
            if ($productsName) {
                $productNameTranslation[$des->langId] = $productsName->name;
            } else {
                $productNameTranslation[$des->langId] = '';
            }
        }

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll("limit 99999", "");

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'descriptionEdit' => $descriptionEdit,
            'productNameTranslation' => $productNameTranslation,
            'productId'=> $productId,
            'productVariantId'=>$productVariantId,
            'langs' => $langs,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);

    }

}