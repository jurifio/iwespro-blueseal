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
class CDetailModel extends AAjaxController
{
    public function get() {
        $idModel = $this->app->router->request()->getRequestData('id');
        $idName = $this->app->router->request()->getRequestData('name');
        $idCode = $this->app->router->request()->getRequestData('code');

        $modelSheetRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype');
        if ($idModel) {
            $detailModel = $modelSheetRepo->findOneBy(['id' => $idModel]);
        } elseif ($idName) {
            $q = "SELECT id FROM ProductSheetModelPrototype WHERE `name` = ?";
            $detailModel = \Monkey::app()->dbAdapter->query($q, [$idName])->fetch();
            if ($detailModel) $detailModel = $modelSheetRepo->findOneBy(['id' => $detailModel['id']]);
        } elseif ($idCode) {
            $q = "SELECT id FROM ProductSheetModelPrototype WHERE `code` = ?";
            $detailModel = \Monkey::app()->dbAdapter->query($q, [$idCode])->fetch();
            if ($detailModel) $detailModel = $modelSheetRepo->findOneBy(['id' => $detailModel['id']]);

        }

        if (!$detailModel) return json_encode(false);
        $res = $detailModel->toArray();
        $res['categories'] = [];
        foreach($detailModel->productCategory as $cats) {
            $res['categories'][] = $cats->id;
        }


        if(!is_null($detailModel->genderId)){
            $res['genders'] = $detailModel->genderId;
        }

        if (!is_null($detailModel->categoryGroupId)){
            $res['prodCats'] = $detailModel->categoryGroupId;
        }

        if(!is_null($detailModel->materialId)){
            $res['materials'] = $detailModel->materialId;
        }


        return json_encode($res);
    }
}