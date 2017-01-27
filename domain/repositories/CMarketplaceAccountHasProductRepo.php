<?php

namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\entities\IEntity;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CCampaignVisit;

/**
 * Class CCampaignRepo
 * @package bamboo\domain\repositories
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
class CMarketplaceAccountHasProductRepo extends ARepo
{

    /**
     * @param $stringId
     * @return bool
     */
    public function deleteProductFromMarketplace($stringId) {
        try {
            $marketplaceHasProduct = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->findOneByStringId($stringId);
            if(null == $marketplaceHasProduct) {
                $marketplaceHasProduct = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->getEmptyEntity();
                $marketplaceHasProduct->readId($stringId);
                $marketplaceHasProduct->isDeleted = 1;
                $marketplaceHasProduct->isToWork = 1;
                $marketplaceHasProduct->insert();
            } else {
                $marketplaceHasProduct->isDeleted = 1;
                $marketplaceHasProduct->isToWork = 1;
                $marketplaceHasProduct->update();
            }
            $this->app->eventManager->triggerEvent('product.marketplace.change',['productIds'=>$marketplaceHasProduct->product->printId()]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}