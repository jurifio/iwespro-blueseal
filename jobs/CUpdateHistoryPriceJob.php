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
use bamboo\domain\entities\DirtyProduct;
use bamboo\domain\entities\DirtySku;
use bamboo\domain\entities\ProductHistoryPrice;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;
use PDO;
use PDOException;
use Throwable;


class CUpdateHistoryPriceJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->report('CUpdateHistoryPriceJob','log','start Job check Price');
        $this->updateHistoryPrice();
    }


    private function updateHistoryPrice()
    {
        $res = "";
        try {
            /* @var $phpRepo CRepo */
            $today = (new \DateTime())->format('Y-m-d H:i:s');
            $phpRepo = \Monkey::app()->repoFactory->create('ProductHistoryPrice');
            $sql = 'SELECT ps.productId AS productId ,ps.productVariantId AS productVariantId,ps.productSizeId AS productSizeId,ps.shopId AS shopId, MAX(ph.datePrice) AS maxDate,
ph.price AS oldPrice,ph.salePrice AS oldSalePrice,ds.price AS newPrice,ds.salePrice AS newSalePrice 
FROM ProductSku ps 

JOIN DirtyProduct dp ON ps.productId=dp.productId AND ps.productVariantId=dp.productVariantId AND ps.shopId=dp.shopId
JOIN  DirtySku ds ON dp.id=ds.dirtyProductId AND ps.productSizeId=ds.productSizeId
LEFT JOIN ProductHistoryPrice ph ON ps.productId=ph.productId AND ps.productVariantId=ph.productVariantId AND ds.productSizeId=ph.productSizeId AND ps.shopId=ph.shopId 
GROUP BY dp.productId,dp.productVariantId,ds.productSizeId,dp.shopId 
 ';
            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($res as $result) {
                $php = $phpRepo->findOneBy(['productId' => $result['productId'],'productVariantId' => $result['productVariantId'],'productSizeId' => $result['productSizeId'],'shopId' => $result['shopId']]);
                if ($php) {
                    if ($php->price != $result['newPrice'] || $php->salePrice != $result['newSalePrice']) {
                        $phpInsert = \Monkey::app()->repoFactory->create('ProductHistoryPrice')->getEmptyEntiy();
                        $phpInsert->productId = $result['productId'];
                        $phpInsert->productVariantId = $result['productVariantId'];
                        $phpInsert->productSizeId = $result['productSizeId'];
                        $phpInsert->shopId = $result['shopId'];
                        $phpInsert->price = $result['newPrice'];
                        $phpInsert->salePrice = $result['newSalePrice'];
                        $phpInsert->datePrice = $today;
                        $phpInsert->insert();

                    }else{
                        continue;
                    }
                } else {
                    $phpInsert = \Monkey::app()->repoFactory->create('ProductHistoryPrice')->getEmptyEntiy();
                    $phpInsert->productId = $result['productId'];
                    $phpInsert->productVariantId = $result['productVariantId'];
                    $phpInsert->productSizeId = $result['productSizeId'];
                    $phpInsert->shopId = $result['shopId'];
                    $phpInsert->price = $result['newPrice'];
                    $phpInsert->salePrice = $result['newSalePrice'];
                    $phpInsert->datePrice = $today;
                    $phpInsert->insert();
                }
            }


        } catch (\Throwable $e) {
            $this->report('CUpdateHistoryPriceJob','Error' ,$e->getMessage() . '-' . $e->getLine());
        }


    }
}