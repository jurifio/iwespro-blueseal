<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;

/**
 * Class CMarketplacesDelete
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
class CMarketplaceProductDelete extends AEventListener
{
    public function work($e)
    {
        if(!$e instanceof CEventEmitted) throw new BambooException('Event is not an event');
        $this->report('MarketplacesDelete','Deleting Product',$e->getEventData());

        $product = $this->app->repoFactory->create('Product')->findOneByStringId($e->getEventData('productId'));
        foreach ($product->marketplaceAccountHasProduct as $marketplaceAccountHasProduct) {
            $marketplaceAccountHasProduct->isDeleted = 1;
            $marketplaceAccountHasProduct->update();
        }
    }
}