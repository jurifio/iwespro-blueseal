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
        // nothing to do here
    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();

        $productPrototypeId = $get['Product_dataSheet'];
        $productDetails = $this->getDetails($get);
        try {
            $this->app->dbAdapter->beginTransaction();
            $newProt = $this->app->repoFactory->create('ProductSheetModelPrototype')->getEmptyEntity();
            $newProt->productSheetPrototypeId = $productPrototypeId;
            $newProt->name = $get['name'];
            $newProt->code = $get['code'];
            $newProt->productName = $get['productName'];
            $newId = $newProt->insert();

            $this->saveCats($get['categories'], $newId);

            $this->insertDetails($productDetails, $newId, $productPrototypeId);
            $this->app->dbAdapter->commit();

            return json_encode(['status' => 'new', 'productSheetModelPrototypeId' => $newId]);
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
            return json_encode(['status' => "ko", 'message' => $e->getMessage()]);
        }
    }

    public function put()
    {
        $get = $this->app->router->request()->getRequestData();
        $id = $get['id'];
        $pspid = $get['Product_dataSheet'];

        $productDetails = $this->getDetails($get);


        $prot = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $id]);

        try {
            $this->app->dbAdapter->beginTransaction();

            //update model
            $prot->name = $get['name'];
            if ($get['code']) {
                $prot->code = $get['code'];
            }
            if ($get['productName']) {
                $prot->productName = $get['productName'];
            }
            $prot->productSheetPrototypeId = $pspid;
            $prot->update();
            //delete all details associated to this model
            $psma = $prot->productSheetModelActual;
            foreach ($psma as $v) {
                $v->delete();
            }
            //insert new details
            $this->insertDetails($productDetails, $prot->id, $prot->productSheetPrototypeId);

            $this->saveCats($get['categories'], $prot->id);
            $this->app->dbAdapter->commit();
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
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
            $newSheet = $this->app->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
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