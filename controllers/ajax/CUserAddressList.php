<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CUserAddressList
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
class CUserAddressList extends AAjaxController
{
	public function get()
	{
		$list = [];
		$user = $this->app->repoFactory->create('User')->findOneBy(['id'=>$this->app->router->request()->getRequestData('userId')]);
		$useraddresses = $user->userAddress;
		foreach ($user->userAddress as $userAddress) {
			$list[] = ['id'=>$userAddress->id,
			           'name'=>$userAddress->name,
			           'surname'=>$userAddress->surname,
			           'city'=>$userAddress->city,
			           'address'=>$userAddress->address];
		}
		return json_encode($list);
	}
}