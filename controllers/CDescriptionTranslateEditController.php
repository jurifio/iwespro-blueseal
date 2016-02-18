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
        $view->setTemplatePath($this->app->cfg()->fetch('paths', 'blueseal') . '/template/description_translate_edit.php');

        $descriptionEm = $this->app->entityManagerFactory->create('ProductDescriptionTranslation', false);
        $descrEdit = $descriptionEm->findBySql("select productId, productVariantId, marketplaceId, langId from ProductDescriptionTranslation WHERE langId=1 AND description <> ''
                                                      AND description <> '<br>' AND description <> '<br><br>' ORDER BY description",array())->getFirst();
        $descriptionEdit = $descriptionEm->findBySql("select * from ProductDescriptionTranslation WHERE productId = ? AND productVariantId = ? ", [$descrEdit->productId,$descrEdit->productVariantId]);

        $productName = [];
        foreach ($descriptionEdit as $des) {
            $productsName = $this->app->repoFactory->create('ProductNameTranslation')->findOneBy(['productId' => $descrEdit->productId, 'productVariantId' => $descrEdit->productVariantId, 'langId' => $des->langId ]);
            $productName[$des->langId] = $productsName->name;
        }
        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll("limit 99999", "");

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'descriptionEdit' => $descriptionEdit,
            'productName' => $productName,
            'langs' => $langs,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);

    }

}