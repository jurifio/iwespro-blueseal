<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CMarketplaceHasShopBrandRights;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CNewsletterUser;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProductBrand;


/**
 * Class CMarketPlaceHasShopListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/09/2018
 * @since 1.0
 */



class CPrestashopBookingProductListAjaxController extends AAjaxController
{

    public function post()
    {

        try{
            $res='Elenco Prodotti Pubblicati<br>';
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
                                $res.='inserimento Prodotto '.$product->id.'-'.$product->productVariantId. 'su marketplace '.$mp->name.'<br>';

                            }
                        }
                    }
                }

            }
            return $res;
        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('CPrestashopBookingProductJob', 'Error','Insert all Product to marketplace',$e->getMessage(),$e->getCode());
        }
    }
}