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
        $name = (array_key_exists('modelName', $get)) ? $get['modelName'] : false;
        $productPrototypeId = $get['productPrototypeId'];
        $productDetails = [];
        foreach ($get as $k => $v) {
            if (false !== strpos($k, 'productDetails')) {
                $productDetails[explode('_', $k)[1]] = $v;
            }
        }
        if ($name) {
            $prot = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['name' => $name]);
            if ($prot) {
                return json_encode(['status' => 'exists']);
            } else {
                try {
                    $this->app->dbAdapter->beginTransaction();
                    $newProt = $this->app->repoFactory->create('ProductSheetModelPrototype')->getEmptyEntity();
                    $newProt->productSheetPrototypeId = $productPrototypeId;
                    $newProt->name = $name;
                    $newProt->insert();

                    $newProt = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['name' => $name]);

                    $this->insertDetails($productDetails, $newProt->id, $newProt->productSheetPrototypeId);
                    $this->app->dbAdapter->commit();


                    return json_encode(['status' => 'new', 'productSheetModelPrototypeId' => $newProt->id]);
                } catch (\Exception $e) {
                    $this->app->dbAdapter->rollBack();
                    return json_encode(['status' => 'fail']);
                }
            }
        } else {
            throw new \Exception("OOPS! Nessun nome fornito");
        }
    }

    public function put()
    {
        $get = $this->app->router->request()->getRequestData();
        $id = $get['id'];
        $pspid = $get['Product_dataSheet'];
        $productDetails = [];
        foreach ($get as $k => $v) {
            if (false !== strpos($k, 'ProductDetail')) {
                if ($v) $productDetails[explode('_', $k)[1]] = $v;
            }
        }

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
            $this->app->dbAdapter->commit();
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            return json_encode(['status' => "ko"]);
        }

        $res = ['status' => 'ok', 'productSheetModelPrototypeId' => $prot->id];

        return json_encode($res);
    }

    private function insertDetails($productDetails, $productSheetModelPrototypeId, $productSheetPrototypeId)
    {
        foreach ($productDetails as $k => $v) {
            $newSheet = $this->app->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
            $newSheet->productSheetModelPrototypeId = $productSheetModelPrototypeId;
            $newSheet->productSheetPrototypeId = $productSheetPrototypeId;
            $newSheet->productDetailLabelId = $k;
            $newSheet->productDetailId = $v;
            $newSheet->insert();
        }
    }
}