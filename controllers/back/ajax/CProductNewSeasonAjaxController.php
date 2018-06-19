<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductHasTag;
use bamboo\domain\entities\CTag;


/**
 * Class CProductNewSeasonAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/06/2018
 * @since 1.0
 */
class CProductNewSeasonAjaxController extends AAjaxController
{


    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post()
    {
        $seasonId = \Monkey::app()->router->request()->getRequestData('season');

        /** @var CObjectCollection $products */
        $products = \Monkey::app()->repoFactory->create('Product')->findBy(['productSeasonId'=>$seasonId]);

        /** @var CRepo $phsRepo */
        $phsRepo = \Monkey::app()->repoFactory->create('ProductHasTag');

        if($products) {
            /** @var CProduct $product */
            foreach ($products as $product) {

                /** @var CProductHasTag $extPhs */
                $extPhs = $phsRepo->findOneBy(['productId' => $product->id, 'productVariantId' => $product->productVariantId, 'tagId' => CTag::NEW_SEASON]);

                if (!is_null($extPhs)) continue;

                \Monkey::app()->dbAdapter->insert('ProductHasTag', ['productId' => $product->id, 'productVariantId' => $product->productVariantId, 'tagId' => CTag::NEW_SEASON], false, true);
            }

            $res = 'Etichetta stagione inserita con successo';
        } else $res = 'Nessun prodotto associato alla stagione selezionata';

        return $res;
    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function delete(){

        $seasonId = \Monkey::app()->router->request()->getRequestData('season');

        /** @var CObjectCollection $products */
        $products = \Monkey::app()->repoFactory->create('Product')->findBy(['productSeasonId'=>$seasonId]);

        /** @var CRepo $phsRepo */
        $phsRepo = \Monkey::app()->repoFactory->create('ProductHasTag');

        if($products) {
            /** @var CProduct $product */
            foreach ($products as $product) {

                /** @var CProductHasTag $extPhs */
                $extPhs = $phsRepo->findOneBy(['productId' => $product->id, 'productVariantId' => $product->productVariantId, 'tagId' => CTag::NEW_SEASON]);

                if (is_null($extPhs)) continue;

                \Monkey::app()->dbAdapter->delete('ProductHasTag', ['productId' => $product->id, 'productVariantId' => $product->productVariantId, 'tagId' => CTag::NEW_SEASON]);
            }

            $res = 'Etichetta stagione eliminata con successo';
        } else $res = 'Nessun prodotto associato alla stagione selezionata';

        return $res;

    }

}