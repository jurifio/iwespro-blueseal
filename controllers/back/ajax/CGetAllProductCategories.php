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
class CGetAllProductCategories extends AAjaxController
{
    public function get()
    {
        $productCategories = \Monkey::app()->repoFactory->create('ProductCategory')->findAll();
        $categories = [];
        $idCat = 0;
        foreach($productCategories as $productCategory) {
            $categories[$idCat] = [];
            $categories[$idCat]['id'] = $productCategory->id;
            $categories[$idCat]['name'] = trim($this->app->categoryManager->categories()->getStringPath($productCategory->id," "));
            $idCat++;
        }
        return json_encode($categories);
    }
}