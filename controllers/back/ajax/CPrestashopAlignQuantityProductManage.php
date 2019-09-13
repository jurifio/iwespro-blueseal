<?php

namespace bamboo\controllers\back\ajax;


use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;

use PDO;

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
            $db_host = '5.189.159.187';
            $db_name = 'iwesPrestaDB';
            $db_user = 'iwesprestashop';
            $db_pass = 'X+]l&LEa]zSI';
        } else {
            $db_host = 'localhost';
            $db_name = 'iwesPrestaDB';
            $db_user = 'root';
            $db_pass = 'geh44fed';
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
                        $qtp = $productRepo->finOneBy(['id' => $p->productId, 'productVariantId' => $p->productVariantId]);
                        $totalQuantity = $qtp->stockQty;
                        /* aggiorno la quantità  totale del prodotto in base allo shop*/
                        try {
                            $stmtUpdateProductQuantityTotal = $db_con->prepare('UPDATE ps_stock_available set quantity=' . $totalQuantity . ', physical_quantity=' . $totalQuantity . ' 
                                                                                   WHERE id_product=' . $idsPrestashop . ' and 
                                                                                   id_product_attribute=0 
                                                                                   and id_shop=' . $shop_id);
                            $stmtUpdateProductQuantityTotal->execute();
                            $res.='<br>aggiornate le quantità per il prodotto con prestaShopId'.$idsPrestashop;
                        }catch(\Throwable $e){
                            $res.=$e;
                            \Monkey::app()->applicationLog('PrestashopAlignQuantityProductManage', 'Error', 'Error while update Total Quantity product '.$prestaIds, $e->getMessage());
                        }
                        $sku = $productPublicSkuRepo->findBy(['productId' => $p->productId, 'productVariantId' => $p->productVariantId]);
                        if ($sku != null) {
                            foreach ($sku as $skus) {
                                $reference = $skus->productId . '-' . $skus->productVariantId . '-' . $skus->productSizeId;
                                $qty = $skus->stockQty;
                                $stmtFindProductAttribute = $db_con->prepare('SELECT pas.id_product_attribute, pa.reference 
                                                                                    FROM ps_product_attribute pa  JOIN ps_product_attribute_shop pas ON pas.id_product=pa.id_product
                                                                                    AND pas.id_product_attribute=pa.id_product_attribute WHERE pas.id_shop=' . $shop_id . '  and pa.reference=\'' . $reference . '\'');
                                $stmtFindProductAttribute->execute();
                                while ($stmtRowProductAttribute = $stmtFindProductAttribute->fetch(PDO::FETCH_ASSOC)) {
                                    $id_productAttribute = $stmtRowProductAttribute['id_product_attribute'];
                                }
                                try {
                                    $stmtUpdateProductQuantity = $db_con->prepare('UPDATE ps_stock_available set quantity=' . $qty . ', physical_quantity=' . $qty . ' 
                                                                                   WHERE id_product=' . $idsPrestashop . ' and 
                                                                                   id_product_attribute=' . $id_productAttribute . ' and  
                                                                                   and id_shop=' . $shop_id);
                                    $stmtUpdateProductQuantity->execute();
                                }catch (\Throwable $e){
                                    \Monkey::app()->applicationLog('PrestashopAlignQuantityProductManage', 'Error', 'Error while update  Quantity product '.$prestaIds.' variant'.$id_productAttribute, $e->getMessage());
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