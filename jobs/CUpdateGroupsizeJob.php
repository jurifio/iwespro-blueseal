<?php

namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CUpdateGroupsizeJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/10/2018
 * @since 1.0
 */
class CUpdateGroupsizeJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        try {
            $productRepo = \Monkey::app()->repoFactory->create('Product');
            $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');

            $sql = "select d.id as dirtyProductId,
       shp.productId as productId,
       shp.productVariantId as productVariantId,
       shp.shopId as shopId,
       dgs.productSizeGroupId as productSizeGroupId
       
         from ShopHasProduct shp 
			join DirtyProduct  d ON shp.shopId=d.shopId AND shp.productId=d.productId AND shp.productVariantId=d.productVariantId 
			 join DirtyProductExtend dpe on d.id = dpe.dirtyProductId 
         join DictionaryGroupSize dgs on dpe.sizeGroup=dgs.term 
         where  d.fullMatch=0  and shp.shopId=dgs.shopId and dpe.sizeGroup is not null group by d.id";
            $res = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

            foreach ($res as $result) {
                $product = $productRepo->findOneBy(['productId' => $result['productId'], 'productVariantId' => $result['productVariantId']]);
                $product->productSizeGroupId = $result['productSizeGroupId'];
                $product->update();
                $shopHasProduct = $shopHasProductRepo->findOneBy(['shopId' => $result['shopId'], 'productId' => $result['productId'], 'productVariantId' => $result['productVariantId']]);
                $shopHasProduct->productSizeGroupId = $result['productSizeGroupId'];
                $shopHasProduct->update();

            }

        }catch (\Throwable $e){
            \Monkey::app()->applicationLog("CUpdateGroupSizeJob","error","verificare",$e->getLine().'-'.$e->getMessage(),"");
        }




        return $res;
    }


}