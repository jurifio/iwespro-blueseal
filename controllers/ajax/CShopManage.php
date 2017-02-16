<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CUserList
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/07/2016
 * @since 1.0
 */
class CShopManage extends AAjaxController
{
	public function get()
	{
	    $shopId = $this->app->router->request()->getRequestData('id');
	    $shops = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
	    if(in_array($shopId,$shops)) {
	        $shop = $this->app->repoFactory->create('Shop')->findOneByStringId($shopId);
	        $shop->user;
	        $shop->billingAddressBook;
	        $shop->shippingAddressBook;
            return json_encode($shop);
        } else {
            $this->app->router->response()->raiseUnauthorized();
            return 'Bad Request';
        }

	}
}