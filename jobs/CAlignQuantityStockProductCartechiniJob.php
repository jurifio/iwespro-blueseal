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
 * Class CAlignQuantityStockProductCartechiniJob
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
 */




class CAlignQuantityStockProductCartechiniJob extends ACronJob
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
        $res="";

            $this->report('Start Align Quantity  From Iwes  ', 'Shop To Update Cartechini');
        $db_host = "5.189.152.89";
        $db_name = "cartechininew";
        $db_user = "root";
        $db_pass = "F1fiI3EYv9JXl8Z";
        $res = "";
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch (PDOException $e) {
            $res .= $e->getMessage();
        }
        //connessione a
            $productPublicSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
            $stmtProductPublicSku = $db_con->prepare("select * from ProductSku");
            $stmtProductPublicSku->execute();
            $collectRealQty=[];
            while ($rowProductPublicSku = $stmtProductPublicSku->fetch(PDO::FETCH_ASSOC)) {
                $destStockQty = $rowProductPublicSku['stockQty'];
                $productId=$rowProductPublicSku['productId'];
                $productVariantId=$rowProductPublicSku['productVariantId'];
                $productSizeId=$rowProductPublicSku['productSizeId'];
                $shopDest=$rowProductPublicSku['shopId'];
                $pps=$productPublicSkuRepo->findOneBy(['productId'=>$productId,'productVariantId'=>$productVariantId,'productSizeId'=>$productSizeId,'shopId'=>$shopDest]);
                if($pps!=null) {
                    $origStockQty = $pps->stockQty;
                    $origShop=$pps->shopId;
                    $stockQty = $origStockQty - $destStockQty;
                    if ($stockQty != 0) {
                        array_push($collectRealQty, ['productId' => $productId, 'productVariantId' => $productVariantId, 'productSizeId' => $productSizeId, 'stockQty' => $origStockQty,'shopId'=>$origShop]);
                    }
                }else{
                    continue;
                }

            }

            foreach ($collectRealQty as $row){
                $stmtUpdateProductPublicSku=$db_con->prepare('UPDATE ProductSku 
                                                                      SET stockQty='.$row['stockQty'].'
                                                                      WHERE productId='.$row['productId'].'
                                                                      AND productVariantId='.$row['productVariantId'].'
                                                                      AND productSizeId='.$row['productSizeId'].'
                                                                      AND shopId='.$row['shopId']);
                $stmtUpdateProductPublicSku->execute();
                $this->report("Updating StockQty single Job Cartechini", "Skus updated: " . $row['productId'] .'-'.$row['productVariantId'].'-'.$row['productSizeId']. ' with quantity: ' . $row['stockQty']. 'for shop:Cartechini');
            }

        }

}