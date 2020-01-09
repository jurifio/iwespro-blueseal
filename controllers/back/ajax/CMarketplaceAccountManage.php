<?php

namespace bamboo\controllers\back\ajax;

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
        $marketplaceAcount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAcountId);
        $marketplaceAcount->marketplace;
        return json_encode($marketplaceAcount);
	}

	public function put()
    {
        $shopData = $this->app->router->request()->getRequestData('shop');
        $shopsId = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
    }
    public function post()
    {
            $data  = $this->app->router->request()->getRequestData();
        $marketplaceAccountId=$data['marketplaceAccountId'];
        $marketplaceId=$data['marketplaceId'];
        $nameOld=$data['marketplace_account_name'];
        $collect=$data['collect'];
       $name=$data['nameAggregator'];

        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketPlaceAccount')->findOneBy(['id'=>$marketplaceAccountId,'marketplaceId'=>$marketplaceId]);
        if($name!=null) {
            $marketplaceAccount->name = $name;
        }else{
            $marketplaceAccount->name = $nameOld;
        }
        $marketplaceAccount->config=$collect;
        $marketplaceAccount->update();
        \Monkey::app()->applicationLog('MarketPlaceAccount','Report','Update','Update Marketplace '.$marketplaceAccountId.'-'.$marketplaceId. ' '.$name);
    }
}