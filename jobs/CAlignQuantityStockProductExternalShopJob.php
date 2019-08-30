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
use PDO;
use PDOException;

/**
 * Class CAlignQuantityStockProductExternalShopJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/08/2019
 * @since 1.0
 *
 */


class CAlignQuantityStockProductExternalShopJob extends ACronJob
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
        $this->alignStockExternalProduct();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function alignStockExternalProduct()
    {
        $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => 1]);

        foreach ($shopRepo as $value) {
            $this->report('Start Align Quantity  From Iwes  ', 'Shop To Update' . $value->name);
            /********marketplace********/
            $db_host = $value->dbHost;
            $db_name = $value->dbName;
            $db_user = $value->dbUsername;
            $db_pass = $value->dbPassword;
            $shop = $value->id;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $res .= " connessione ok <br>";
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }
            $productPublicSkuRepo = \Monkey::app()->repoFactory->create('ProductPublicSku');
            $stmtProductPublicSku = $db_con->prepare("select * from ProductPublicSku");
            $stmtProductPublicSku->execute();
            $collectRealQty=[];
            while ($rowProductPublicSku = $stmtProductPublicSku->fetch(PDO::FETCH_ASSOC)) {
                $destStockQty = $rowProductPublicSku['stockQty'];
                $productId=$rowProductPublicSku['productId'];
                $productVariantId=$rowProductPublicSku['productVariantId'];
                $productSizeId=$rowProductPublicSku['productSizeId'];
                $pps=$productPublicSkuRepo->findOneBy(['productId'=>$productId,'productVariantId'=>$productVariantId,'productSizeId'=>$productSizeId]);
                $origStockQty = $pps->stockQty;
                $stockQty = $origStockQty - $destStockQty;
                if ($stockQty != 0) {
                    //echo sprintf("Quantità differente del prodotto %s-%s-%s quantità iwes:%s quantita Destinazione:%s<br>", $productId, $productVariantId, $productSizeId, $origStockQty, $destStockQty);
                    array_push($collectRealQty, ['productId' => $productId, 'productVariantId' => $productVariantId, 'productSizeId' => $productSizeId, 'stockQty' => $origStockQty]);
                }

            }

            foreach ($collectRealQty as $row){
               $stmtUpdateProductPublicSku=$db_con->prepare('UPDATE ProductPublicSku 
                                                                      SET stockQty='.$row['stockQty'].'
                                                                      WHERE productId='.$row['productId'].'
                                                                      AND productVariantId='.$row['productVariantId'].'
                                                                      AND productSizeId='.$row['productSizeId']);
                $stmtUpdateProductPublicSku->execute();
                $this->report("Updating StockQty", "Skus updated: " . $row['productId'] .'-'.$row['productVariantId'].'-'.$row['productSizeId']. ' with quantity: ' . $row['stockQty']. 'for shop:'.$value->name);
            }

        }
    }
}