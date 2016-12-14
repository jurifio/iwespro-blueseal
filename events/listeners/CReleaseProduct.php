<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CReleaseProduct extends CLogging
{
    public function work($eventName)
    {
        if(!$eventName instanceof CEventEmitted) throw new BambooException('Event is not an event');
        $this->backtrace = $eventName->getBacktrace();
        $this->params = $eventName->getEventData();
        $userId = $eventName->getUserId();

        $release = $this->getParameter('release');
        if ($release) {
            $releaseDate = $this->getParameter('releaseDate');
            if ($this->user) $userId = $this->user->id;
            elseif ($user = $this->getParameter('user')) $userId = $user->id;

            if (!$releaseDate) $releaseDate = date('Y-m-d H:i:s');
            $shp = $this->getParameter('ShopHasProductE');
            if ($shp) {
                if (!$shp->releaseDate) {
                    $shp->releaseDate = $releaseDate;
                    $shp->update();
                    $this->insertLogRow($eventName->getEventName(), $userId, $release, $shp->getEntityName(), $shp->printId(), $releaseDate);
                }
            } else {
                $product = $this->getParameter('Product');
                if (($product) && ($product->productPhoto->count())) {
                    if ('release' === $release) {
                        foreach ($product->shopHasProduct as $shp) {
                            $qty = 0;
                            foreach ($shp->productSku as $sku) {
                                $qty += $sku->stockQty;
                            }
                            if (($qty) && (!$shp->releaseDate)) {
                                $shp->releaseDate = $releaseDate;
                                $shp->update();
                            }
                            $this->insertLogRow($eventName->getEventName(), $userId, $release, $shp->getEntityName(), $shp->printId(), $releaseDate);
                        }
                    }
                }
            }
        } else {
            //TODO: log errors
        }
    }
}