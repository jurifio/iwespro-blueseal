<?php
namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;

/**
 * Class CPrestashopUpdateProductQuantity
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/04/2019
 * @since 1.0
 */
class CPrestashopUpdateProductQuantity extends ACronJob
{

    /**
     * @param null $args
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this->updatePrestashopQty();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function updatePrestashopQty()
    {
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');

        $prestashopProduct = new CPrestashopProduct();
        /** @var CObjectCollection $phpC */
        $phpC = $phpRepo->findBy(['status' => 2]);

        /** @var CPrestashopHasProduct $php */
        foreach ($phpC as $php){

            $ppsC  = $php->product->productPublicSku;
            $price = $php->prestashopHasProductHasMarketplaceHasShop;

            $product =$php->product;
            $marketPlaceHasShop=$php->marketplaceHasShop;



            /** @var CPrestashopHasProductHasMarketplaceHasShop $prices */
            foreach($price as $prices){
                if($prices->isOnSale===1){
                    $productPrice=$prices->salePrice;
                }else{
                    $productPrice=$prices->price;
                }
               // $shop=$prices->shopId;

            }

            $sizes = [];

            /** @var CProductPublicSku $pps */
            foreach ($ppsC as $pps){
                $sizes[$pps->productSizeId] = $pps->stockQty;
            }


            $shops = $php->getShopsForProduct();


            foreach ($sizes as $size => $qty){
                $prestashopProduct->updateProductQuantity($php->prestaId, $size,  $qty,  null,  $shops);
            }



            $php->status = 1;
            $php->update();
        }

        $this->report('Update product qty and price', 'End Update');
    }
}