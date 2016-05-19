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
class CGetCategoryTree extends AAjaxController
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
}