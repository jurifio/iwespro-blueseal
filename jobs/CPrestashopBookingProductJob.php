<?php
namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CMarketplaceHasShopBrandRights;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProductBrand;

/**
 * Class CPrestashopBookingProductJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/05/2020
 * @since 1.0
 */
class CPrestashopBookingProductJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->report('CPrestashopBookingProductJob', 'Start Booking','');

        $this->insertBookingProducts();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function insertBookingProducts()
    {
        try{
        /** @var  CRepo $productRepo  */
        $productRepo=\Monkey::app()->repoFactory->create('Product');
        /** @var  CRepo $productBrandRepo  */
        $productBrandRepo=\Monkey::app()->repoFactory->create('ProductBrand');
        /** @var  CRepo $phpRepo  */
        $mhsRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');
        /** @var  CRepo $marketplaceHasShopBrandRightsRepo  */
        $marketplaceHasShopBrandRightsRepo=\Monkey::app()->repoFactory->create('MarketplaceHasShopBrandRights');

        /**  @var CRepo $phpRepo */
        $phpRepo=\Monkey::app()->repoFactory->create('PrestashopHasProduct');
        /** @var CObjectCollection $php */
        $mhs=$mhsRepo->findAll();
        foreach($mhs as $mp) {
            /** @var CObjectCollection $products */
            $products = $productRepo->findBy(['productStatusId' => 6]);
            foreach ($products as $product) {
                /** @var CProductBrand $productBrand */
                $productBrand = $productBrandRepo->findOneBy(['id' => $product->productBrandId]);
                if ($productBrand->hasMarketplaceRights == 1) {
                    /** @var CMarketplaceHasShopBrandRights $mhsbr */
                    $mhsbr = $marketplaceHasShopBrandRightsRepo->findOneBy(['marketplaceHasShopId' => $mp->id,'productBrandId' => $product->productBrandId]);
                    if ($mhsbr != null) {
                        /**@var CPrestashopHasProduct $phpFind */
                        $phpFind = $phpRepo->findOneBy(['productId' => $product->id,'productVariantId' => $product->productVariantId,'marketplaceHasShopId' => $mp->id]);
                        if ($phpFind == null) {

                            /** @var CPrestashopHasProduct $phpInsert */
                            $phpInsert=$phpRepo->getEmptyEntity();
                            $phpInsert->productId = $product->id;
                            $phpInsert->productVariantId = $product->productVariantId;
                            $phpInsert->marketplaceHasShopId = $mp->id;
                            $phpInsert->modifyType = 'nf';
                            $phpInsert->variantValue = 0.0;
                            $phpInsert->insert();

                        }
                    }
                }
            }

        }

        $this->report('CPrestashopBookingProductJob', 'End Booking','');
    }catch(\Throwable $e){
            $this->report('CPrestashopBookingProductJob','error',$e->getMessage());
        }
    }
}