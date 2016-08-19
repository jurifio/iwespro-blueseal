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
class CDetailModel extends AAjaxController
{
    public function get() {
        $idModel = $this->app->router->request()->getRequestData('id');
        $idName = $this->app->router->request()->getRequestData('name');
        $idCode = $this->app->router->request()->getRequestData('code');

        $modelSheetRepo = $this->app->repoFactory->create('ProductSheetModelPrototype');
        if ($idModel) {
            $detailModel = $modelSheetRepo->findOneBy(['id' => $idModel]);
        } elseif ($idName) {
            $detailModel = $modelSheetRepo->findOneBy(['name' => $idName]);
        } elseif ($idCode) {
            $detailModel = $modelSheetRepo->findOneBy(['code' => $idCode]);
        }

        if (!$detailModel) return json_encode(false);
        $res = $detailModel->toArray();
        $res['categories'] = [];
        foreach($detailModel->productSheetModelPrototypeHasProductCategory as $cats) {
            $res['categories'][] = $cats->productCategoryId;
        }

        return json_encode($res);
    }
}