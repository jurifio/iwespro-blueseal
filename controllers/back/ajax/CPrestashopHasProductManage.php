<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CPrestashopHasProductRepo;
use bamboo\domain\repositories\CProductRepo;

/**
 * Class CPrestashopHasProductManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/03/2019
 * @since 1.0
 */
class CPrestashopHasProductManage extends AAjaxController
{
    public function get()
    {

        $mhsColl = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findAll();

        $res = [];
        $i = 0;
        /** @var CMarketplaceHasShop $mhs */
        foreach ($mhsColl as $mhs) {
            $res[$i]['id'] = $mhs->id;
            $res[$i]['shop-marketplace'] = $mhs->shop->name . ' | ' . $mhs->marketplace->name;
            $i++;
        }

        return json_encode($res);

    }

    public function post()
    {
        if(empty($this->data['marketplaceHasShopId']) || (empty($this->data['variantValue']) && $this->data['modifyType'] !== 'nf')){
            return 'Inserisci i dati correttamente';
        }
        $res='';
        $mhs = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $this->data['marketplaceHasShopId']]);
        $shopHasProductRepo=\Monkey::app()->repoFactory->create('ShopHasProduct');
        $phsdRepo=\Monkey::app()->repoFactory->create('ProductHasShopDestination');
        $marketplaceHasShopRepo=\Monkey::app()->repoFactory->create('MarketplaceHasShop');

        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');

        $products = new CObjectCollection();
        foreach ($this->data['products'] as $productCode) {
            $productIds = explode('-',$productCode);
            $shopId=$marketplaceHasShopRepo->findOneBy(['id'=>$this->data['marketplaceHasShopId'],'isActive'=>1])->shopId;
            $isProductShop=$shopHasProductRepo->findOneBy(['productId'=>$productIds[0], 'productVariantId'=>$productIds[1],'shopId'=>$shopId]);
            $isProductHasShopDestination=$phsdRepo->findOneBy(['productId'=>$productIds[0], 'productVariantId'=>$productIds[1],'shopIdDestination'=>$shopId]);
            if($isProductShop!=null) {
                /** @var CProduct $product */
                $product = $productRepo->findOneBy(['id'=>$productIds[0], 'productVariantId'=>$productIds[1]]);
                $products->add($product);
                $res.='Prodotto '.$productIds[0].'-'.$productIds[1].' inserito con successo</br>';
            } elseif($isProductHasShopDestination!=null) {
                /** @var CProduct $product */
                $product = $productRepo->findOneBy(['id'=>$productIds[0], 'productVariantId'=>$productIds[1]]);
                $products->add($product);
                $res.='Prodotto '.$productIds[0].'-'.$productIds[1].' inserito con successo</br>';
            }else{
                $res.='Prodotto' .$productIds[0].'-'.$productIds[1].' inserito Non inserito pechè non presente neanche nello shop di Destinazione</br>';
            }
        }

        $prestashopProduct = new CPrestashopProduct();

        if ($prestashopProduct->addNewProducts($products, $mhs, $this->data['modifyType'], $this->data['variantValue'])) {
            return $res;
        }

        return 'Errore durante l\'inserimento dei prodotti';
    }

    public function put()
    {
    }

    /**
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete()
    {
        $prestashopProduct = new CPrestashopProduct();

        /** @var CPrestashopHasProductRepo $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        foreach ($this->data['products'] as $product){
            if($prestashopProduct->deleteProduct($product['prestaId'], $product['productId'], $product['productVariantId'])){

                /** @var CPrestashopHasProduct $php */
                $php = $phpRepo->findOneBy(['productId'=>$product['productId'],
                                      'productVariantId'=>$product['productVariantId']
                                     ]);

                /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
                foreach ($php->prestashopHasProductHasMarketplaceHasShop as $phphmhs){
                    $phphmhs->delete();
                }

                $php->delete();
            };
        }

        return 'Prodotti elminati correttamente';
    }

}