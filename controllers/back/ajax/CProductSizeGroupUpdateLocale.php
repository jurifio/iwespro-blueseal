<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProductSizeGroup;

/**
 * Class CProductSizeGroupUpdateLocale
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 06/01/2022
 * @since 1.0
 *
 */
class CProductSizeGroupUpdateLocale extends AAjaxController
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

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();


        try {
            $id= (int) $data['id'];

            $country = implode(',',$data['newCountry']);
            $category = implode(',',$data['newCategories']);
            $psg =\Monkey::app()->repoFactory->create('ProductSizeGroup')->findOneBy(['id' => $id]);
            $psg->country = $country;
            $psg->category = $category;
            $psg->update();


        } catch (\Throwable $e) {

            return "OOPS! Errore nella combinazione dei Gruppi taglia e dei Paesi<br />" . $e->getMessage();
        }
        return "I Gruppi Taglia  sono state aggiornati!";

    }
}