<?php

namespace bamboo\controllers\back\ajax;

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
class CProductSheetModelPrototypeHasProductCategoryManage extends AAjaxController
{

    public function post()
    {
        $ids = $this->app->router->request()->getRequestData('ids');
        $newCategories = $this->app->router->request()->getRequestData('newCategories');

        foreach ($ids as $modelId){
            $this->saveCats($newCategories, $modelId);
        }

        return "Le categorie sono state aggiornate!";


    }

    private function saveCats($cats, $modelId)
    {
        $em = $this->rfc('ProductSheetModelPrototypeHasProductCategory');
        if (!is_array($cats)) throw new \Exception('$cats must be an array');

        $catDel = $em->findBy(['productSheetModelPrototypeId' => $modelId]);
        foreach ($catDel as $v) {
            $v->delete();
        }

        foreach ($cats as $v) {
            $cat = $em->getEmptyEntity();
            $cat->productSheetModelPrototypeId = $modelId;
            $cat->productCategoryId = $v;
            $cat->insert();
        }
    }
}