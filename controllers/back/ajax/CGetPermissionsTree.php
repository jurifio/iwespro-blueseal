<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CGetPermissionsTree
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
class CGetPermissionsTree extends AAjaxController
{
    public function get()
    {
        return $this->app->rbacManager->perms()->treeToJson(1,'title');
    }
}