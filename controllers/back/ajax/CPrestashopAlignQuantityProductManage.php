<?php

namespace bamboo\controllers\back\ajax;


use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;

use PDO;
use PDOException;

/**
 * Class CPrestashopAlignQuantityProductManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/09/2019
 * @since 1.0
 */
class CPrestashopAlignQuantityProductManage extends AAjaxController
{
    public function get()
    {
    }

    public function put()
    {
    }

    /**
     * @return string
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $shop_id = $data['marketplaceHasShopId'];
        $prestashopHasProductRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $prestashopHasProductHasMarketplaceHasShopRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productPublicSkuRepo = \Monkey::app()->repoFactory->create('ProductPublicSku');
        // connessione db remoto
        if (ENV === 'prod') {
            $db_host = '84.247.137.139';
            $db_name = 'cartechini_scodem';
            $db_user = 'cartechininew';
            $db_pass = 'Scoponi2024!';
        } else {
           $db_host = '84.247.137.139';
            $db_name = 'cartechini_scodem';
            $db_user = 'cartechininew';
            $db_pass = 'Scoponi2024!';
        }
        $res = "";
        try {
            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch (PDOException $e) {
            $res .= $e->getMessage();
        }
        /*trovo il prestaId*/
        $prestaId = $prestashopHasProductRepo->findAll();
        foreach ($prestaId as $prestaIds) {
            $idsPrestashop = $prestaIds->prestaId;
            if ($idsPrestashop == null) {
                continue;
            } else {
                $productId = $prestaIds->productId;
                $productVariantId = $prestaIds->productVariantId;
                /* trovo tutti i prodotti pubblicati */
                $productPresta = $prestashopHasProductHasMarketplaceHasShopRepo->findBy(['productId' => $productId, 'productVariantId' => $productVariantId, 'marketplaceHasShopId' => $shop_id]);
                if ($productPresta != null) {
                    foreach ($productPresta as $p) {
                        /* ottengo la quantita totale dei prodotti;*/
                        $qtp = $productRepo->findOneBy(['id' => $productId, 'productVariantId' => $productVariantId]);
                        $totalQuantity = $qtp->qty;
                        /* aggiorno la quantità  totale del prodotto in base allo shop*/
                        try {
                            $stmtUpdateProductQuantityTotal = $db_con->prepare('UPDATE ps_stock_available set quantity=' . $totalQuantity . ', physical_quantity=' . $totalQuantity . ' 
                                                                                   WHERE id_product=' . $idsPrestashop . ' and 
                                                                                   id_product_attribute=0 
                                                                                   and id_shop=' . $shop_id);
                            $stmtUpdateProductQuantityTotal->execute();
                            $res.='<br>aggiornate le quantità per il prodotto con prestaShopId:'.$idsPrestashop;
                        }catch(\Throwable $e){
                            $res.=$e;
                            \Monkey::app()->applicationLog('PrestashopAlignQuantityProductManage', 'Error', 'Error while update Total Quantity product '.$idsPrestashop, $e);
                        }
                        $sku = $productPublicSkuRepo->findBy(['productId' => $productId, 'productVariantId' =>$productVariantId]);
                        if ($sku != null) {

                            foreach ($sku as $skus) {
                                $reference = $skus->productId . '-' . $skus->productVariantId . '-' . $skus->productSizeId;
                                $qty = $skus->stockQty;

                                $stmtFindProductAttribute = $db_con->prepare('SELECT pas.id_product_attribute, pa.reference 
                                                                                    FROM ps_product_attribute pa  JOIN ps_product_attribute_shop pas ON pas.id_product=pa.id_product
                                                                                    AND pas.id_product_attribute=pa.id_product_attribute WHERE pas.id_shop=' . $shop_id . '  and pa.reference=\'' . $reference . '\'');
                                $stmtFindProductAttribute->execute();
                                if($stmtFindProductAttribute!=null){
                                    \Monkey::app()->applicationLog('PrestashopAlignQuantityProductManage', 'Error', 'selection '.$idsPrestashop.' reference'.$reference,'');
                                }
                                while ($stmtRowProductAttribute = $stmtFindProductAttribute->fetch(PDO::FETCH_ASSOC)) {
                                    $id_productAttribute = $stmtRowProductAttribute['id_product_attribute'];
                                }
                                try {
                                    $stmtUpdateProductQuantity = $db_con->prepare('UPDATE ps_stock_available set quantity=' . $qty . ', physical_quantity=' . $qty . ' 
                                                                                   WHERE id_product=' . $idsPrestashop . ' and 
                                                                                   id_product_attribute=' . $id_productAttribute . ' and  
                                                                                   id_shop=' . $shop_id);
                                    $stmtUpdateProductQuantity->execute();
                                    $res.='<br>aggiornate le quantità per il prodotto con prestaShopId:'.$idsPrestashop.'e id_Attributo:'.$id_productAttribute;
                                }catch (\Throwable $e){
                                    \Monkey::app()->applicationLog('PrestashopAlignQuantityProductManage', 'Error', 'Error while update  Quantity product '.$idsPrestashop.' variant'.$id_productAttribute, $e);
                                }

                            }

                        }
                    }
                }
            }

        }


        return $res;
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
        $mhs = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $this->data['marketplaceHasShopId']]);
        $prestashopProduct = new CPrestashopProduct();

        /** @var CRepo $phphmhsR */
        $phphmhsR = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        foreach ($this->data['products'] as $product) {
            if ($prestashopProduct->removeSpecificPriceForSale($product, $mhs)) {
                /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
                $phphmhs = $phphmhsR->findOneBy(['productId' => $product['productId'], 'productVariantId' => $product['productVariantId'], 'marketplaceHasShopId' => $mhs->id]);
                $phphmhs->salePrice = null;
                $phphmhs->isOnSale = 0;
                $phphmhs->update();
            };
        }

        return 'Saldi tolti correttamente';
    }

}