<?php
namespace bamboo\controllers\back\ajax;
use Throwable;

/**
 * Class CCheckPermission
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
        } catch (\Throwable $e) {
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