<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CProducTag
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/04/2016
 * @since 1.0
 */
class CProductPriorityChangeController extends AAjaxController
{
    public function get()
    {
        $res = [];
        foreach (\Monkey::app()->repoFactory->create('SortingPriority')->findAll() as $item) {
            $res[] = ['id' => $item->id, 'name' => $item->priority];
        }
        return json_encode($res);
    }

    /**
     *
     */
    public function put()
    {
        $i = 0;
        $priorità = $this->app->router->request()->getRequestData('priority');
        foreach ($this->app->router->request()->getRequestData('products') as $row) {
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($row);
            $product->sortingPriorityId = $priorità;
            $i+=$product->update();
        }
        return $i;
    }
}