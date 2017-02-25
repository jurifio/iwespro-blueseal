<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;

/**
 * Class CCheckProductsToBePublished
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
class CChangeProductsSeason extends AAjaxController
{

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();
        $act = $get['action'];
        if (array_key_exists('rows', $get)) $rows = $get['rows'];
        switch ($act) {
            case "updateSeason":
                if ($get['productSeasonId']) {
                    $count = 0;
                    $this->app->dbAdapter->beginTransaction();
                    try {
                        foreach ($rows as $k => $v) {
                            $product = $this->app->repoFactory->create('Product')->findOneBy(
                                [
                                    'id' => $v['id'],
                                    'productVariantId' => $v['productVariantId']
                                ]);
                            $product->productSeasonId = $get['productSeasonId'];
                            $count += $product->update();
                        }
                        $this->app->dbAdapter->commit();
                        return "Aggiornato lo stato di " . $count . " prodotti";
                    } catch (\Throwable $e) {
                        return "Errore nell'aggiornamento dello stato dei prodotti:<br />" .
                            $e->getMessage();
                            "Contattare l'amministratore<br />";
                        $this->app->dbAdapter->rollBack();
                    }

                }
                break;
        }
    }

    public function get()
    {   /** @var CObjectCollection $seasons */
        $seasons = $this->app->repoFactory->create('ProductSeason')->findBy(['isActive' => 1]);

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