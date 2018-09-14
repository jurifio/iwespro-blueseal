<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;

/**
 * Class CGetProductCatsByAnyString
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/08/2018
 * @since 1.0
 */
class CGetProductCatsByAnyString extends AAjaxController
{
    public function get() {
        $search = $this->app->router->request()->getRequestData('search');

        $query = "SELECT psmpc.id
                  FROM ProductSheetModelPrototypeCategoryGroup psmpc 
                  WHERE psmpc.name like ?";
        $params = [];
        $params[] = '%'.$search.'%';

        $psmpc = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup')->findBySql($query, $params);
        $ret = [];
        foreach($psmpc as $v) {
          $ret[$v->id] = $v->name;
        }
        return json_encode($ret);
    }
}