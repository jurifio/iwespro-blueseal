<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CChangeOrderStatus
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
class CStorehouseOperationDetails extends AAjaxController
{
    public function get() {
        $storehouseOperation = $this->app->repoFactory->create('StorehouseOperation')->findOneByStringId($this->app->router->request()->getRequestData('id'));

        $x = [];
        $x['notes'] = $storehouseOperation->notes;
        $x['user'] = $storehouseOperation->user->getFullName();
        $x['cause'] = $storehouseOperation->storehouseOperationCause->name;
        foreach ($storehouseOperation->storehouseOperationLine as $line) {
            $x['lines'][] = $line->productSku->printId();
        }
        return json_encode($x);
    }
}