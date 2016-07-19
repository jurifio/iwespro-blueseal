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
class CDetailModelGetDetails extends AAjaxController
{
    public function get()
    {
        $get = $this->app->router->request()->getRequestData();
        $code = $get['code'];
        $search = (array_key_exists('search', $get)) ? $get['search'] : false;
        $names = [];
        $namesCount = 0;

        list($id, $variantId) = explode('-', $code);
        $cats = $this->app->repoFactory->create('Product')->findOneBy(['productVariantId' => $variantId])->productCategory;

        foreach($cats as $v) {
            $names[$namesCount] = [];
            $psmp = $this->app->repoFactory->create('ProductSheetModelPrototypeHasProductCategory')->findOneBy(['productCategoryId' => $v->id]);
            if ($psmp) {
                \BlueSeal::dump($psmp);
                $names[$namesCount]['id'] = $psmp->productSheetModelPrototypeId;
                $names[$namesCount]['name'] = $psmp->productSheetModelPrototype->name;
                $names[$namesCount]['origin'] = 'code';
                $namesCount++;
            }
        }

        if ($search) {
            $searchRes = $this->app->dbAdapter->query("SELECT * FROM ProductSheetModelPrototype WHERE `name` LIKE ?",['%' . trim($search) . '%'])->fetchAll();
            foreach($searchRes as $v) {
                if (!in_array($v['name'], array_column($names, 'id'))) {
                    $names[$namesCount] = [];
                    $names[$namesCount]['id'] = $v['id'];
                    $names[$namesCount]['name'] = $v['name'];
                    $names[$namesCount]['origin'] = 'search';
                    $namesCount++;
                }
            }
        }

        return json_encode($names);
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

                    $this->insertDetails($productDetails, $newProt->id);
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
        $name = (array_key_exists('modelName', $get)) ? $get['modelName'] : false;
        $productDetails = [];
        foreach ($get as $k => $v) {
            if (false !== strpos($k, 'productDetails')) {
                $productDetails[explode('_', $k)[1]] = $v;
            }
        }

        $prot = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['name' => $name]);

        try {
            $this->app->dbAdapter->beginTransaction();
            //delete all details associated to this model
            $psma = $prot->productSheetModelActual;
            foreach ($psma as $v) {
                $v->delete();
            }
            //insert new details
            $this->insertDetails($productDetails, $prot->id);
            $this->app->dbAdapter->commit();
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            return json_encode(['status' => "ko"]);
        }

        $res = ['status' => 'ok', 'productSheetModelPrototypeId' => $prot->id];

        return json_encode($res);
    }

    private function insertDetails($productDetails, $productPrototypeId)
    {
        foreach ($productDetails as $k => $v) {
            if (!$v) continue;
            $newSheet = $this->app->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
            $newSheet->productSheetModelPrototypeId = $productPrototypeId;
            $newSheet->productDetailLabelId = $k;
            $newSheet->productDetailId = $v;
            $newSheet->insert();
        }
    }
}