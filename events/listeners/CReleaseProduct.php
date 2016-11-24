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
        $releaseDate = $this->getParameter('releaseDate');
        $userId = $this->getParameter('userId');
        $user = $this->getParameter('user');
        if ($user) $userId = $user->id;
        if (!$releaseDate) $releaseDate = date('Y-m-d H:i:s');
        $shp = $this->getParameter('ShopHasProductE');

        if ($shp) {
            if (!$shp->releaseDate) {
                $shp->releaseDate = $releaseDate;
                $shp->update();
                $this->insertLogRow($eventName, $userId, null, $shp->getEntityName(), $shp->printId(), $releaseDate);
            }
        } else {
            $product = $this->getParameter('Product');
            if (($product) && ($product->productPhoto->count())) {
                foreach ($product->shopHasProduct as $shp) {
                    $qty = 0;
                    foreach($shp->productSku as $sku) {
                        $qty += $sku->stockQty;
                    }
                    if (($qty) && (!$shp->releaseDate)) {
                        $shp->releaseDate = $releaseDate;
                        $shp->update();
                        $this->insertLogRow($eventName, $userId, null, $shp->getEntityName(), $shp->printId(), $releaseDate);
                    }
                }
            }
        }
    }
}