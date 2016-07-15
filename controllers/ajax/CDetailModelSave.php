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
class CDetailModelSave extends AAjaxController
{
    public function get()
    {

    }

    public function post() {
        $get = $this->app->router->request()->getRequestData();
        $name = (array_key_exists('modelName', $get)) ? $get['modelName'] : false;
        $return = 0;
        if ($name) {
            $prot = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['name' => $name]);
            if ($prot) {
                return 0;
            } else {
                $newProt = $this->app->repoFactory->create('ProductSheetModelPrototype')->getEmptyEntity();
                $newProt->pro
                return $newProt->id;
            }
        } else {
            throw new \Exception("OOPS! Nessun nome fornito");
        }
    }

    public function put() {
        //todo
    }

}