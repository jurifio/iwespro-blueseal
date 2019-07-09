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
class CPrestashopUpdateProductImage extends ACronJob
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
        $this->updatePrestashopImage();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function updatePrestashopImage()
    {
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');

        $prestashopProduct = new CPrestashopProduct();
        /** @var CObjectCollection $phpC */
        $phpC = $phpRepo->findAll();

        /** @var CPrestashopHasProduct $php */
        foreach ($phpC as $php){

            $ppsC  = $php->product->productPublicSku;
            $price = $php->prestashopHasProductHasMarketplaceHasShop;

            $product =$php->product;
            $marketPlaceHasShop=$php->marketplaceHasShop;
            $productId=$php->productId;
            $productVariantId=$php->productVariantId;





            $sizes = [];

            /** @var CProductPublicSku $pps */
            foreach ($ppsC as $pps){
                $sizes[$pps->productSizeId] = $pps->stockQty;
            }


            $shops = $php->getShopsForProduct();



                $prestashopProduct->updateProductImage($php->prestaId,$productId,$productVariantId, $shops);



        }

        $this->report('Update Image product Prestashop', 'End Update');
    }
}