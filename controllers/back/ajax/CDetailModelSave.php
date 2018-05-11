<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;

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
        // nothing to do here
    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();

        $productPrototypeId = $get['Product_dataSheet'];
        $productDetails = $this->getDetails($get);
        try {
            \Monkey::app()->repoFactory->beginTransaction();

            $pn = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findByName(trim($get['productName']));
            if (!$pn) throw new BambooException('Non si può creare un modello con un nome prodotto inesistente');
            $pnIt = $pn->findOneByKey('langId', 1);
            $newProt = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->getEmptyEntity();
            $newProt->productSheetPrototypeId = $productPrototypeId;
            $newProt->name = $get['name'];
            $newProt->code = $get['code'];
            $newProt->productName = str_replace(' !', '', $pnIt->name);
            $newProt->genderId = $get['genders'];
            $newProt->categoryGroupId =  $get['prodCats'];
            $newProt->materialId =  $get['materials'];
            $newProt->note =  $get['note'];
            $newId = $newProt->insert();

            $this->saveCats($get['categories'], $newId);

            $this->insertDetails($productDetails, $newId, $productPrototypeId);
            \Monkey::app()->repoFactory->commit();

            return json_encode(['status' => 'new', 'productSheetModelPrototypeId' => $newId]);
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            return json_encode(['status' => "ko", 'message' => $e->getMessage()]);
        }
    }

    public function put()
    {
        $get = $this->app->router->request()->getRequestData();
        $id = $get['id'];
        $pspid = $get['Product_dataSheet'];

        $productDetails = $this->getDetails($get);

        $prot = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $id]);

        try {
            \Monkey::app()->repoFactory->beginTransaction();

            //update model

            $pn = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findByName(trim($get['productName']));
            if (!$pn) throw new BambooException('Non si può aggiornare un modello con un nome prodotto inesistente');
            $pnIt = $pn->findOneByKey('langId', 1);
            $prot->name = $get['name'];
            if ($get['code']) {
                $prot->code = $get['code'];
            }
            if ($get['productName']) {
                $prot->productName = str_replace(' !', '', $pnIt->name);
            }

            if(isset($get['genders'])) $prot->genderId = $get['genders'];


            if(isset($get['prodCats'])) $prot->categoryGroupId = $get['prodCats'];

            if(isset($get['materials'])) $prot->materialId = $get['materials'];

            $prot->update();
            //delete all details associated to this model
            $psma = $prot->productSheetModelActual;
            foreach ($psma as $v) {
                $v->delete();
            }
            //insert new details
            $this->insertDetails($productDetails, $prot->id, $prot->productSheetPrototypeId);

            $this->saveCats($get['categories'], $prot->id);
            \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            return json_encode(['status' => "ko", 'message' => $e->getMessage()]);
        }
        $res = ['status' => 'ok', 'productSheetModelPrototypeId' => $prot->id];
        return json_encode($res);
    }

    /**
     * @param $productDetails
     * @param $productSheetModelPrototypeId
     * @param $productSheetPrototypeId
     */
    private function insertDetails($productDetails, $productSheetModelPrototypeId, $productSheetPrototypeId)
    {
        foreach ($productDetails as $k => $v) {
            $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
            $newSheet->productSheetModelPrototypeId = $productSheetModelPrototypeId;
            $newSheet->productDetailLabelId = $k;
            $newSheet->productDetailId = $v;
            $newSheet->insert();
        }
    }

    /**
     * @param $cats
     * @param $modelId
     * @throws \Exception
     */
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

    /**
     * @param $get
     */
    private function getDetails($get) {
        $productDetails = [];
        foreach ($get as $k => $v) {
            if (false !== strpos($k, 'ProductDetail')) {
                if ($v) $productDetails[explode('_', $k)[2]] = $v;
            }
        }
        return $productDetails;
    }
}