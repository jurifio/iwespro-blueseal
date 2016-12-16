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
        $get = $this->app->router->request()->getRequestData();
        $productSheetModelPrototypeId = $this->app->router->request()->getRequestData('productSheetModelPrototypeId');
        $code = $this->app->router->request()->getRequestData('code');
        $idModel = $this->app->router->request()->getRequestData('idModel');
        list($id, $variantId) = explode('-', $code);
        $search = (array_key_exists('search', $get)) ? $get['search'] : false;
        unset($get);
        $cats = [];
        $countCats = 0;

        if ($code) {
            $resByCode = $this->app->repoFactory->create('Product')->findOneBy(['productVariantId' => $variantId])->productCategory;
            foreach ($resByCode as $k => $v) {
                $cats[$countCats] = [];
                $cats[$countCats]['id'] = $v->id;
                $cats[$countCats]['slug'] = $v->slug;
                $cats[$countCats]['name'] = $this->app->repoFactory->create('ProductCategoryTranslation')->findOneBy(['langId' => 1, 'productCategoryId' => $v->id])->name;
                $cats[$countCats]['path'] = $this->getCategoryTree($this->app->categoryManager->categories()->getPath($v->id));
                $cats[$countCats]['origin'] = 'code';
                $countCats++;
            }
            unset($resByCode);
        }

        if ($productSheetModelPrototypeId) {
            $resByModelName = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $productSheetModelPrototypeId]);
            foreach ($resByModelName as $v) {
                if (!in_array($v->productCategory->id, array_column($cats, 'id'))) {
                    $cats[$countCats] = [];
                    $cats[$countCats]['id'] = $v->productCategory->id;
                    $cats[$countCats]['slug'] = $v->productCategory->slug;
                    $cats[$countCats]['name'] = $this->app->repoFactory->create('productCategoryTranslation')->findOneBy(
                        ['langId' => 1, 'productCategoryId' => $v->productCategory->id]
                    );
                    $cats[$countCats]['path'] = $this->getCategoryTree($this->app->categoryManager->categories()->getPath($v->productCategory->id));
                    $cats[$countCats]['origin'] = 'model';
                    $countCats++;
                }
            }
            unset($resByModelName);
        }

        if ($idModel) {
            $resByModelName = $this->app->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $productSheetModelPrototypeId]);
            foreach ($resByModelName as $v) {
                if (!in_array($v->productCategory->id, array_column($cats, 'id'))) {
                    $cats[$countCats] = [];
                    $cats[$countCats]['id'] = $v->productCategory->id;
                    $cats[$countCats]['slug'] = $v->productCategory->slug;
                    $cats[$countCats]['name'] = $this->app->repoFactory->create('productCategoryTranslation')->findOneBy(
                        ['langId' => 1, 'productCategoryId' => $v->productCategory->id]
                    );
                    $cats[$countCats]['path'] = $this->getCategoryTree($this->app->categoryManager->categories()->getPath($v->productCategory->id));
                    $cats[$countCats]['origin'] = 'model';
                    $countCats++;
                }
            }
            unset($resByModelName);
        }

        if ($search) {
            $resBySearch = $this->app->dbAdapter->query("SELECT * FROM `ProductCategoryTranslation` WHERE `langId` = 1 AND `name` LIKE ? LIMIT 30", ['%' . $search . '%'])->fetchAll();
            foreach ($resBySearch as $v) {
                if (!in_array($v['productCategoryId'], array_column($cats, 'id'))) {
                    $cats[$countCats] = [];
                    $cats[$countCats]['id'] = $v['productCategoryId'];
                    $cats[$countCats]['name'] = $v['name'];
                    $cats[$countCats]['path'] = $this->getCategoryTree($this->app->categoryManager->categories()->getPath($v['productCategoryId']));
                    $cats[$countCats]['slug'] = $this->app->repoFactory->create('ProductCategory')->findOneBy(['id' => $v['productCategoryId']])->slug;
                    $cats[$countCats]['origin'] = 'search';
                    $countCats++;
                }
            }
        }
        return json_encode($cats);
    }

    public function post() {
        $get = $this->app->router->request()->getRequestData();
        $productSheetModelPrototypeId = $get['productSheetModelPrototypeId'];
        $categoryId = $get['categoryId'];

        try {
            $cat = $this->app->repoFactory->create('ProductSheetModelPrototypeHasProductCategory')->findOneBy(['productCategoryId' => $categoryId]);
            if ($cat) {
                $ent = $this->app->repoFactory->create('ProductSheetModelPrototypeHasProductCategory')->findOneBy(['productSheetModelPrototypeId' => $productSheetModelPrototypeId]);
                if ($ent) {
                    $ent->delete();
                }
                $cat->productSheetModelPrototypeId = $productSheetModelPrototypeId;
                $cat->update();
            } else {
                $cat = $this->app->repoFactory->create('ProductSheetModelPrototypeHasProductCategory')->getEmptyEntity();
                $cat->productCategoryId = $categoryId;
                $cat->productSheetModelPrototypeId = $productSheetModelPrototypeId;
                $cat->insert();
            }
        } catch(\Throwable $e) {
            return "OOPS! Non sono riuscito ad aggiornare la categoria!<br />" . $e->getMessage();
        }
        return "Categoria Aggiornata!"; // todo all
    }

    public function put() {
        //todo
    }


    public function getCategoryTree($arr) {
        $names = [];
        foreach($arr as $v) {
            if (1 == $v['id']) continue;
            $pct = $this->app->repoFactory->create('ProductCategoryTranslation')->findOneBy(['productCategoryId' => $v['id'], 'langId' => 1]);
            $names[] = $pct->name;
        }
        return implode('/', $names);
    }

}