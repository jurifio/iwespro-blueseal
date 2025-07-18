<?php
namespace bamboo\controllers\back\ajax;
use bamboo\ecommerce\views\VBase;

/**
 * Class CChangeOrderStatus
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
class CStorehouseOperationDetails extends AAjaxController
{
    public function get() {
        $storehouseOperation = \Monkey::app()->repoFactory->create('StorehouseOperation')->findOneByStringId($this->app->router->request()->getRequestData('id'));

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/storage_operation_detail.php');

        return $view->render([
            'storehouseOperation' => $storehouseOperation,
        ]);
    }
}