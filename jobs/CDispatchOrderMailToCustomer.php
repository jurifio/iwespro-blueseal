<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\export\order\COrderExport;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\COrderLine;

/**
 * Class CDispatchOrderMailToCustomer
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/10/2019
 * @since 1.0
 */

class CDispatchOrderMailToCustomer extends ACronJob
{

    var $success = "ORD_MAIL_PREP_C";
    var $fail = "ORD_FRND_OK";

    /**
     * @param null $args
     */
    public function run($args = null)
    {

        /** @var COrderLineRepo $orderLineRepo */
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $lines = $orderLineRepo->findBy(['status'=>'ORD_FRND_OK']);
            try {

                \Monkey::app()->repoFactory->beginTransaction();
                foreach ($lines as $line) {
                    try {
                        $row = [];
                        $row['orderId'] = $line->orderId;
                        $row['orderLineId'] = $line->id;
                        $row['productId'] = $line->productId;
                        $row['productVariantId'] = $line->productVariantId;
                        $row['productSizeId'] = $line->productSizeId;
                        $row['shopId'] = $line->shopId;
                        $row['netPrice'] = $line->netPrice;
                        $row['remoteShopSellerId']=$line->remoteShopSellerId;

                        try {
                            $dirtySku = $line->productSku->findRightDirtySku();

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
                        $order=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id'=>$line->orderId]);
                        if($order->isParallel==1) {
                            $to=['friends@iwes.it'];
                        }else {
                            $user = \Monkey::app()->repoFactory->create('User')->findOneBy(['id' => $order->userId]);
                            $to = [$user->email];
                        }

                        /** find photo */
                        //$findIds = "SELECT pp.name AS photo FROM ProductHasProductPhoto ps, ProductPhoto pp WHERE ps.productPhotoId = pp.id AND ps.productId = ? AND ps.productVariantId = ? AND pp.size = ? ";
                        //$ids = $this->app->dbAdapter->query($findIds, [$line->productId, $line->productVariantId, 281])->fetchAll()[0];
                        $row['photo'] = $product->getPhoto(1,281);
                        /** @var CEmailRepo $emailRepo */
                        $emailRepo = \Monkey::app()->repoFactory->create('Email');
                        $emailRepo->newPackagedTemplateMail('ordermailtocustomer', 'no-reply@pickyshop.com', $to, [], [], ['row' => $row]);
                        $orderLine = \Monkey::app()->repoFactory->create("OrderLine")->findOneBy(['id' => $line->id, 'orderId' => $line->orderId]);
                        $line->status=$this->success;
                        $line->update();
                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                }
                \Monkey::app()->repoFactory->commit();
            } catch (\Throwable $e) {

                \Monkey::app()->repoFactory->beginTransaction();
                foreach ($lines as $line) {
                    try {
                        $line->status=$this->success;
                        $line->update();
                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                }
                \Monkey::app()->repoFactory->commit();
            }
        }

}