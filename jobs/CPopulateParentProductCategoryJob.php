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
 * Class CPopulateParentProductCategoryJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/11/2021
 * @since 1.0
 */
class CPopulateParentProductCategoryJob extends ACronJob
{

    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this->populate();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function populate()
    {
        $res = "";
        try {
            $phpcRepo=\Monkey::app()->repoFactory->create('ProductHasProductCategory');
            $sql='SELECT php.productId as productId, php.productVariantId as productVariantId,php.productCategoryId as productCategoryId, pc.depth as depth, pc.slug as slug from ProductHasProductCategory php 
join Product p on p.id = php.productId and p.productVariantId = php.productVariantId join ShopHasProduct shp on php.productId=shp.productId and php.productVariantId =shp.productVariantId
join ProductCategory pc on pc.id=php.productCategoryId where pc.depth >1  and p.productStatusId in (6,11)';
            $res=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach($res as $row) {
                $productId=$row['productId'];
                $productVariantId=$row['productVariantId'];
                $productCategoryId=$row['productCategoryId'];
                $depth=$row['depth'];
                $slug=$row['slug'];
                $parentDepth=0;
                $sqlParent='SELECT parent.id as parentCategoryId, parent.depth as parentDepth, node.slug as slugNode  FROM
        ProductCategory node,
        ProductCategory parent
        WHERE (
            node.lft BETWEEN parent.lft AND parent.rght          
        )
        AND node.id='.$productCategoryId.'
        ORDER BY parent.rght - parent.lft';
                $resParent = \Monkey::app()->dbAdapter->query($sqlParent,[])->fetchAll();
                foreach ($resParent as $rowParent){
                    if($rowParent['parentDepth']>0){
                        $parentDepth = $rowParent['parentDepth'];
                        $slugNode =$rowParent['slugNode'];
                        $parentCategoryId=$rowParent['parentCategoryId'];
                        $findPhpc=\Monkey::app()->dbAdapter->selectCount('ProductHasProductCategory',['productCategoryId' =>$parentCategoryId,'productId'=>$productId,'productVariantId'=>$productVariantId]);
                        if($findPhpc > 0) {
                            continue;
                        }else{
                            $phpc = $phpcRepo->getEmptyEntity();
                            $phpc->productCategoryId = $parentCategoryId;
                            $phpc->productId = $productId;
                            $phpc->productVariantId = $productVariantId;
                            $phpc->insert();
                            $this->report('CPopulateParentProductCategoryJob','insert product'.$productId.'-'.$productVariantId.' in  '.$slug.'/'.$slugNode);
                        }
                    }

                }


            }

        } catch (\Throwable $e) {
            $this->report('CPopulateParentProductCategoryJob',$e->getMessage());
        }


    }

}