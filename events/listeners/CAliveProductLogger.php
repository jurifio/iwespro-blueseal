<?php

namespace bamboo\events\listeners;


use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;

/**
 * Class CAliveProductLogger
 * @package bamboo\events\listeners
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
class CAliveProductLogger extends CLogging
{
    public function work($e)
    {
        if (!$e instanceof CEventEmitted) throw new BambooException('Event is not an event');

        if ($e->getEventData('productSkuIds')) {
            $productSku = $this->app->repoFactory->create('ProductSku')->findOneByStringId($e->getEventData('productSkuIds'));
        } else throw new BambooException('Wrong configuration for event');

        $qty = 0;
        if ($productSku->product->productStatus->isVisible) {
            foreach ($productSku->shopHasProduct->productSku as $productSku2) {
                $qty += ($productSku2->stockQty - $productSku2->padding) < 0 ? 0 : ($productSku2->stockQty - $productSku2->padding);
            }
        }

        $this->insertLogRow(
            $e->getEventName(),
            $e->getUserId(),
            $qty,
            'ShopHasProduct',
            $productSku->shopHasProduct->printId()
        );
    }
}