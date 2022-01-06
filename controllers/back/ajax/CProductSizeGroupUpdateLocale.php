<?php

namespace bamboo\controllers\back\ajax;

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
            $this->app->cacheService->getCache("misc")->add("FullCategoryTreeAsJSON", $cache, 13000);
        }
        return $cache;
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $action = '';
        if (array_key_exists("action", $data)) $action = $data['action'];
        $psgRepo=\Monkey::app()->repoFactory->create('ProductSizeGroup');
        switch ($action) {
            case "updateCat":


                try {


                       $psg=$psgRepo->findOneBy(['id'=>$data['id']]);
                       $psg->country=implode(',',$data['newCountry']);
                       $psg->category=implode(',',$data['newCategories']);
                       $psg-update();



                    \Monkey::app()->repoFactory->commit();
                } catch (\Throwable $e) {
                    \Monkey::app()->repoFactory->rollback();
                    return "OOPS! Errore nella combinazione dei Gruppi taglia e dei Paesi<br />" . $e->getMessage();
                }
                return "I Gruppi Taglia  sono state aggiornati!";
                break;
        }
    }
}