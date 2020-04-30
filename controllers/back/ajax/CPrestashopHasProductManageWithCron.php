<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\repositories\CPrestashopHasProductRepo;


/**
 * Class CPrestashopHasProductManageWithCron
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/03/2019
 * @since 1.0
 */
class CPrestashopHasProductManageWithCron extends AAjaxController
{
    public function get()
    {
    }

    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        if(empty($this->data['marketplaceHasShopId']) || (empty($this->data['variantValue']) && $this->data['modifyType'] !== 'nf')){
            return 'Inserisci i dati correttamente';
        }
        $res='';
        /** @var CPrestashopHasProductRepo $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $shopHasProductRepo=\Monkey::app()->repoFactory->create('ShopHasProduct');
        $phsdRepo=\Monkey::app()->repoFactory->create('ProductHasShopDestination');
        $marketplaceHasShopRepo=\Monkey::app()->repoFactory->create('MarketplaceHasShop');

        foreach ($this->data['products'] as $productCode){

            $productIds = explode('-',$productCode);
            $shopId=$marketplaceHasShopRepo->findOneBy(['prestashopId'=>$this->data['marketplaceHasShopId']])->shopId;
            $isProductShop=$shopHasProductRepo->findOneBy(['productId'=>$productIds[0], 'productVariantId'=>$productIds[1],'shopId'=>$shopId]);
            $isProductHasShopDestination=$phsdRepo->findOneBy(['productId'=>$productIds[0], 'productVariantId'=>$productIds[1],'shopIdDestination'=>$shopId]);
            if($isProductShop!=null) {
                /** @var CPrestashopHasProduct $php */
                $php = $phpRepo->findOneBy(['productId' => $productIds[0],'productVariantId' => $productIds[1]]);

                /** @var @ marketplaceHasShopId */
                $php->marketplaceHasShopId = $this->data['marketplaceHasShopId'];
                $php->modifyType = $this->data['modifyType'];
                $php->variantValue = $this->data['modifyType'] === 'nf' ? 0 : $this->data['variantValue'];
                $php->update();
                $res.='Prodotto '.$productIds[0].'-'.$productIds[1].' inserito con successo</br>';
            }elseif($isProductHasShopDestination!=null) {
                /** @var CPrestashopHasProduct $php */
                $php = $phpRepo->findOneBy(['productId' => $productIds[0],'productVariantId' => $productIds[1]]);

                /** @var @ marketplaceHasShopId */
                $php->marketplaceHasShopId = $this->data['marketplaceHasShopId'];
                $php->modifyType = $this->data['modifyType'];
                $php->variantValue = $this->data['modifyType'] === 'nf' ? 0 : $this->data['variantValue'];
                $php->update();
                $res.='Prodotto' .$productIds[0].'-'.$productIds[1].' inserito con successo</br>';
            }else{
                $res.='Prodotto' .$productIds[0].'-'.$productIds[1].' inserito Non inserito pechè non presente neanche nello shop di Destinazione</br>';

            }
        }

        return $res;
    }


    public function put()
    {
    }

    public function delete()
    {
    }

}