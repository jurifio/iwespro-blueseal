<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\repositories\CProductPublicSkuRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
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
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put() {

        $data = \Monkey::app()->router->request()->getRequestData();
        $productId = $data['productId'];
        $productVariantId = $data['productVariantId'];
        $newPrice = $data['newPrice'];
        $newSalePrice = $data['newSalePrice'];

        if(empty($newSalePrice) && empty($newPrice)){

            $res = "Non hai scritto nessun nuovo prezzo!";
            return $res;

        } else {

            /** @var CProductSkuRepo $productSkuRepo */
            $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');

            /** @var CProductSku $productSku */
            $productSku = $productSkuRepo->findBy(['productId' => $productId, 'productVariantId' => $productVariantId]);

            /** @var CProductSku $singleSku */
            foreach ($productSku as $singleSku){
                if(!empty($newPrice)) {
                    $singleSku->price = $newPrice;
                }
                if(!empty($newSalePrice)) {
                    $singleSku->salePrice = $newSalePrice;
                }
                $singleSku->update();
            }

            /** @var CProductPublicSkuRepo $publicSkuRepo */
            $publicSkuRepo = \Monkey::app()->repoFactory->create('ProductPublicSku');

            /** @var CProductPublicSku $publicSku */
            $publicSku = $publicSkuRepo->findBy(['productId' => $productId, 'productVariantId' => $productVariantId]);

            /** @var CProductPublicSku $singlePublicSku */
            foreach ($publicSku as $singlePublicSku) {
                if(!empty($newPrice)) {
                    $singlePublicSku->price = $newPrice;
                }
                if(!empty($newSalePrice)) {
                    $singlePublicSku->salePrice = $newSalePrice;
                }
                $singlePublicSku->update();
            }


            $res = "Prezzi aggiornati!!";
            return $res;
        }

    }

}