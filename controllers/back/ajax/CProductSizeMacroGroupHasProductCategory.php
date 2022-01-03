<?php

namespace bamboo\controllers\back\ajax;

/**
 * Class CProductSizeMacroGroupHasProductCategory
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/01/2022
 * @since 1.0
 */
class CProductSizeMacroGroupHasProductCategory extends AAjaxController
{
    public function get()
    {
        $cache = $this->app->cacheService->getCache("misc")->get("FullCategoryTreeAsJSON");
        if (!$cache) {
            $cache = $this->app->categoryManager->categories()->treeToJson(1);
            $this->app->cacheService->getCache("misc")->add("FullCategoryTreeAsJSON", $cache, 13000);
        }
        return $cache;
    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();
        $action = '';
        if (array_key_exists("action", $get)) $action = $get['action'];

        switch ($action) {
            case "updateCat":
                $rowsId = [];

                try {

                    foreach ($get['rows'] as $v) {
                        foreach ($get['newCategories'] as $c) {
                            $this->app->dbAdapter->insert(
                                "ProductSizeMacroGroupHasProductCategory",
                                [
                                    'productSizeMacroGroupId' => $v['id'],
                                    'productCategoryId' => $c
                                ]
                            );
                            if ($this->app->dbAdapter->countAffectedRows() != 1) throw new \Exception('No rows Updated');
                        }
                    }
                    \Monkey::app()->repoFactory->commit();
                } catch (\Throwable $e) {
                    \Monkey::app()->repoFactory->rollback();
                    return "OOPS! Errore nell'assegnazione delle categorie<br />" . $e->getMessage();
                }
                return "Le categorie sono state aggiornate!";
                break;
        }
    }
}