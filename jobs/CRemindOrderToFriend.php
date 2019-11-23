<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CShop;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\export\order\COrderExport;

/**
 * Class CRemindOrderToFriend
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CRemindOrderToFriend extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $shops = \Monkey::app()->repoFactory->create('Shop')->findAll();
        $query = "SELECT * from OrderLine where `status` in ('ORD_FRND_SENT') AND shopId = ? ";

        /** @var COrderLineRepo $orderLineRepo */
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');

        foreach($shops as $shop){
            try {
                $lines = $orderLineRepo->em()->findBySql($query, [$shop->id]);
                $this->report('Working Shop ' . $shop->name . ' Start', 'Found ' . count($lines) . ' to send');

                if (isset($shop->referrerEmails) && count($lines) >0 ) {
                    $to = explode(';',$shop->referrerEmails);
                    $orderGetLines=$this->buildDatas($shop, $lines);
                    /*$this->app->mailer->prepare('friendorderreminder','no-reply', $to,[],[],
                        ['orderLines'=>$lines]);
                    $this->app->mailer->send();*/

                    /** @var CEmailRepo $emailRepo */
                    $emailRepo = \Monkey::app()->repoFactory->create('Email');
                    $emailRepo->newPackagedMail('friendorderreminder','no-reply@iwes.pro', $to,[],[],
                        ['lines'=>$orderGetLines]);

                    $this->report('Working Shop ' . $shop->name . ' End', 'Reminder Sent ended');
                }

            } catch(\Throwable $e){
                $this->error( 'Working Shop ' . $shop->name . ' End', 'ERROR Sending Lines',$e);
            }
        }
    }
    /**
     * @param CShop $shop
     * @param $orderLines
     * @return array
     */
    public function buildDatas(CShop $shop, $orderLines)
    {
        $lines = [];
        foreach ($orderLines as $line) {
            $lines[] = $this->buildData($shop, $line);
        }

        return $lines;
    }

    /**
     * @param CShop $shop
     * @param COrderLine $line
     * @return array
     */
    private function buildData(CShop $shop, COrderLine $line)
    {
        $row = [];
        $billing = new $shop->billingLogic($this->app);
        $row['orderId'] = $line->orderId;
        $row['orderLineId'] = $line->id;
        $row['productId'] = $line->productId;
        $row['productVariantId'] = $line->productVariantId;
        $row['productSizeId'] = $line->productSizeId;
        $row['shopId'] = $line->shopId;
        $row['remoteShopSellerId']=$line->remoteShopSellerId;
        $row['remoteOrderSellerId']=$line->remoteOrderSellerId;
        $row['remoteOrderLineSellerId']=$line->remoteOrderLineSellerId;
        try {
            $dirtySku = $line->productSku->findRightDirtySku();
            /*
             $findIds = "SELECT itemno, extId, ds.extSkuId AS extSkuId, var, size
                        FROM DirtyProduct dp, DirtySku ds
                        WHERE ds.dirtyProductId = dp.id AND
                                dp.productId = ? AND
                                dp.productVariantId = ? AND
                                dp.shopId = ? AND
                                ds.productSizeId = ?";
            $ids = $this->app->dbAdapter->query($findIds, [$line->productId, $line->productVariantId, $line->shopId, $line->productSizeId])->fetchAll()[0];
            */
            $row['extId'] = !empty($dirtySku->extSkuId) ? $dirtySku->extSkuId : $dirtySku->dirtyProduct->extId;
            $row['var'] = $dirtySku->dirtyProduct->var;
            $row['size'] = $dirtySku->size;
            $row['itemno'] = $dirtySku->dirtyProduct->itemno;
        } catch (\Throwable $e) {
            $row['extId'] = $line->productSku->shopHasProduct->extId;
            $row['itemno'] = $line->productSku->product->itemno;
            $row['var'] = $line->productSku->product->productVariant->name;
            $row['size'] = $line->productSku->productSize->name;
        }

        $product = $line->productSku->product;

        /**  find brand name*/
        //$findIds = "SELECT pb.name AS brand, slug  FROM ProductBrand pb, Product p WHERE p.productBrandId = pb.id AND p.id = ? AND p.productVariantId = ?";
        //$ids = $this->app->dbAdapter->query($findIds, [$line->productId, $line->productVariantId])->fetchAll()[0];
        $row['brand'] = $product->productBrand->name;
        $row['brandSlug'] = $product->productBrand->slug;
        $row['productNameTranslation'] = $product->getName();

        $row['friendRevenue'] = isset($line->friendRevenue) && !is_null($line->friendRevenue) && $line->friendRevenue <> 0 ? $line->friendRevenue : $billing->calculateFriendReturn($line);
        /** find photo */
        //$findIds = "SELECT pp.name AS photo FROM ProductHasProductPhoto ps, ProductPhoto pp WHERE ps.productPhotoId = pp.id AND ps.productId = ? AND ps.productVariantId = ? AND pp.size = ? ";
        //$ids = $this->app->dbAdapter->query($findIds, [$line->productId, $line->productVariantId, 281])->fetchAll()[0];
        $row['photo'] = $product->getPhoto(1,281);

        return $row;
    }

}