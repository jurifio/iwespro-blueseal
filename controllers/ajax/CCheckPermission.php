<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CCheckPermission
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CCheckPermission extends AAjaxController
{
    public function get()
    {
        try {
            return $this->app->getUser()->hasPermissions($this->app->router->request()->getRequestData('permission'));
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function put()
    {
        $this->get();
    }

    public function post()
    {
        $this->get();
    }

    public function delete()
    {
        $this->get();
    }
}