<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\repositories\CProductSkuRepo;


/**
 * Class CSalePriceProductSkuModify
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/12/2017
 * @since 1.0
 */
class CSalePriceProductSkuModify extends AAjaxController
{

    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put() {

        $a = null;
        $data = \Monkey::app()->router->request()->getRequestData();
        $multipleRow = $data['multipleRow'];
        if ($multipleRow){
            //Se ho selezionato più righe
            $productCode = $data['productCode'];
            $newPrice = $data['newPrice'];
            $newSalePrice = $data['newSalePrice'];

            foreach ($productCode as $singleProductCode){
                $newProductCode = explode('-', $singleProductCode);
                $pId = $newProductCode[0];
                $pVID = $newProductCode[1];

                /** @var CProductSkuRepo $productSkuRepo */
                $productSkuRepo = \Monkey::app()->repoFactory->create('ProductPublicSku');

                /** @var CObjectCollection $productPublicSkus */
                $productPublicSkus = $productSkuRepo->findBy(['productId' => $pId, 'productVariantId' => $pVID]);

                /** @var CProductPublicSku $singleSku */
                foreach ($productPublicSkus as $singleSku){
                    if(!empty($newPrice)) {
                        $singleSku->price = $newPrice;
                    }
                    if(!empty($newSalePrice)) {
                        $singleSku->salePrice = $newSalePrice;
                    }
                    $singleSku->update();
                }
            }

            $res = "Prezzi aggiornati!!";
            return $res;

        } else {
            //Se ho selezionato una sola riga
            $productId = $data['productId'];
            $productVariantId = $data['productVariantId'];
            $newPrice = $data['newPrice'];
            $newSalePrice = $data['newSalePrice'];

            if(empty($newSalePrice) && empty($newPrice)){

                $res = "Non hai scritto nessun nuovo prezzo!";
                return $res;

            } else {

                /** @var CProductSkuRepo $productSkuRepo */
                $productSkuRepo = \Monkey::app()->repoFactory->create('ProductPublicSku');

                /** @var CObjectCollection $productPublicSkus */
                $productPublicSkus = $productSkuRepo->findBy(['productId' => $productId, 'productVariantId' => $productVariantId]);

                /** @var CProductPublicSku $singleSku */
                foreach ($productPublicSkus as $singleSku){
                    if(!empty($newPrice)) {
                        $singleSku->price = $newPrice;
                    }
                    if(!empty($newSalePrice)) {
                        $singleSku->salePrice = $newSalePrice;
                    }
                    $singleSku->update();
                }

                $res = "Prezzi aggiornati!!";
                return $res;
            }
        }

    }

}