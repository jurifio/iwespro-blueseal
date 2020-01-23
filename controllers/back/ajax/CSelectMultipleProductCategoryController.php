<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductSheetModelPrototype;
use bamboo\domain\repositories\COrderRepo;
use bamboo\domain\repositories\CShipmentRepo;

/**
 * Class CGetPermissionsForUser
 * @package bamboo\blueseal\controllers\ajax
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
class CSelectMultipleProductCategoryController extends AAjaxController
{
    public function get()
    {
        $id = $this->app->router->request()->getRequestData('id');
        /** @var CProductCategory $productCategory */
        $productCategory = \Monkey::app()->repoFactory->create('ProductCategory',false)->findOneByStringId($id);
        $productCategory->marketplaceAccountCategory;
        $productCategory->dictionaryCategory;
        $productCategory->productCategoryTranslation;
        $productCategory->product;
        $productCategory->descendantMarketplaceAccountCategory = 0;
        $productCategory->descendantDictionaryCategory = 0;
        $productCategory->descendantProduct = \Monkey::app()->repoFactory->create('Product')->countProductsByCategoryFullTree($productCategory->id);
        foreach ($productCategory->descendantProductCategory as $descendantProductCategory) {
            if(!$descendantProductCategory->marketplaceAccountCategory->isEmpty())
                $productCategory->descendantMarketplaceAccountCategory += $descendantProductCategory->marketplaceAccountCategory->count() ;
            if(!$descendantProductCategory->dictionaryCategory->isEmpty())
                $productCategory->descendantDictionaryCategory += $descendantProductCategory->dictionaryCategory->count();
        }

        $url = \Monkey::app()->baseUrl(false).'/blueseal/prodotti/modelli/modifica?id=';

        $productCategory->psmp = '';
        /** @var CProductSheetModelPrototype $psm */
        foreach ($productCategory->productSheetModelPrototype as $psm){
            if($psm->isVisible == 1) {
                $productCategory->psmp .= "<a href='$url$psm->id' target='_blank'>$psm->id</a>" . ' | ';
            }
        }

        return json_encode($productCategory);
    }
}