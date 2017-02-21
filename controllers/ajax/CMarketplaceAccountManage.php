<?php

namespace bamboo\blueseal\controllers\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CMarketplaceAccountManage
 * @package bamboo\blueseal\controllers\ajax
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
class CMarketplaceAccountManage extends AAjaxController
{
	public function get()
	{
	    $marketplaceAcountId = $this->app->router->request()->getRequestData('id');
        $marketplaceAcount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAcountId);
        $marketplaceAcount->marketplace;
        return json_encode($marketplaceAcount);
	}

	public function put()
    {
        $shopData = $this->app->router->request()->getRequestData('shop');
        $shopsId = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
    }
}