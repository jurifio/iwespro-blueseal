<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\exceptions\BambooException;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CReleaseProduct extends CLogging
{
    public function run($eventName)
    {
        $shp = $this->getParam('ShopHasProductE');
        if (!$shp)  {
            $shpR = \Monkey::app()->repoFactory->create('ShopHasProduct');
            $sku = $this->getParam('ProductSku');
            $shp = $shpR->findOneBy(['productId' => $sku->productId, 'productVariantId' => $sku->productVariantId, 'shopId']);
        }

        if (!$shp) {
            $product = $this->getParam('ProductSku');
            $shp = $product->shopHasProduct;
        }

        $releaseDate = $this->getParam('releaseDate');
        if (!$releaseDate) $releaseDate = date('Y-m-d H:i:s');
        $this->insertLogRow($eventName, null, $shp->getEntityName(), $shp->printId(), $releaseDate);
    }
}