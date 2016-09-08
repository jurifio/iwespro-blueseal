<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\ecommerce\views\VBase;

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

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/blog_category.php');

        return $view->render([
            'storehouseOperation' => $storehouseOperation,
        ]);
    }
}