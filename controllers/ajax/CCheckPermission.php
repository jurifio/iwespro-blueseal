<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CCheckPermission
 * @package redpanda\blueseal\controllers\ajax
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
            $id = $this->app->rbacManager->perms()->pathId($this->app->router->request()->getRequestData('permission'));
            return (bool) $this->app->getUser()->hasPermission($id);
        } catch (\Exception $e) {
            return false;
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