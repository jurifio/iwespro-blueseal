<?php
namespace bamboo\controllers\back\ajax;

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
class CUserData extends AAjaxController
{
	public function get()
	{
        if($this->app->getUser()->hasPermissions('/admin/user/list&&allShops')) {
            if($userId = $this->app->router->request()->getRequestData('userId')) {
                $user = \Monkey::app()->repoFactory->create('User')->findOneByStringId($userId);
                $user->userDetails;
                $user->userAddress;
                $user->newsletter;
                return json_encode($user->fullTreeToArray());
            } else {
                $list = [];
                foreach (\Monkey::app()->repoFactory->create('User')->findBy(['isDeleted'=>0],"","ORDER BY id desc") as $user) {
                    $list[] = ['id'=>$user->id,
                        'email'=>$user->email,
                        'name'=> is_null($user->userDetails) ? "" : $user->userDetails->name,
                        'surname'=> is_null($user->userDetails) ? "" : $user->userDetails->surname];
                }
                return json_encode($list);
            }

        } else {
            $this->app->router->response()->raiseUnauthorized();
            return 'Bad Request';
        }

	}
}