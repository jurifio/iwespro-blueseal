<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductHasTag;

/**
 * Class CSpecialNewBrandTagsAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/06/2018
 * @since 1.0
 */
class CSpecialNewBrandTagsAjaxController extends AAjaxController
{

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post(){
        $brand = \Monkey::app()->router->request()->getRequestData('brand');
        $tag = \Monkey::app()->router->request()->getRequestData('tag');

        /** @var CObjectCollection $products */
        $products = \Monkey::app()->repoFactory->create('Product')->findBy(['productBrandId'=>$brand]);

        /** @var CRepo $phtRepo */
        $phtRepo = \Monkey::app()->repoFactory->create('ProductHasTag');

        if($products) {
        foreach ($products as $product) {
            /** @var CProductHasTag $extPht */
            $extPht = $phtRepo->findOneBy(['productId'=>$product->id, 'productVariantId'=>$product->productVariantId, 'tagId'=>$tag]);

            if(!is_null($extPht)) continue;

            \Monkey::app()->dbAdapter->insert('ProductHasTag',['productId'=>$product->id,'productVariantId'=>$product->productVariantId,'tagId'=>$tag],false,true);
        }
            $res = 'Special tag inserito con successo';
        } else $res = 'Nessun prodotto associato al brand selezionato';

        return $res;
    }

    public function delete(){

        $brand = \Monkey::app()->router->request()->getRequestData('brand');
        $tag = \Monkey::app()->router->request()->getRequestData('tag');

        /** @var CObjectCollection $products */
        $products = \Monkey::app()->repoFactory->create('Product')->findBy(['productBrandId'=>$brand]);

        /** @var CRepo $phtRepo */
        $phtRepo = \Monkey::app()->repoFactory->create('ProductHasTag');

        if($products) {
            foreach ($products as $product) {
                /** @var CProductHasTag $extPht */
                $extPht = $phtRepo->findOneBy(['productId'=>$product->id, 'productVariantId'=>$product->productVariantId, 'tagId'=>$tag]);

                if(is_null($extPht)) continue;

                \Monkey::app()->dbAdapter->delete('ProductHasTag',['productId'=>$product->id,'productVariantId'=>$product->productVariantId,'tagId'=>$tag]);
            }
            $res = 'Special tag eliminato con successo';
        } else $res = 'Nessun prodotto associato al brand selezionato';

        return $res;


    }

}