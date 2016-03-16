<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\ecommerce\views\VBase;

/**
 * Class CGetAutocompleteSize
 * @package bamboo\blueseal\controllers\ajax
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGetDataSheet extends AAjaxController
{
    /**
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/parts/sheetDetails.php');

        $name = $this->app->router->request()->getRequestData('value');

	    $productSheetPrototype = $this->app->entityManagerFactory->create('ProductSheetPrototype')->findOne(['id'=>$name]);

        echo $view->render([
            'productSheetPrototype' =>$productSheetPrototype
        ]);
    }

    public function post()
    {
        return $this->get();
    }
}