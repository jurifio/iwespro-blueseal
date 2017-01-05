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
class CUserList extends AAjaxController
{
	public function get()
	{
		$list = [];
		foreach ($this->app->repoFactory->create('User')->findBy(['isDeleted'=>0],"","ORDER BY id desc") as $user) {
			$list[] = ['id'=>$user->id,
			           'email'=>$user->email,
			           'name'=> is_null($user->userDetails) ? "" : $user->userDetails->name,
			           'surname'=> is_null($user->userDetails) ? "" : $user->userDetails->surname];
		}
		return json_encode($list);
	}
}