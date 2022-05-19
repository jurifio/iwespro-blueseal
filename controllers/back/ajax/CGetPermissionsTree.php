<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CGetPermissionsTree
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
class CGetPermissionsTree extends AAjaxController
{
    public function get()
    {
        return $this->app->rbacManager->perms()->treeToJson(1,'title');
    }
}