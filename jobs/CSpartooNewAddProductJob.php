<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use PDO;

/**
 * Class CEbayNewAddProductJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 27/04/2020
 * @since 1.0
 */
class CEbayNewAddProductJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->AddProductsInEbay();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function AddProductsInEbay()
    {
        $xml = '';

        $shopActive = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => '1']);

        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $addressBookRepo = \Monkey::app()->repoFactory->create('AddressBook');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productSizeGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeGroup');
        $productBrandRepo = \Monkey::app()->repoFactory->create('ProductBrand');
        $productSizeGroupHasProductSizeRepo = \Monkey::app()->repoFactory->create('ProductSizeGroupHasProductSize');
        $productSizeRepo = \Monkey::app()->repoFactory->create('ProductSize');
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        $productEanRepo = \Monkey::app()->repoFactory->create('ProductEan');
        $productInMarketplaceRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        $marketplaceAccounts = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findBy(['marketplaceId' => 17,'isActive' => 1]);
        foreach ($marketplaceAccounts as $marketplaceAccount) {

            $shop = $shopRepo->findOneBy(['id' => $marketplaceAccount->config['shopId']]);
            $addressBook = $addressBookRepo->findOneBy(['id' => $shop->billingAddressBookId]);
            $goods = $productInMarketplaceRepo->findBy(['isPublished' => 2,'marketplaceHasShopId' => $marketplaceAccount->config['marketplaceHasShopId']]);
            $xml = '';
            $xml .= '<?xml version="1.0" encoding="utf-8"?>';
            $xml .= '<root>';
            $xml .= '<products>';
            foreach ($goods as $good) {
                /**  @var CProduct $product * */
                $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $good->productId,'productVariantId' => $good->productVariantId]);
                if ($product->qty == 0) {
                    continue;
                }
                $productCategory=\Monkey::app()->repoFactory->create('ProductHasProductCategory')->findOneBy(['productId'=>$good->productId,'productVariantId' => $good->productVariantId]);
                $productCategoryId=$productCategory->productCategoryId;
                $category = \Monkey::app()->repoFactory->create('ProductCategoryHasMarketplaceAccountCategory')->findOneBy(['marketplaceId' => 3,'marketplaceAccountId' => 3,'productCategoryId' => $productCategoryId]);
                if (!$category) {
                    continue;
                }
                $productBrand = $productBrandRepo->findOneBy(['id' => $product->productBrandId]);
                $slugBrand = $productBrand->slug;
                $brandName = $productBrand->name;
                $productEanParent = $productEanRepo->findOneBy(['productId' => $good->productId,'productVariantId' => $good->productVariantId,'productSizeId' => 0]);
                if ($productEanParent) {
                    $productEan = $productEanParent->ean;
                } else {
                    $productEanSelect =$productEanRepo->findOneBy(['used'=>0]);
                    $productEan=$productEanSelect->ean;
                    $productEanSelect->productId=$good->productId;
                    $productEanSelect->productVariantId=$good->productVariantId;
                    $productEanSelect->productSizeId=0;
                    $productEanSelect->usedForParent=1;
                    $productEanSelect->brandAssociate=$product->productBrandId;
                    $productEanSelect->shopId=$marketplaceAccount->config['shopId'];
                    $productEanSelect->update();
                }

                $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $product->productSizeGroupId]);
                $productSizeGroupHasProductSize = $productSizeGroupHasProductSizeRepo->findBy(['ProductSizeGroupId' => $productSizeGroup->id]);
                $phphmhsRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
                //etichetta taglie
                $isOnSale = $good->isOnSale;


                $xml .= '<product>';
                $xml .= '<reference_partenaire>'.$product->id.'-'.$product->productVariantId.'</reference_partenaire>';
                if ($good->titleModified == "1" && $good->isOnSale == "1") {
                    $percSc = number_format(100 * ($good->price - $good->salePrice) / $good->price,0);
                    $name = $product->productBrand->name
                        . ' Sconto del ' . $percSc . '% da ' . number_format($good->price,'2','.','') . ' € a ' . number_format($good->salePrice,'2','.','')
                        . ' € ' .
                        $product->itemno
                        . ' ' .
                        $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;
                    $xml .= '<Title><![CDATA[' . $name . ']]></Title>';
                } else {
                    $name = $product->productCategoryTranslation->findOneByKey('langId',1)->name
                        . ' ' .
                        $product->productBrand->name
                        . ' ' .
                        $product->itemno
                        . ' ' .
                        $product->productColorGroup->productColorGroupTranslation->findOneByKey('langId',1)->name;


                    $xml .= '<product_name><![CDATA[' . $name . ']]></product_name>';
                }
                $xml .= '<manufacturers_name><![CDATA['. $product->productBrand->name.']]></manufacturers_name>';
                $shopHasProduct=\Monkey::app()->repoFactory->create('ShopHasProduct')->findOneBy(['productId'=>$product->id,'productVariantId'=>$product->productVariantId]);

                if($product->isOnSale==0){
                    $xml.='<product_price>'.$shopHasProduct->price.'</product_price>';
                }else{
                    $xml.='<product_price>'.$shopHasProduct->salePrice.'</product_price>';
                }
                $xml .= '<product_quantity>'.$product->qty.'</product_quantity>';
                $pcghmacg=\Monkey::app()->repoFactory->create('ProductColorGroupHasMarketplaceAccountColorGroup')->findOneBy(['marketplaceId'=>$marketplaceAccount->marketplaceId,'marketplaceAccountId'=>$marketplaceAccount->id,'productColorGroupId'=>$product->productColorGroupId]);
                if($pcghmacg){
                    $xml .= '<color_id>'.$pcghmacg->marketplaceColorGroupId.'</color_id>';
                }
                $productHasProductCategory=\Monkey::app()->repoFactory->create('ProductHasProductCategory')->findOneBy(['productId'=>$product->id,'productVariantId'=>$product->productVariantId]);
                $productCategoryId=$productHasProductCategory->productCategoryId;
                $pchmac=\Monkey::app()-> repoFactory -> create('ProductCategoryHasMarketplaceAccountCategory')->findOneBy(['productCategoryId'=>$productCategoryId,'marketplaceId'=>$marketplaceAccount->marketplaceId,'marketplaceAccountId'=>$marketplaceAccount->id]);
                $pchmac->marketplaceAccountCategoryId;
                $xml .= '<product_style>'.$pchmac->marketplaceAccountCategoryId.'</product_style>';
                $xml .= '<product_description><![CDATA['.$product->getDescription().']]></product_description>';
                $xml .= '<country_origin>105</country_origin>';
                $xml .= '<productColor><![CDATA['.$product->productColorGroup->name.']]></productColor>';
                $category=$product->getLocalizedProductCategories('<br>', '/');
                switch(true){
                    case (strpos($category,'Donna')):
                        $categoryId='F';
                        break;
                    case (strpos($category,'Uomo')):
                        $categoryId='H';
                        break;
                    case (strpos($category,'Bambino')):
                        $categoryId='K';
                        break;
                    case (strpos($category,'Bambina')):
                        $categoryId='G';
                        break;
                    default:
                        $categoryId='M';

                }
                $xml .= '<product_sex>'.$categoryId.'</product_sex>';
                //variante colore
                $productVariant = \Monkey::app()->repoFactory->create('ProductVariant')->findOneBy(['id' => $good->productVariantId]);
                //varianti taglie n
                $xml.='<size_list>';
                /** @var CProductSku $productSku */
                $productSku = $productSkuRepo->findBy(['productId' => $good->productId,'productVariantId' => $good->productVariantId]);
                foreach ($productSku as $sku) {
                    if ($sku->stockQty > 0) {
                        $xml .= '<size>';
                        // $xml .= '<SKU>prestashop-' . $reservedId['prestaId'] . '-' . $rowsGetReferenceIdProductAttribute[0]['id_product_attribute'] . '</SKU>';
                        $pshmas=\Monkey::app()->repoFactory->create('ProductSizeHasMarketplaceAccountSize')->findOneBy(['marketplaceId'=>$marketplaceAccount->marketplaceId,'marketplaceAccountId'=>$marketplaceAccount->id,'productSizeId'=>$sku->productSizeId]);
                        $marketplaceAccountSize=\Monkey::app()->repoFactory->create('MarketplaceAccountSize')->findOneBy(['marketplaceId'=>$marketplaceAccount->marketplaceId,'marketplaceAccountId'=>$marketplaceAccount->id,'marketplaceSizeId'=>$pshmas->marketplaceSizeId]);
                        $xml .= '<size_name><![CDATA[' . $marketplaceAccountSize->name . ']]></size_name>';
                        $xml .= '<size_reference><![CDATA[' . $sku->productId . '-' . $sku->productVariantId . '-' . $sku->productSizeId . ']]></size_reference>';
                        $xml .= '<size_quantity>' . $sku->stockQty . '</size_quantity>';
                        $xml .= '<VariationProductListingDetails>';
                       $findSkuEan=$productEanRepo->findOneBy(['productId' => $sku->productId,'productVariantId' => $sku->productVariantId,'productSizeId' => $sku->productSizeId]);
                        if ($findSkuEan) {
                            $productSkuEan = $findSkuEan->ean;
                        } else {
                            $productSkuEanSelect =$productEanRepo->findOneBy(['used'=>0]);
                            $productSkuEan=$productSkuEanSelect->ean;
                            $productSkuEanSelect->productId=$sku->productId;
                            $productSkuEanSelect->productVariantId=$sku->productVariantId;
                            $productSkuEanSelect->productSizeId=$sku->productSizeId;
                            $productSkuEanSelect->usedForParent=0;
                            $productSkuEanSelect->used=1;
                            $productSkuEanSelect->brandAssociate=$product->productBrandId;
                            $productSkuEanSelect->shopId=$marketplaceAccount->config['shopId'];
                            $productSkuEanSelect->update();
                        }

                            $xml .= '<ean>' . $productSkuEan . '</ean>';

                        $xml .= '</size>';
                    }
                }
                $xml .= '</sizeList>';
                $xml .= '<Photos>';
                $productHasProductPhoto = \Monkey::app()->repoFactory->create('ProductHasProductPhoto')->findBy(['productId' => $sku->productId,'productVariantId' => $sku->productVariantId]);
               $z=1;
                foreach ($productHasProductPhoto as $phs) {
                    $productPhoto = \Monkey::app()->repoFactory->create('ProductPhoto')->findOneBy(['id' => $phs->productPhotoId]);
                    if ($productPhoto->size == '1124') {
                        $xml .= '<url'.$z.'>https://cdn.iwes.it/' . $slugBrand . '/' . $productPhoto->name . '</url'.$z.'>';
                    }

                }
                $xml .= '</Photos>';
                $xml .= '<selections>';
                $xml .= '<selection>519</selection>';

                $xml .='</selections>';
                $xml .='</product>';



            }
            $xml.='</products>';
            $xml.='</root>';
            try{
                $urlInsert = "https://sws.spartoo.it/mp/xml_import_products.php";
                $options = array(
                    "http" => array(
                        "header" => "Content-type: text/xml\r\n",
                        "method" => "POST",
                        "xml" => $xml,
                        "partneaire"=>"EF40F969744AF620"
                    ),
                );
                $context = stream_context_create($options);
                $result = json_decode(file_get_contents($urlInsert, false, $context), true);

            }catch(\Throwable $e){

            }


        }

    }
}