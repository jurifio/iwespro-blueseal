<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CGetPermissionsForUser
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CActivateUser extends AAjaxController
{
    public function put()
    {
	    $ids = $this->app->router->request()->getRequestData('users');
	    foreach($ids as $id) {
			$user = $this->app->repoFactory->create('User')->findOne([$id]);
		    $user->isActive = 1;
		    $user->update();
	    }
    }
}