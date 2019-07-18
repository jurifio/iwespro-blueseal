<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;


class CAssignEanToMarketPlaceProductAssociateJob extends ACronJob
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
        $this->assignEanToProduct();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function assignEanToProduct()
    {
        /* definizione delle repo */
        /** @var CProductBrand $productBrandRepo */
        $productBrandRepo = \Monkey::app()->repoFactory->create('ProductBrand');
        /** @var CProduct $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        /** @var CProductSku $productSkuRepo */
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        /** @var CProductEan $productEanRepo */
        $productEanRepo = \Monkey::app()->repoFactory->create('ProductEan');
        /** @var CPrestashopHasProduct $prestashopHasProductRepo */
        $prestashopHasProductRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
        $phphmhs = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        /*prima estrabolo dalla tabella di ProductBrand i brand con i diritti per la pubblicazione su marketplace*/
        /** @var CProductBrand $productBrand */
        /* ciclo il  brand con il filtro delle colonne hasMarketplaceRights  */
        $productBrand = $productBrandRepo->findBy(['hasMarketplaceRights' => 1]);
        /* verifico se l'array non è nullo */
        if ($productBrand != null) {
            $hasExternalEan = $productBrand->hasExternalEan;
            /** @var CProduct $product */

            $product = $productRepo->findBy(['productBrandId' => $productBrand->id, 'productStatusId' => 6]);
            if ($product != null) {
                foreach ($product as $products) {
                    $productId = $products->id;
                    $productVariantId = $product->productVariantId;
                    /* ciclo i proodotti da product per prestashophasproduct verificando se gia sono inseriti o meno*/
                    $prestashopHasProduct = $prestashopHasProductRepo->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId]);
                    /* se non ci sono li inserisco e gli metto lo stato a 0 altrimenti salta il ciclo*/
                    if ($prestashopHasProduct != null) {
                        continue;
                    } else {
                        /** @var CPrestashopHasProduct $prestashopHasProductInsert */
                        $prestashopHasProductInsert = $prestashopHasProductRepo->getEmptyEntity();
                        $prestashopHasProductInsert->productId = $productId;
                        $prestashopHasProductInsert->productVariantId = $productVariantId;
                        $prestashopHasProductInsert->status = 0;
                        $prestashopHasProductInsert->insert();
                        /*se il brand ha l'utilizzo degli ean esterni passo all'inserimento dell'ean*/
                        if ($hasExternalEan === 1) {
                            /*nell inserimento del prodotto padre verifico  se esiste l'ean  nella tabella productEan con il filtro sul prodotto*/
                            /** @var CProductEan $productEanParentFind */
                            $productEanParentFind = $productEanRepo->findOneBy(['productId' => $productId, 'productVariantId' => $productVariantId, 'productSizeId' => 0]);
                            if ($productEanParentFind == null) {
                                /*   se non esiste lo inserisco */
                                /** @var CProductEan $productEanParentInsert */
                                $productEanParentInsert = $productEanRepo->findOneBy(['used' => 0]);
                                $productEanParentInsert->productId = $productId;
                                $productEanParentInsert->productVariantId = $productVariantId;
                                $productEanParentInsert->productSizeId = 0;
                                $productEanParentInsert->usedForParent = 1;
                                $productEanParentInsert->used = 1;
                                $productEanParentInsert->brandAssociate = $productBrand->id;
                                $productEanParentInsert->shopId = 1;
                                $productEanParentInsert->update();
                            }
                            /* colleziono ogni taglia prodotto su productSku*/
                            /** @var CProductSku $productSku */
                            $productSku=$productSkuRepo->findBy(['productId'=>$productId,'productVariantId'=>$productVariantId]);
                            if($productSku!=null){
                                /* ciclo le taglie*/
                                foreach($productSku as $productSkus){
                                    $productSizeId=$productSkus->productSizeId;
                                    /*vedo se esiste il prodotto per le taglie*/
                                    $productEanFind=$productEanRepo->findOneBy(['productId'=>$productId,'productVariantId'=>$productVariantId,'productSizeId'=>$productSizeId]);
                                    if($productEanFind !=null){
                                        continue;
                                    }else{
                                        /** @var CProductEan $productEanInsert */
                                        $productEanInsert=$productEanRepo->findOneBy(['used'=>0]);
                                            if($productEanInsert!=null){
                                                $productEanInsert->productId = $productId;
                                                $productEanInsert->productVariantId = $productVariantId;
                                                $productEanInsert->productSizeId = $productSizeId;
                                                $productEanInsert->usedForParent = 0;
                                                $productEanInsert->used = 1;
                                                $productEanInsert->brandAssociate = $productBrand->id;
                                                $productEanInsert->shopId = 1;
                                                $productEanInsert->update();
                                            }


                                    }
                                }
                            }

                        }
                    }
                }
            }

        }

        $this->report('assign product  orphan ean for marketplace ', 'End Update');
    }
}