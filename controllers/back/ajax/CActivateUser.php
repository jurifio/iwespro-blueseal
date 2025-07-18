<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CGetPermissionsForUser
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
			$user = \Monkey::app()->repoFactory->create('User')->findOne([$id]);
		    $user->isActive = 1;
		    $user->update();
	    }
    }
}