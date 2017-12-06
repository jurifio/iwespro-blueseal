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
class CProductHasProductCategory extends AAjaxController
{
    public function get()
    {
        $cache = $this->app->cacheService->getCache("misc")->get("FullCategoryTreeAsJSON");
        if (!$cache) {
            $cache = $this->app->categoryManager->categories()->treeToJson(1);
            $this->app->cacheService->getCache("misc")->add("FullCategoryTreeAsJSON",$cache,13000);
        }
        return $cache;
    }
    
    public function post() {
        $get = $this->app->router->request()->getRequestData();
        $action = '';
        if (array_key_exists("action", $get)) $action = $get['action'] ;

        switch($action) {
            case "updateCat":
                $rowsId = [];

                try {
                    \Monkey::app()->repoFactory->beginTransaction();
                    foreach($get['rows'] as $k => $v) {
                        $this->app->dbAdapter->delete(
                            "ProductHasProductCategory",
                            [
                                'productId' => $v['id'],
                                'productVariantId' => $v['productVariantId'],
                            ],
                            'AND',
                            true
                        );
                    }
                    foreach ($get['rows'] as $v) {
                        foreach($get['newCategories'] as $c) {
                            $this->app->dbAdapter->insert(
                                "ProductHasProductCategory",
                                [
                                    'productId' => $v['id'],
                                    'productVariantId' => $v['productVariantId'],
                                    'productCategoryId' => $c
                                ]
                            );
                            if($this->app->dbAdapter->countAffectedRows() != 1) throw new \Exception('No rows Updated');
                        }
                    }
                    \Monkey::app()->repoFactory->commit();
                } catch(\Throwable $e) {
                    \Monkey::app()->repoFactory->rollback();
                    return "OOPS! Errore nell'assegnazione delle categorie<br />" . $e->getMessage();
                }
                return "Le categorie sono state aggiornate!";
                break;
        }
    }
}