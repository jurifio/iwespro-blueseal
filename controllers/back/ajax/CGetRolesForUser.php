<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CGetRolesForUser
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
class CGetRolesForUser extends AAjaxController
{
    public function get()
    {
	    $id = $this->app->router->request()->getRequestData('id');
        return json_encode($this->app->rbacManager->roles()->getRolesForUser($id));
    }
}