<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;

/**
 * Class CCheckProductsToBePublished
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
class CChangeProductsSeason extends AAjaxController
{

    public function post()
    {
        $prestashopHasProductRepo=\Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $get = $this->app->router->request()->getRequestData();
        $act = $get['action'];
        if (array_key_exists('rows', $get)) $rows = $get['rows'];
        switch ($act) {
            case "updateSeason":
                if ($get['productSeasonId']) {
                    $count = 0;
                    \Monkey::app()->repoFactory->beginTransaction();
                    try {
                        foreach ($rows as $k => $v) {
                            $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(
                                [
                                    'id' => $v['id'],
                                    'productVariantId' => $v['productVariantId']
                                ]);



                            $product->productSeasonId = $get['productSeasonId'];

                            $count += $product->update();

                            $prestashopHasProduct=$prestashopHasProductRepo->findOneBy(
                                [
                                    'productId' => $v['id'],
                                    'productVariantId' => $v['productVariantId']
                                ]);
                            if($prestashopHasProduct!==null){
                            $prestashopHasProduct->status=2;
                            $prestashopHasProduct->update();
                                }
                        }
                        \Monkey::app()->repoFactory->commit();
                        return "Aggiornato lo stato di " . $count . " prodotti";
                    } catch (\Throwable $e) {
                        return "Errore nell'aggiornamento dello stato dei prodotti:<br />" .
                            $e->getMessage();
                            "Contattare l'amministratore<br />";
                        \Monkey::app()->repoFactory->rollback();
                    }

                }
                break;
        }
    }

    public function get()
    {   /** @var CObjectCollection $seasons */
        $seasons = \Monkey::app()->repoFactory->create('ProductSeason')->findBy(['isActive' => 1]);

        $expSeasons = [];
        $i = 0;
        foreach($seasons as $s) {
            $expSeasons[$i]['id'] = $s->id;
            $expSeasons[$i]['name'] = $s->name . " " . $s->year;
            $expSeasons[$i]['isActive'] = $s->isActive;
            $i++;
        };

        return json_encode($expSeasons);
    }

    public function delete()
    {
        $this->get();
    }
}