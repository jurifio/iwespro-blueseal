<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CProductListAjaxController
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
class CDetailModelAssocToCat extends AAjaxController
{
    public function get()
    {
        $this->
    }

    public function post() {
        $get = $this->app->router->request()->getRequestData();
        $name = (array_key_exists('modelName', $get)) ? $get['modelName'] : false;
        if ($name) {

        }
        return $cache;
    }

    public function put() {
        //todo
    }

}