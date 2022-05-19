<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
        $code = (array_key_exists('code', $get)) ? $get['code'] : false;
        $search = (array_key_exists('search', $get)) ? $get['search'] : false;
        $modelId = (array_key_exists('modelId', $get)) ? $get['modelId'] : false;
        $categories = $this->app->router->request()->getRequestData('categories');
        $names = [];
        $namesCount = 0;

        if ($modelId) {
            $details = [];
            $modelRes = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $modelId]);
            $details['id'] = $modelRes->id;
            $details['prototype'] = $modelRes->productSheetPrototypeId;
            $modelDetails = $modelRes->productSheetModelActual;
            //$modelDetails = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->findBy(['productSheetModelPrototypeId' => $modelRes->id]);
            foreach ($modelDetails as $v) {
                $details[$v->productDetailLabelId] = [];
                $details[$v->productDetailLabelId]['labelName'] = \Monkey::app()->repoFactory->create('ProductDetailLabelTranslation')->findOneBy(['langId' => 1, 'productDetailLabelId' => $v->productDetailLabelId]);
                $details[$v->productDetailLabelId]['detailId'] = $v->productDetailId;
            }
            $res = $details;
        } else {
            if ($code) {
                //list($id, $variantId) = explode('-', $code);
                $product = \Monkey::app()->repoFactory->create('Product')->findByAnyString($code);
                foreach($product as $v) {
                    $names[$namesCount] = [];
                    $names[$namesCount]['id'] = $v->productSheetPrototypeId;
                    $names[$namesCount]['name'] = '';
                    $names[$namesCount]['origin'] = 'code';
                    $namesCount++;
                    break;
                }
            }

            if ($categories) {
                //$catIds = explode(',', $categories);
                $res = $this->app->dbAdapter->query(
                    'SELECT `ps`.* FROM `ProductSheetModelPrototype` as `ps` JOIN `ProductSheetModelPrototypeHasProductCategory` as `pc`  ON `ps`.id = `pc`.productSheetModelPrototypeId WHERE `pc`.productCategoryId IN (' . $categories .') group by `ps`.`id`',
                    []
                )->fetchAll();
                foreach($res as $k => $v) {
                    $names[$namesCount]['id'] = $v['id'];
                    $names[$namesCount]['name'] = $v['name'];
                    $names[$namesCount]['origin'] = 'code';
                    $namesCount++;
                }
            }
            if ($search) {
                $searchRes = $this->app->dbAdapter->query("SELECT * FROM ProductSheetModelPrototype WHERE `name` LIKE ?", ['%' . trim($search) . '%'])->fetchAll();
                foreach ($searchRes as $v) {
                    if (!in_array($v['name'], array_column($names, 'id'))) {
                        $names[$namesCount] = [];
                        $names[$namesCount]['id'] = $v['id'];
                        $names[$namesCount]['name'] = $v['name'];
                        $names[$namesCount]['origin'] = 'search';
                        $namesCount++;
                    }
                }
            }
            $res = $names;
        }
        return json_encode($res);
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
            $prot = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['name' => $name]);
            if ($prot) {
                return json_encode(['status' => 'exists']);
            } else {
                try {
                    \Monkey::app()->repoFactory->beginTransaction();
                    $newProt = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->getEmptyEntity();
                    $newProt->productSheetPrototypeId = $productPrototypeId;
                    $newProt->name = $name;
                    $newProt->insert();

                    $newProt = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['name' => $name]);

                    $this->insertDetails($productDetails, $newProt->id);
                    \Monkey::app()->repoFactory->commit();

                    return json_encode(['status' => 'new', 'productSheetModelPrototypeId' => $newProt->id]);
                } catch (\Throwable $e) {
                    \Monkey::app()->repoFactory->rollback();
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

        $prot = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['name' => $name]);

        try {
            \Monkey::app()->repoFactory->beginTransaction();
            //delete all details associated to this model
            $psma = $prot->productSheetModelActual;
            foreach ($psma as $v) {
                $v->delete();
            }
            //insert new details
            $this->insertDetails($productDetails, $prot->id);
            \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            return json_encode(['status' => "ko"]);
        }

        $res = ['status' => 'ok', 'productSheetModelPrototypeId' => $prot->id];

        return json_encode($res);
    }

    private function insertDetails($productDetails, $productPrototypeId)
    {
        foreach ($productDetails as $k => $v) {
            if (!$v) continue;
            $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
            $newSheet->productSheetModelPrototypeId = $productPrototypeId;
            $newSheet->productDetailLabelId = $k;
            $newSheet->productDetailId = $v;
            $newSheet->insert();
        }
    }
}